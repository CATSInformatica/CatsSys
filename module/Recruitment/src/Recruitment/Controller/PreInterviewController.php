<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Recruitment\Controller;

use Database\Service\EntityManagerService;
use DateTime;
use Exception;
use Recruitment\Form\CpfFilter;
use Recruitment\Form\CpfForm;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\Session\Container;
use Zend\View\Model\ViewModel;

/**
 * Description of PreInterviewController
 *
 * @author Márcio Dias <marciojr91@gmail.com>
 */
class PreInterviewController extends AbstractActionController
{

    use EntityManagerService;

    public function indexAction()
    {
        $request = $this->getRequest();
        $form = new CpfForm();

        if ($request->isPost()) {
            $data = $request->getPost();
            $form->setInputFilter(new CpfFilter());
            $form->setData($data);

            if ($form->isValid()) {
                $data = $form->getData();

                try {

                    $em = $this->getEntityManager();

                    $registration = $em->getRepository('Recruitment\Entity\Registration')
                        ->findOneByPersonCpf($data['cpf']);

                    if ($registration !== null) {
                        if ($registration->getRegistrationConvocationDate() instanceof DateTime) {

                            $studentContainer = new Container('pre_interview');
                            $studentContainer->offsetSet('regId', $registration->getRegistrationId());

                            return $this->redirect()->toRoute('recruitment/pre-interview',
                                    array(
                                    'action' => 'studentPreInterview'
                            ));
                        }

                        $message = 'Candidato não convocado';
                    } else {
                        $message = 'Candidato não encontrado.';
                    }
                } catch (Exception $ex) {
                    $message = 'Erro inesperado, não foi possível encontrar uma inscrição associada a este cpf.'
                        . $ex->getMessage();
                }
            } else {
                $message = '';
            }
        } else {
            $message = null;
        }

        return new ViewModel(array(
            'message' => $message,
            'form' => $form,
        ));
    }

    public function studentPreInterviewAction()
    {
        $studentContainer = new Container('pre_interview');

        if ($studentContainer->offsetExists('regId')) {
            try {

                $em = $this->getEntityManager();

                $registration = $em->getRepository('Recruitment\Entity\Registration')->findOneBy(array(
                    'registrationId' => $studentContainer->offsetGet('regId')
                ));
            } catch (Exception $ex) {
                $registration = null;
            }
        } else {
            $registration = null;
        }

        return new ViewModel(array(
            'registration' => $registration
        ));
    }

}
