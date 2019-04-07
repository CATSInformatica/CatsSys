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
use Zend\ServiceManager\Factory\FactoryInterface;
use Interop\Container\ContainerInterface;
use Authentication\Factory\Controller\Helper\CreateEmailSenderService;

/**
 * Description of IndexControllerFactory
 *
 * @author Gabriel Pereira <rickardch@gmail.com>
 */
class IndexControllerFactory implements FactoryInterface
{
    use CreateEmailSenderService;

    public function createService(ContainerInterface $container)
    {
        $emailOptions = $container->get('config')['email']['contact'];
        $mailgunOptions = $container->get('config')['mailgun'];
        $emailService = $this->createEmailSenderService($mailgunOptions);

        $emailService->setTo($emailOptions['to']['email'], $emailOptions['to']['name']);
        $emailService->setFrom($emailOptions['from']['email'], $emailOptions['from']['name']);

        $controller = new IndexController($emailService);

        $em = $container->get('Doctrine\ORM\EntityManager');
        $controller->setEntityManager($em);

        return $controller;
    }

}
