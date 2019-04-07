<?php

/*
 * Copyright (C) 2016 Gabriel Pereira <rickardch@gmail.com>
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

namespace SchoolManagement\Form;

use Doctrine\Common\Persistence\ObjectManager;
use DoctrineModule\Stdlib\Hydrator\DoctrineObject as DoctrineHydrator;
use SchoolManagement\Form\Fieldset\ExamFieldset;
use Zend\Form\Form;

/**
 * Description of ExamForm
 *
 * @author Gabriel Pereira <rickardch@gmail.com>
 */
class ExamForm extends Form
{
    /**
     *
     * @param ObjectManager $obj
     */
    public function __construct(ObjectManager $obj)
    {
        parent::__construct('exam-form');
        $this->setHydrator(new DoctrineHydrator($obj));
        $examFieldset = new ExamFieldset($obj);
        $examFieldset->setUseAsBaseFieldset(true);
        $this->add($examFieldset);

        $this
            ->add(array(
                'name' => 'submit',
                'type' => 'submit',
                'attributes' => array(
                    'class' => 'btn btn-primary btn-block',
                    'value' => 'Criar Prova',
                )
            ))
        ;
    }
}
