<?php

/*
 * Copyright (C) 2016 Márcio Dias <marciojr91@gmail.com>
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

namespace AdministrativeStructure\Controller;

use AdministrativeStructure\Entity\Job;
use AdministrativeStructure\Entity\Office;
use AdministrativeStructure\Form\JobForm;
use Authentication\Entity\User;
use Authentication\Service\UserService;
use Database\Controller\AbstractEntityActionController;
use Doctrine\Common\Collections\ArrayCollection;
use Exception;
use Recruitment\Entity\Person;
use Recruitment\Entity\Recruitment;
use Recruitment\Entity\RecruitmentStatus;
use Recruitment\Form\SearchRegistrationsForm;
use Zend\View\Model\JsonModel;
use Zend\View\Model\ViewModel;

/**
 * Description of JobController
 *
 * @author Márcio Dias <marciojr91@gmail.com>
 */
class JobController extends AbstractEntityActionController
{

    /**
     * Exibe os cargos criados, o cargo superior e cargos subordinados.
     * 
     * @return ViewModel
     */
    public function indexAction()
    {

        $em = $this->getEntityManager();

        $jobs = $em->getRepository('AdministrativeStructure\Entity\Job')->findAll();

        return new ViewModel([
            'jobs' => $jobs,
        ]);
    }

    /**
     * Cria um cargo (ou posto de trabalho).
     * 
     * A criação envolve a adição de quais funções (papéis) o cargo possuirá, a criação de um papel para o cargo e
     * quem será o cargo superior que herdará (pode ser nenhum) o papel do cargo em criação.
     * 
     * 
     * @return ViewModel
     */
    public function createAction()
    {
        $request = $this->getRequest();
        $em = $this->getEntityManager();

        $job = new Job();
        $form = new JobForm($em);
        $form->bind($job);

        if ($request->isPost()) {
            $data = $request->getPost();
            $form->setData($data);
            if ($form->isValid()) {
                try {

                    $roles = new ArrayCollection();
                    if (isset($data['roles'])) {
                        foreach ($data['roles'] as $roleIdx) {
                            $roles->add($em->getReference('Authorization\Entity\Role', $roleIdx));
                        }
                    }
                    $job->setParentRoles($roles);
                    $em->persist($job);
                    $em->flush();
                    return $this->redirect()->toRoute('administrative-structure/job', ['action' => 'index']);
                } catch (\Exception $ex) {
                    return new ViewModel([
                        'form' => $form,
                        'message' => $ex->getMessage(),
                    ]);
                }
            }
        }

        return new ViewModel([
            'form' => $form,
            'message' => null,
        ]);
    }

    /**
     * Permite editar cargos cadastrados.
     * 
     * A edição é um pouco complexa, pois envove, além do nome do cargo, a alteração dos papéis que o cargo possui e,
     * em caso de mudança do cargo superior ($parent !== null), envolve a remoção do papel herado pelo cargo superior.
     * 
     * @return ViewModel
     */
    public function editAction()
    {
        $id = $this->params('id', false);

        if ($id) {
            $request = $this->getRequest();
            $em = $this->getEntityManager();

            $job = $em->find('AdministrativeStructure\Entity\Job', $id);

            $form = new JobForm($em, $id);
            $form->bind($job);

            if ($request->isPost()) {
                $data = $request->getPost();
                $form->setData($data);
                if ($form->isValid()) {
                    $roles = new ArrayCollection();
                    if (isset($data['roles'])) {
                        foreach ($data['roles'] as $roleIdx) {
                            $roles->add($em->getReference('Authorization\Entity\Role', $roleIdx));
                        }
                    }

                    $job->addParentRoles($roles);
                    $job->setLastRevisionDate(new \DateTime());
                    $parent = $job->getParentBuffer();

                    if ($parent !== null) {
                        $em->persist($parent);
                    }

                    $em->persist($job);
                    $em->flush();

                    return $this->redirect()->toRoute('administrative-structure/job', ['action' => 'index']);
                }
            }

            return new ViewModel([
                'message' => null,
                'form' => $form,
            ]);
        }


        return new ViewModel([
            'message' => 'Nenhum cargo selecionado',
            'form' => null,
        ]);
    }

