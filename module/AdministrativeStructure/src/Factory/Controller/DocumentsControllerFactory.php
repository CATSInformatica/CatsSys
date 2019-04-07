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

namespace AdministrativeStructure\Factory\Controller;

use AdministrativeStructure\Controller\DocumentsController;
use Zend\ServiceManager\FactoryInterface;
use Interop\Container\ContainerInterface;

/**
 * Cria uma instância de DocumentsController e injeta o EntityManager
 *
 * @author Márcio Dias <marciojr91@gmail.com>
 */
class DocumentsControllerFactory implements FactoryInterface
{

    public function createService(ContainerInterface $container)
    {
        $sm = $container->getServiceLocator();
        $controller = new DocumentsController();
        $controller->setEntityManager($sm->get('Doctrine\ORM\EntityManager'));
        return $controller;
    }

}
