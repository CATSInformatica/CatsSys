<?php

/*
 * Copyright (C) 2017 Gabriel Pereira <rickardch@gmail.com>
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

namespace Site\Factory\Controller;

use Authentication\Service\EmailSenderService;
use Site\Controller\IndexController;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Description of IndexControllerFactory
 *
 * @author Gabriel Pereira <rickardch@gmail.com>
 */
class IndexControllerFactory implements FactoryInterface
{

    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $sl = $serviceLocator->getServiceLocator();
        
        $emailOptions = $sl->get('config')['email']['contact'];
        $emailService = new EmailSenderService();
        
        foreach ($emailOptions['to'] as $email => $name) {
            $emailService->addTo($email, $name);
        }   
        
        $emailService->setFrom($emailOptions['from'], $emailOptions['from_name']);
        
        $controller = new IndexController($emailService);
        
        $em = $sl->get('Doctrine\ORM\EntityManager');
        $controller->setEntityManager($em);

        return $controller;
    }

}