    /**
     * Remove o cargo $id.
     * 
     * Se o cargo possui filhos, eles são repassados para o cargo superior ou então tornam-se orfãos
     * 
     * @return JsonModel
     */
    public function deleteAction()
    {
        $id = $this->params('id', false);

        if ($id) {

            try {
                $em = $this->getEntityManager();
                $job = $em->find('AdministrativeStructure\Entity\Job', $id);

                if ($job !== null) {
                    $parent = $job->getParent();
                    $children = $job->getChildren();

                    // retira, de cada cargo filho, a referência do cargo que será removido
                    foreach ($children as $child) {
                        $child->removeParent();
                    }

                    // se existe um cargo pai, remove a referência do filho e de seu papel e
                    // adiciona os filhos do cargo que será removido
                    if ($parent !== null) {
                        $job->removeParent();
                        $parent->removeChildren(new ArrayCollection([$job]));
                        $parent->addChildren($children);

                        foreach ($children as $child) {
                            $child->addNewParent($parent);
                            $em->merge($child);
                        }
                        $em->merge($parent);
                    } else {
                        foreach ($children as $child) {
                            $em->merge($child);
                        }
                    }
                    $em->remove($job);
                    $em->remove($job->getRole());
                    $em->flush();
                }

                return new JsonModel([
                    'message' => 'Cargo removido com sucesso',
                ]);
            } catch (Exception2 $ex) {
                return new JsonModel([
                    'message' => $ex->getMessage(),
                ]);
            }
        }

        return new JsonModel([
            'message' => 'Nenhum cargo foi especificado',
        ]);
    }

    /**
     * Exibe os cargos e os voluntários para que seja possível realizar as associações ou removê-las.
     * 
     * @return ViewModel
     */
    public function officeManagerAction()
    {
        try {
            $em = $this->getEntityManager();
            $form = new SearchRegistrationsForm($em, Recruitment::VOLUNTEER_RECRUITMENT_TYPE);

            $form
                ->get('registrationStatus')
                ->setValue(RecruitmentStatus::STATUSTYPE_VOLUNTEER)
                ->setAttribute('disabled', 'disabled');

            return new ViewModel(array(
                'message' => null,
                'form' => $form,
            ));
        } catch (Exception $ex) {
            return new ViewModel(array(
                'message' => 'Erro inesperado. Por favor entre em contato com o administrador do sistema.',
                'form' => null,
            ));
        }
    }

    /**
     * Permite atribuir cargos a voluntários
     * 
     *  - Ao criar um Office deve-se verificar
     *      - Pessoa possui usuário?
     *          - Sim: Adicionar o papel correspondente ao cargo
     *          - Não: Criar um usuário e adicionar o papel correspondente ao cargo
     * 
     */
    public function addOfficeAction()
    {
        $id = $this->params('id', false);
        $request = $this->getRequest();
        $data = $request->getPost();

        if ($id && $data['jobs']) {

            $em = $this->getEntityManager();

            try {

                $registration = $em->find('Recruitment\Entity\Registration', $id);
                $user = $this->getUserIfExistsCreateIfNotExists($registration->getPerson());


                foreach ($data['jobs'] as $jobId) {

                    $office = $em->getRepository('AdministrativeStructure\Entity\Office')->findOneBy([
                        'registration' => $id,
                        'job' => $jobId,
                        'end' => null,
                    ]);

                    // se o voluntário não possui o cargo
                    if ($office === null) {
                        $job = $em->find('AdministrativeStructure\Entity\Job', $jobId);

                        // adiciona os papéis dos cargos escolhidos ao vetor de papeis do usuário associado a 
                        // $registration
                        $user->addRole($job->getRole());

                        // cria uma nova associação do voluntário com o cargo
                        $office = new Office();
                        $office
                            ->setRegistration($registration)
                            ->setJob($job);

                        // salva o cargo
                        $em->persist($office);
                        // salva o usuário
                        $em->persist($user);
                    }
                }

                $em->flush();

                return new JsonModel([
                    'message' => 'Cargo(s) adicionado(s) com sucesso',
                ]);
            } catch (\Exception $ex) {
                return new JsonModel([
                    'message' => $ex->getMessage(),
                ]);
            }
        }

        return new JsonModel([
            'message' => 'Nenhum voluntário ou cargo selecionado',
        ]);
    }

