<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Recruitment\Controller;

use Recruitment\Form\StudentRegistrationForm;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

/**
 * Description of RegistrationController
 *
 * @author marcio
 */
class RegistrationController extends AbstractActionController
{

    public function indexAction()
    {
        return new ViewModel();
    }

    public function studentRegistrationAction()
    {
        $request = $this->getRequest();
        $form = new StudentRegistrationForm($request->getBaseUrl() . '/recruitment/captcha/generate', 'Inscrição');
        if ($request->isPost()) {
            $data = $request->getPost();
            $form->setData($data);
            
            if($form->isValid()) {
                
            }
            
        }

        return new ViewModel(array(
            'form' => $form,
        ));
    }

}
