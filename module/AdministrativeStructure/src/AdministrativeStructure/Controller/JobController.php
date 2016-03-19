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
use AdministrativeStructure\Form\JobForm;
use Database\Controller\AbstractEntityActionController;
use Doctrine\Common\Collections\ArrayCollection;
use Zend\View\Model\JsonModel;
use Zend\View\Model\ViewModel;

/**
 * Description of JobController
 *
 * @author Márcio Dias <marciojr91@gmail.com>
 */
class JobController extends AbstractEntityActionController
{

    public function indexAction()
    {

        $em = $this->getEntityManager();

        $jobs = $em->getRepository('AdministrativeStructure\Entity\Job')->findAll();

        return new ViewModel([
            'jobs' => $jobs,
        ]);
    }

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
                $roles = new ArrayCollection();
                if (isset($data['roles'])) {
                    foreach ($data['roles'] as $roleIdx) {
                        $roles->add($em->getReference('Authorization\Entity\Role', $roleIdx));
                    }
                }
                $job->setParentRoles($roles);
                $em->persist($job);
                $em->flush();
            }
        }

        return new ViewModel([
            'form' => $form,
        ]);
    }

    public function editAction()
    {
        $id = $this->params('id', false);

        if ($id) {
            $request = $this->getRequest();
            $em = $this->getEntityManager();

            $job = $em->find('AdministrativeStructure\Entity\Job', $id);
            $form = new JobForm($em);
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
            } catch (\Exception $ex) {
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
     * Permite atribuir cargos à voluntários
     * 
     * @todo
     *  - Criar entidade JobTitle
     *      - Data de Entrada
     *      - Data de Saída
     *      - Registration
     *      - Job
     * 
     *  - Ao criar um jobTitle deve-se verificar
     *      - Pessoa possui usuário?
     *          - Sim: Adicionar o papel correspondente ao cargo
     *          - Não: Criar um usuário e adicionar o papel correspondente ao cargo
     * 
     */
    public function assignJobTitleAction()
    {
        
    }

    /**
     * Permite retirar cargos de voluntários
     * 
     * @todo
     *  - Remove um jobTitle
     *  - Ao remover deve-se retirar o papel correspondente do usuário
     */
    public function removeJobTitleAction()
    {
        
    }

    /**
     * Permite finalizar cargos de voluntários
     * 
     * @todo
     * - Adiciona data de saída no JobTitle correspondente
     * - Retira o papel correspondente do usuário
     */
    public function closeJobTitleAction()
    {
        
    }

}
