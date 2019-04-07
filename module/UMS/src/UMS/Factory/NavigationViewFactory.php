<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace UMS\Factory;

use Zend\ServiceManager\FactoryInterface;
use Interop\Container\ContainerInterface;
use Zend\Session\Container;
use Zend\View\Helper\Navigation as NavigationHelper;

class NavigationViewFactory implements FactoryInterface
{

    public function createService(ContainerInterface $container)
    {
        $session = new Container('User');
        $role = $session->activeRole;
        $acl = $container->getServiceLocator()->get('acl');
        $navigation = $this->createHelper($container);
        $navigation->setAcl($acl)->setRole($role);

        return $navigation;
    }

    private function createHelper(ContainerInterface $container)
    {
        $helper = new NavigationHelper();
        $helper->setServiceLocator($container);
        return $helper;
    }

}
