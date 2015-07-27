<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Dashboard\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Auth\Provider\ProvidesEntityManager;

/**
 * Description of IndexController
 *
 * @author marcio
 */
class IndexController extends AbstractActionController
{

    use ProvidesEntityManager; // doctrine entity manager
//    protected function createUser()
//    {
//
//        $em = $this->getEntityManager();
//
//        $user = new User();
//        $user->setUsrName("manuel@hotmail.com")
//                ->setUsrEmail("manuel@hotmail.com")
//                ->setUsrPasswordSalt(
//                        substr(str_shuffle("!@#$%*()_+{}:|0123456789ab"
//                                        . "cdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNO"
//                                        . "PQRSTUVWXYZ"), 0, 39))
//                ->setUsrPassword('123456789')
//                ->setUsrActive(true);
//
//        $em->persist($user);
//        $em->flush();
//
//        var_dump($user->getUsrId());
//    }

    public function indexAction()
    {
        $auth = $this->getServiceLocator()
                ->get('Zend\Authentication\AuthenticationService');
        if ($auth->hasIdentity()) {

            $em = $this->getEntityManager();
            $users = $em->getRepository('Auth\Entity\User')
                    ->findAll();
            $message = $this->params()
                    ->fromQuery('message', 'foo');
            
        }
        return new ViewModel(array(
            'message' => $message,
            'users' => $users,
        ));
    }

}
