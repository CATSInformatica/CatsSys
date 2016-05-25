<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Recruitment\Controller;

use Database\Controller\AbstractEntityActionController;
use Exception;
use Recruitment\Entity\RecruitmentStatus;
use Recruitment\Form\PreInterviewForm;
use Recruitment\Form\VolunteerInterviewForm;
use Recruitment\Service\AddressService;
use Recruitment\Service\RegistrationStatusService;
use Recruitment\Service\RelativeService;
use RuntimeException;
use Zend\View\Model\ViewModel;

/**
 * Description of InterviewController
 *
 * @author Márcio Dias <marciojr91@gmail.com>
 */
class InterviewController extends AbstractEntityActionController
{

    use RelativeService,
        AddressService,
        RegistrationStatusService;

    public function studentAction()
    {
        $id = $this->params('id', false);
        $request = $this->getRequest();
        if ($id) {
            try {
                $em = $this->getEntityManager();
                $registration = $em->find('Recruitment\Entity\Registration', $id);
                $person = $registration->getPerson();

                $form = new PreInterviewForm($em,
                    array(
                    'person' => array(
//                        'relative' => $person->isPersonUnderage(),
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

    public function volunteerAction()
    {
        $id = $this->params('id', false);
        $request = $this->getRequest();
        if ($id) {
            try {
                $em = $this->getEntityManager();
                $registration = $em->find('Recruitment\Entity\Registration', $id);

                $form = new VolunteerInterviewForm($em,
                    array(
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
                            $this->updateRegistrationStatus($registration,
                                RecruitmentStatus::STATUSTYPE_TESTCLASS_COMPLETE);
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

}
