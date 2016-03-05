<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Database\Factory\Cache;

use Doctrine\Common\Cache\ApcCache;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Description of ApcCache
 *
 * @author Márcio Dias <marciojr91@gmail.com>
 */
class ApcCacheFactory implements FactoryInterface
{

    /**
     * Doctrine ApcCache
     * 
     * @param ServiceLocatorInterface $serviceLocator
     * @return ApcCache
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        return new ApcCache();
    }
    
}
