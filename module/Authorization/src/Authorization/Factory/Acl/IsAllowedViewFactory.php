<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Authorization\Factory\Acl;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Authorization\View\Helper\IsAllowed as IsAllowedViewHelper;

/**
 * Description of IsAllowedViewFactory
 *
 * @author marcio
 */
class IsAllowedViewFactory implements FactoryInterface
{

    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $auth = $serviceLocator->get('Zend\Authentication\AuthenticationService');
        $acl = $serviceLocator->get('acl');
        $helper = new IsAllowedViewHelper($auth, $acl);
        return $helper;
    }
    
}
