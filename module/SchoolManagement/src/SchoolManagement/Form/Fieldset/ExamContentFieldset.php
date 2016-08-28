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

namespace SchoolManagement\Form\Fieldset;

use Doctrine\Common\Persistence\ObjectManager;
use DoctrineModule\Stdlib\Hydrator\DoctrineObject as DoctrineHydrator;
use Zend\Form\Fieldset;
use Zend\InputFilter\InputFilterProviderInterface;
use SchoolManagement\Entity\ExamContent;

/**
 * Description of ExamContentFieldset
 *
 * @author Gabriel Pereira <rickardch@gmail.com>
 */
class ExamContentFieldset extends Fieldset implements InputFilterProviderInterface
{

    public function __construct(ObjectManager $obj)
    {
        parent::__construct('exam-content-fieldset');

        $this->setHydrator(new DoctrineHydrator($obj))
            ->setObject(new ExamContent());

        $this->add(array(
            'name' => 'description',
            'type' => 'textarea',
            'attributes' => array(
                'rows' => 5,
            ),
            'options' => array(
                'label' => 'Descrição do conteúdo',
            ),
        ));
    }

    public function getInputFilterSpecification()
    {
        return array(
            'description' => array(
                'required' => false,
                'filters' => array(
                    array('name' => 'StringTrim'),
                    array('name' => 'StripTags'),
                ),
                'validators' => array(
                    array(
                        'name' => 'StringLength',
                        'options' => array(
                            'max' => 200,
                            'inclusive' => true,
                        ),
                    ),
                ),
            ),
        );
    }

}
