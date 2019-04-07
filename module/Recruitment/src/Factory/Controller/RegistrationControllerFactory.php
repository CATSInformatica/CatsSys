<?php

/*
 * Copyright (C) 2016 Márcio Dias <marciojr91@gmail.com>
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

namespace Recruitment\Factory\Controller;

use Recruitment\Controller\RegistrationController;
use Zend\ServiceManager\Factory\FactoryInterface;
use Interop\Container\ContainerInterface;
use Authentication\Factory\Controller\Helper\CreateEmailSenderService;
use Doctrine\ORM\EntityManager;

/**
 * Cria uma instância do controller RegistrationController e injeta o EntityManager e o serviço de emails.
 *
 * @author Márcio Dias <marciojr91@gmail.com>
 */
class RegistrationControllerFactory implements FactoryInterface
{
    use CreateEmailSenderService;

    public function createService(ContainerInterface $container)
    {
        $config = $container->get('config');
        $emailOptions = $config['email']['recruitment'];
        $mailgunOptions = $config['mailgun'];
        $emailService = $this->createEmailSenderService($mailgunOptions);

        $emailService->setFrom($emailOptions['from']['email'], $emailOptions['from']['name']);
        $emailService->setReplyTo($emailOptions['replyTo']['email'], $emailOptions['replyTo']['name']);

        $viewRenderer = $container->get('ViewRenderer');
        $controller = new RegistrationController($emailService, $viewRenderer);
        $em = $container->get(EntityManager::class);

        $controller->setEntityManager($em);

        return $controller;
    }
}
