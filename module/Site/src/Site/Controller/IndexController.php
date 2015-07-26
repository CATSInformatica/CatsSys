<?php

namespace Site\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Auth\Entity\User;
use Zend\Authentication\AuthenticationService;

/*
 * traits
 */
use Auth\Provider\ProvidesEntityManager;

class IndexController extends AbstractActionController
{

    use ProvidesEntityManager;

    public function indexAction()
    {
        $auth = new AuthenticationService();

//        echo '<h1>User Has Identity: ' . $auth->hasIdentity() . '</h1>';

        $entityManager = $this->getEntityManager();
        $user = new User();
        $users = $entityManager->getRepository('Auth\Entity\User')
                ->findAll();

        $user->setUsrName('Fulano muito louco')
                ->setUsrEmail('email@email.com')
                ->setUsrPasswordSalt('987654321')
                ->setUsrPassword('123456789')
                ->setUsrActive(false);

//        echo '<pre>';
//        print_r($user);
//        echo '</pre>';

//        echo '<pre>';
//        print_r($users);
//        echo '</pre>';

        return new ViewModel();
    }

}