    /**
     * Permite retirar cargos de voluntários.
     * 
     * Deve ser utilizada apenas em casos em que adição do cargo foi efeita indevidamente.
     * 
     * @todo
     *  - Remove um Office
     *  - Ao remover deve-se retirar o papel correspondente do usuário
     */
    public function removeOfficeAction()
    {
        $id = $this->params('id', false);
        $request = $this->getRequest();
        $data = $request->getPost();

        if ($id && $data['jobs']) {

            $em = $this->getEntityManager();

            try {

                $registration = $em->find('Recruitment\Entity\Registration', $id);
                $user = $this->getUserIfExistsCreateIfNotExists($registration->getPerson());

                foreach ($data['jobs'] as $jobId) {

                    $office = $em->getRepository('AdministrativeStructure\Entity\Office')->findOneBy([
                        'registration' => $id,
                        'job' => $jobId,
                        'end' => null,
                    ]);

                    // se o voluntário possui o cargo
                    if ($office !== null) {

                        // retira o papel associado ao cargo
                        $role = $office->getJob()->getRole();
                        $user->removeRole($role);

                        $em->remove($office);
                        $em->merge($user);
                    }
                }

                $em->flush();

                return new JsonModel([
                    'message' => 'Cargo(s) removido(s) com sucesso',
                ]);
            } catch (\Exception $ex) {
                return new JsonModel([
                    'message' => $ex->getMessage(),
                ]);
            }
        }

        return new JsonModel([
            'message' => 'Nenhum voluntário ou cargo selecionado',
        ]);
    }

    /**
     * Permite finalizar cargos de voluntários.
     * 
     * Deve ser utilizada sempre que um voluntário deixar de possuir um cargo
     * 
     * @todo
     * - Adiciona data de saída no Office correspondente
     * - Retira o papel correspondente do usuário
     */
    public function endOfficeAction()
    {
        $id = $this->params('id', false);
        $request = $this->getRequest();
        $data = $request->getPost();

        if ($id && $data['jobs']) {

            $em = $this->getEntityManager();

            try {

                $registration = $em->find('Recruitment\Entity\Registration', $id);
                $user = $this->getUserIfExistsCreateIfNotExists($registration->getPerson());

                foreach ($data['jobs'] as $jobId) {

                    $office = $em->getRepository('AdministrativeStructure\Entity\Office')->findOneBy([
                        'registration' => $id,
                        'job' => $jobId,
                        'end' => null,
                    ]);

                    // se o voluntário possui o cargo
                    if ($office !== null) {

                        // retira o papel associado ao cargo
                        $user->removeRole($office->getJob()->getRole());

                        $office->setEnd(new \DateTime());
                        $em->merge($office);
                        $em->merge($user);
                    }
                }

                $em->flush();

                return new JsonModel([
                    'message' => 'Cargo(s) finalizado(s) com sucesso',
                ]);
            } catch (\Exception $ex) {
                return new JsonModel([
                    'message' => $ex->getMessage(),
                ]);
            }
        }

        return new JsonModel([
            'message' => 'Nenhum voluntário ou cargo selecionado',
        ]);
    }

    /**
     * Encontra todos os cargos e organiza-os em forma de uma floresta.
     */
    public function getJobsAction()
    {

        $em = $this->getEntityManager();

        $jobs = $em->getRepository('AdministrativeStructure\Entity\Job')->findBy([
            'parent' => null,
        ]);

        $jobsArr = [];

        // Floresta de cargos
        foreach ($jobs as $job) {
            $jobsArr[] = $this->depthFirstSearch($job);
        }

        return new JsonModel([
            'jobs' => $jobsArr,
        ]);
    }

    /**
     * Busca em profundidade.
     * 
     * Monta a árvore de cargos
     * 
     * @param type $jobModel Cargo pai
     * @return array Árvore de cargos
     */
    protected function depthFirstSearch(Job $jobModel)
    {

        $childrenArr = [];

        $jobArr = [
            'id' => $jobModel->getJobId(),
            'name' => $jobModel->getJobName(),
            'department' => $jobModel->getDepartment()->getDepartmentName(),
            'children' => &$childrenArr,
        ];

        $children = $jobModel->getChildren()->toArray();

        foreach ($children as $child) {
            $childrenArr[] = $this->depthFirstSearch($child);
        }

        return $jobArr;
    }

    /**
     * Busca pelo usuário da inscrição $id. Caso não exista cria um novo.
     * 
     * @param Person $person pessoa associada à inscrição
     * @return User
     */
    protected function getUserIfExistsCreateIfNotExists(Person $person)
    {

        $em = $this->getEntityManager();

        // se não possui usuário
        if ($person->getUser() === null) {

            $user = new User();
            $userName = $person->getPersonEmail();
            $userPassword = preg_replace('/[.,-]/', '', $person->getPersonCpf());
            $pass = UserService::encryptPassword($userPassword);

            $user
                ->setUserName($userName)
                ->setUserPassword($pass['password'])
                ->setUserPasswordSalt($pass['password_salt'])
                ->setUserActive(true);

            $person->setUser($user);

            $em->merge($person);

            return $user;
        }

        return $person->getUser();
    }

    /**
     * Exibe a hierarquia de Cargos
     */
    public function hierarchyAction()
    {
        return new ViewModel();
    }

}
