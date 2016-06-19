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

namespace Recruitment\Controller;

use Database\Controller\AbstractEntityActionController;
use DoctrineModule\Stdlib\Hydrator\DoctrineObject as DoctrineHydrator;
use Exception;
use Recruitment\Entity\Recruitment;
use Recruitment\Entity\RecruitmentStatus;
use Recruitment\Form\PreInterviewForm;
use Recruitment\Form\VolunteerInterviewForm;
use Recruitment\Service\AddressService;
use Recruitment\Service\RegistrationStatusService;
use Recruitment\Service\RelativeService;
use RuntimeException;
use Zend\View\Model\JsonModel;
use Zend\View\Model\ViewModel;

/**
 * Manipula informações de candidatos do psa e psv.
 *
 * @author Márcio Dias <marciojr91@gmail.com>
 */
class InterviewController extends AbstractEntityActionController
{

    use RelativeService,
        AddressService,
        RegistrationStatusService;

    /**
     * Página de alunos que devem preencher ou já preencheram o formulário
     * de pré-entrevista.
     * 
     * @return ViewModel
     */
    public function studentListAction()
    {

        $em = $this->getEntityManager();
        $recruitment = $em->getRepository('Recruitment\Entity\Recruitment')
            ->findLastClosed(Recruitment::STUDENT_RECRUITMENT_TYPE);

        $candidates = [];

        if (isset($recruitment['recruitmentId'])) {
            $calledForPreInterview = $em
                ->getRepository('Recruitment\Entity\Registration')
                ->findByStatusSimplified($recruitment['recruitmentId'], RecruitmentStatus::STATUSTYPE_CALLEDFOR_PREINTERVIEW);

            $preInterviewCompleted = $em
                ->getRepository('Recruitment\Entity\Registration')
                ->findByStatusSimplified($recruitment['recruitmentId'], RecruitmentStatus::STATUSTYPE_PREINTERVIEW_COMPLETE);

            $candidates = array_merge($calledForPreInterview, $preInterviewCompleted);
        }

        return new ViewModel([
            'recruitment' => $recruitment,
            'candidates' => $candidates,
        ]);
    }

    /**
     * ???
     * 
     * @return ViewModel
     */
    public function studentAction()
    {
        $id = $this->params('id', false);
        $request = $this->getRequest();
        if ($id) {
            try {
                $em = $this->getEntityManager();
                $registration = $em->find('Recruitment\Entity\Registration', $id);
                $person = $registration->getPerson();

                $form = new PreInterviewForm($em, array(
                    'person' => array(
                        'relative' => $person->isPersonUnderage(),
                        'relative' => false,
                        'address' => true,
                        'social_media' => true,
                    ),
                    'pre_interview' => $registration->getPreInterview() !== null,
                ));

                $form->bind($registration);

                if ($request->isPost()) {
                    $form->setData($request->getPost());

                    if ($form->isValid()) {
                        $em->merge($registration);
                        $em->flush();
                    }
                }

                return new ViewModel(array(
                    'message' => '',
                    'registration' => $registration,
                    'form' => $form,
                ));
            } catch (Exception $ex) {
                return new ViewModel(array(
                    'message' => 'Não foi possível encontrar o registro do candidato: ' . $ex->getMessage(),
                    'registration' => null
                ));
            }
        }

        return new ViewModel(array(
            'message' => 'nenhum candidato foi especificado.',
            'registration' => null
        ));
    }

    /**
     * Entrevista para voluntários
     * 
     * @return ViewModel
     * @throws RuntimeException
     */
    public function volunteerAction()
    {
        $id = $this->params('id', false);
        $request = $this->getRequest();
        if ($id) {
            try {
                $em = $this->getEntityManager();
                $registration = $em->find('Recruitment\Entity\Registration', $id);

                $form = new VolunteerInterviewForm($em, array(
                    'interview' => true,
                    'person' => array(
                        'relative' => false,
                        'address' => true,
                        'social_media' => true,
                    ),
                ));

                $form->bind($registration);

                if ($request->isPost()) {

                    $currentStatusType = (int) $registration
                            ->getCurrentRegistrationStatus()
                            ->getRecruitmentStatus()
                            ->getNumericStatusType();

                    if ($currentStatusType == RecruitmentStatus::STATUSTYPE_REGISTERED) {
                        throw new RuntimeException('Este candidato ainda não foi convocado para entrevista '
                        . 'ou aula teste');
                    }

                    $form->setData($request->getPost());

//                    echo $currentStatusType . ' ' . RecruitmentStatus::STATUSTYPE_CALLEDFOR_INTERVIEW;
//                    exit;

                    if ($form->isValid()) {

                        if ($currentStatusType === RecruitmentStatus::STATUSTYPE_CALLEDFOR_INTERVIEW) {
                            $this->updateRegistrationStatus($registration, RecruitmentStatus::STATUSTYPE_INTERVIEWED);
                        } else if ($currentStatusType === RecruitmentStatus::STATUSTYPE_CALLEDFOR_TESTCLASS) {
                            $this->updateRegistrationStatus($registration, RecruitmentStatus::STATUSTYPE_TESTCLASS_COMPLETE);
                        }

                        $em->merge($registration);
                        $em->flush();
                    }
                }

                return new ViewModel(array(
                    'message' => '',
                    'registration' => $registration,
                    'form' => $form,
                ));
            } catch (Exception $ex) {
                return new ViewModel(array(
                    'message' => 'Erro: ' . $ex->getMessage(),
                    'registration' => null
                ));
            }
        }

        return new ViewModel(array(
            'message' => 'nenhum candidato foi especificado.',
            'registration' => null
        ));
    }

    /**
     * Formulário de entrevista para candidatos ao processo seletivo de alunos.
     * 
     * @return ViewModel
     */
    public function studentFormAction()
    {
        return new ViewModel([]);
    }

    /**
     * Retorna informações do candidato ao processo seletivo de alunos. 
     * 
     * @return \Recruitment\Controller\JsonModel
     */
    public function getStudentInfoAction()
    {

        $registrationId = $this->params('id', false);

        if ($registrationId) {

            try {

                $em = $this->getEntityManager();
                $hydrator = new DoctrineHydrator($em);

                $registration = $em->find('Recruitment\Entity\Registration', $registrationId);

                // informações de inscrição
                $data = $hydrator->extract($registration);
                $data['registrationNumber'] = $registration->getRegistrationNumber();

                // informações pessoais
                $person = $registration->getPerson();
                $data['person'] = $hydrator->extract($person);

                // informações de endereço
                $data['person']['addresses'] = [];
                $addresses = $person->getAddresses();

                foreach ($addresses as $addr) {
                    $data['person']['addresses'][] = $hydrator->extract($addr);
                }

                // informações de parentes (para menores de idade)
                // pega apenas o primeiro
                $relatives = $person->getRelatives();
                $data['person']['relatives'] = [];
                if (count($relatives) > 0) {
                    $data['person']['relatives'][] = $hydrator->extract($relatives[0]);
                }

                //informações da pré-entrevista
                $preInterview = $registration->getPreInterview();
                if ($preInterview !== null) {
                    $data['preInterview'] = $hydrator->extract($preInterview);
                }

                //informações da entrevista
                $studentInterview = $registration->getStudentInterview();
                if ($studentInterview !== null) {
                    $data['studentInterview'] = $hydrator->extract($studentInterview);
                }

                return new JsonModel([
                    'info' => $data,
                ]);
            } catch (\Throwable $ex) {
                return new JsonModel([
                    'message' => $ex->getMessage(),
                ]);
            }
        }

        return new JsonModel([]);
    }
}
