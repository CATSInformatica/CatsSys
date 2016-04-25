<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace UMS\Factory;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\Session\Container;
use Zend\View\Helper\Navigation as NavigationHelper;

class NavigationViewFactory implements FactoryInterface
{

    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $session = new Container('User');
        $role = $session->activeRole;
        $acl = $serviceLocator->getServiceLocator()->get('acl');
        $navigation = $this->createHelper($serviceLocator);
        $navigation->setAcl($acl)->setRole($role);

        return $navigation;
    }

    private function createHelper(ServiceLocatorInterface $serviceLocator)
    {
        $helper = new NavigationHelper();
        $helper->setServiceLocator($serviceLocator);
        return $helper;
    }

}
