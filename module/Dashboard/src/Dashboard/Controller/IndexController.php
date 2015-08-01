<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Dashboard\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Database\Provider\ProvidesEntityManager;
/**
 * Description of IndexController
 *
 * @author marcio
 */
class IndexController extends AbstractActionController
{

    use ProvidesEntityManager; // doctrine entity manager

    public function indexAction()
    {
        
        $message = 'Welcome to Dashboard.';
        
        return new ViewModel(array(
            'message' => $message
        ));
    }

}
