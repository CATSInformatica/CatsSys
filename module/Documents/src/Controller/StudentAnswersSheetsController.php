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

namespace Documents\Controller;

use Database\Controller\AbstractEntityActionController;
use Zend\View\Model\ViewModel;

/**
 * Description of StudentAnswersSheetsController
 *
 * @author Márcio Dias <marciojr91@gmail.com>
 */
class StudentAnswersSheetsController extends AbstractEntityActionController
{

    public function indexAction()
    {
        try {

            $em = $this->getEntityManager();

            $classes = $em->getRepository('SchoolManagement\Entity\StudentClass')
                ->findByEndDateGratherThan(new \DateTime('now'));

            return new ViewModel([
                'classes' => $classes,
                'message' => null,
            ]);
        } catch (Exception $ex) {
            return new ViewModel([
                'message' => $ex->getMessage(),
                'classes' => null,
            ]);
        }
    }

}
