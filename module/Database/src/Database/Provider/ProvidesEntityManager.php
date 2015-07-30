<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Database\Provider;

/**
 * Description of ProvidesEntityManager
 *
 * @author marcio
 */
trait ProvidesEntityManager
{
    /**
     *
     * @var Doctrine\ORM\EntityManager
     */
    protected $entityManager;

    protected function getEntityManager()
    {
        if (null == $this->entityManager) {
            $this->entityManager = $this
                    ->getServiceLocator()
                    ->get('Doctrine\ORM\EntityManager');
        }
        
        return $this->entityManager;
    }
}
