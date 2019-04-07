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

namespace SchoolManagement\Factory\Controller;

use SchoolManagement\Controller\SchoolSubjectController;
use Zend\ServiceManager\FactoryInterface;
use Interop\Container\ContainerInterface;

/**
 * Instancia o controller SchoolSubjectController e injeta o EntityManager
 *
 * @author Márcio Dias <marciojr91@gmail.com>
 */
class SchoolSubjectControllerFactory implements FactoryInterface
{

    public function createService(ContainerInterface $container)
    {

        $controller = new SchoolSubjectController();
        $sl = $container->getServiceLocator();
        $em = $sl->get('Doctrine\ORM\EntityManager');
        $controller->setEntityManager($em);
        return $controller;
    }

}
