<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Authorization\Factory\Acl;

use Zend\ServiceManager\FactoryInterface;
use Interop\Container\ContainerInterface;
use Authorization\Acl\AclDb;

/**
 * Description of AclDbFactory
 *
 * @author Márcio Dias <marciojr91@gmail.com>
 */
class AclDbFactory implements FactoryInterface
{

    public function createService(ContainerInterface $container)
    {
        return new AclDb($container->get('Doctrine\ORM\EntityManager'));
    }

}
