<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Authorization\Factory\Acl;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Authorization\Acl\AclDb;

/**
 * Description of AclDbFactory
 *
 * @author marcio
 */
class AclDbFactory implements FactoryInterface
{

    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        return new AclDb($serviceLocator->get('Doctrine\ORM\EntityManager'));
    }

}
