<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Authorization\Factory\Acl;

use Zend\ServiceManager\Factory\FactoryInterface;
use Interop\Container\ContainerInterface;
use Authorization\Controller\Plugin\IsAllowed as IsAllowedControllerPlugin;
use Zend\Authentication\AuthenticationService;

/**
 * Description of IsAllowedControllerFactory
 *
 * @author marcio
 */
class IsAllowedControllerFactory implements FactoryInterface
{

    public function createService(ContainerInterface $container)
    {
        $auth = $container->get(AuthenticationService::class);
        $acl = $container->get('acl');
        $plugin = new IsAllowedControllerPlugin($auth, $acl);
        return $plugin;
    }
}
