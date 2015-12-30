<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace SchoolManagement\Controller;

use Database\Service\EntityManagerService;
use Exception;
use Recruitment\Entity\Recruitment;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

/**
 * Description of EnrollmentController
 *
 * @author marcio
 */
class EnrollmentController extends AbstractActionController
{

    use EntityManagerService;

    public function indexAction()
    {

        try {

            $em = $this->getEntityManager();
            $recruitment = $em->getRepository('Recruitment\Entity\Recruitment')->findOneBy(
                    array('recruitmentType' => Recruitment::STUDENT_RECRUITMENT_TYPE), array('recruitmentId' => 'DESC')
            );


            $this->layout()->toolbar = array(
                'menu' => array(
                    array(
                        'url' => '/recruitment/registration/studentProfile/$id',
                        'title' => 'Perfil do Candidato',
                        'description' => 'Analizar Perfil do Candidato',
                        'class' => 'fa fa-file-text-o bg-blue',
                        'target' => '_blank',
                        'fntype' => 'selectedHttpClick',
                    ),
                ),
            );


            return new ViewModel(array(
                'message' => null,
                'recruitment' => $recruitment,
            ));
        } catch (Exception $ex) {
            return new ViewModel(array(
                'message' => 'Erro inesperado. Por favor entre em contato'
                . ' com o administrador do sistema: ' . $ex->getMessage(),
                'recruitment' => null,
            ));
        }
    }

}
