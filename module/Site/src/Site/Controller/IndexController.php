<?php

namespace Site\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
//use Database\Entity\User;
//use Zend\Authentication\AuthenticationService;

/*
 * traits
 */
//use Database\Provider\ProvidesEntityManager;

class IndexController extends AbstractActionController
{

//    use ProvidesEntityManager;

    public function indexAction()
    {
        return new ViewModel();
    }

}
