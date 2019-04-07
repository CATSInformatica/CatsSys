<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace UMS\Factory;

use Zend\ServiceManager\Factory\FactoryInterface;
use Interop\Container\ContainerInterface;
use Zend\Session\Container as SessionContainer;
use Zend\View\Helper\Navigation as NavigationHelper;

class NavigationViewFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $session = new SessionContainer('User');
        $role = $session->activeRole;
        $acl = $container->get('acl');
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
