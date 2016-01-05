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

/**
 * Description of NavigationFactory
 *
 * @author Márcio Dias <marciojr91@gmail.com>
 */
class NavigationViewFactory implements FactoryInterface
{

    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $session = new Container('User');
        $role = $session->activeRole;
        $acl = $serviceLocator->getServiceLocator()->get('acl');
        $navigation = $serviceLocator->get('Zend\View\Helper\Navigation');
        $navigation->setAcl($acl)->setRole($role);

        return $navigation;
    }

}
