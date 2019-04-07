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

namespace Database\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Database\Accessor\DbalConnectionAccessor;
use Database\Accessor\EntityManagerAccessor;

/**
 * Permite a utilização de métodos de acesso para o EntityManager e DbalConnection
 *
 * @author Márcio Dias <marciojr91@gmail.com>
 */
abstract class AbstractDbalAndEntityActionController extends AbstractActionController
{

    use DbalConnectionAccessor;
    use EntityManagerAccessor;

}
