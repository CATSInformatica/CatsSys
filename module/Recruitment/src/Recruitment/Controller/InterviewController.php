<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Recruitment\Controller;

use Database\Service\EntityManagerService;
use Exception;
use Recruitment\Form\PreInterviewForm;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

/**
 * Description of InterviewController
 *
 * @author Márcio Dias <marciojr91@gmail.com>
 */
class InterviewController extends AbstractActionController
{

    use EntityManagerService;

    public function studentAction()
    {
        $id = $this->params('id', false);

        if ($id) {
            try {
                $em = $this->getEntityManager();
                $registration = $em->find('Recruitment\Entity\Registration', $id);

                $form = new PreInterviewForm($em);
                $form->bind($registration);

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

}
