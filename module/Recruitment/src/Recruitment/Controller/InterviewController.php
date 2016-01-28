<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Recruitment\Controller;

use Database\Service\EntityManagerService;
use Exception;
use Recruitment\Entity\RecruitmentStatus;
use Recruitment\Form\PreInterviewForm;
use Recruitment\Form\VolunteerInterviewForm;
use Recruitment\Service\AddressService;
use Recruitment\Service\RelativeService;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

/**
 * Description of InterviewController
 *
 * @author Márcio Dias <marciojr91@gmail.com>
 */
class InterviewController extends AbstractActionController
{

    use EntityManagerService,
        RelativeService,
        AddressService;

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
                        'relative' => $person->isPersonUnderage(),
                        'address' => true,
                        'social_media' => false,
                    ),
                    'pre_interview' => true,
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

                    $currentStatusType = $registration
                        ->getCurrentRegistrationStatus()
                        ->getRecruitmentStatus()
                        ->getNumericStatusType();

                    if (!in_array($currentStatusType,
                            array(
                            RecruitmentStatus::STATUSTYPE_CALLEDFOR_INTERVIEW,
                            RecruitmentStatus::STATUSTYPE_CALLEDFOR_TESTCLASS,
                        ))) {
                        throw new \RuntimeException('Candidato não foi convocado para entrevista ou aula teste');
                    }
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
