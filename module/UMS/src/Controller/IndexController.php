<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace UMS\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
/**
 * Description of IndexController
 *
 * @author marcio
 */
class IndexController extends AbstractActionController
{
    public function indexAction()
    {
        $message = 'Welcome to UMS.';

        return new ViewModel(array(
            'message' => $message
        ));
    }

}
