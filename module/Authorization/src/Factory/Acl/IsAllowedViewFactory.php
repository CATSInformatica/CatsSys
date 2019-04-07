<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Authorization\Factory\Acl;

use Zend\ServiceManager\Factory\FactoryInterface;
use Interop\Container\ContainerInterface;
use Authorization\View\Helper\IsAllowed as IsAllowedViewHelper;

/**
 * Description of IsAllowedViewFactory
 *
 * @author marcio
 */
class IsAllowedViewFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $auth = $container->get('doctrine.authenticationservice.orm_default');
        $acl = $container->get('acl');
        $helper = new IsAllowedViewHelper($auth, $acl);
        return $helper;
    }
}
