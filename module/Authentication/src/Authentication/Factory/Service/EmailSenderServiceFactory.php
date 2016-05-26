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

namespace Authentication\Factory\Service;

use Authentication\Service\EmailSenderService;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Cria uma instância do EmailSenderService e injeta a configuração.
 *
 * @author Márcio Dias <marciojr91@gmail.com>
 */
class EmailSenderServiceFactory implements FactoryInterface
{

    /**
     * Configura o serviço de email.
     * 
     * @param ServiceLocatorInterface $serviceLocator
     * @return EmailSenderService
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {

        $emailConfig = $serviceLocator->get('config')['email_config'];

        $emailService = new EmailSenderService();
        $emailService
            ->setConfig($emailConfig['smtp_options'])
            ->setFrom($emailConfig['from_recruitment'], $emailConfig['from_recruitment_name']);

        return $emailService;
    }

}
