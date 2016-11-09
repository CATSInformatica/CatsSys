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

namespace Documents\Form\Fieldset;

use Doctrine\Common\Persistence\ObjectManager;
use DoctrineModule\Stdlib\Hydrator\DoctrineObject as DoctrineHydrator;
use Zend\Form\Fieldset;
use Zend\InputFilter\InputFilterProviderInterface;
use Documents\Entity\StudentBgConfig;

/**
 * Description of StudentBgConfigFieldset
 *
 * @author Gabriel Pereira <rickardch@gmail.com>
 */
class StudentBgConfigFieldset extends Fieldset implements InputFilterProviderInterface
{
    
    public function __construct(ObjectManager $obj)
    {
        parent::__construct('student_bg_config_fieldset');

        $this->setHydrator(new DoctrineHydrator($obj))
                ->setObject(new StudentBgConfig());

        $this
                ->add(array(
                    'name' => 'studentBgConfigPhrase',
                    'type' => 'textarea',
                    'options' => array(
                        'label' => 'Frase',
                        'add-on-prepend' => '<i class="fa fa-paragraph"></i>',
                    ),
                    'attributes' => array(
                        'rows' => 2,
                    )
                ))
                ->add(array(
                    'name' => 'studentBgConfigAuthor',
                    'type' => 'text',
                    'options' => array(
                        'label' => 'Autor',
                        'add-on-prepend' => '<i class="fa fa-quote-left"></i>',
                    ),
                ))
        ;
    }

    public function getInputFilterSpecification()
    {
        return array(
            'studentBgConfigPhrase' => array(
                'required' => true,
                'filters' => array(
                    array('name' => 'StringTrim'),
                    array('name' => 'StripTags'),
                ),
                'validators' => array(
                    array(
                        'name' => 'StringLength',
                        'options' => array(
                            'min' => 3,
                            'max' => 150,
                            'encoding' => 'UTF-8',
                        ),
                    ),
                ),
            ),
            'studentBgConfigAuthor' => array(
                'required' => true,
                'filters' => array(
                    array('name' => 'StringTrim'),
                    array('name' => 'StripTags'),
                ),
                'validators' => array(
                    array(
                        'name' => 'StringLength',
                        'options' => array(
                            'min' => 3,
                            'max' => 40,
                            'encoding' => 'UTF-8',
                        ),
                    ),
                ),
            ),
        );
    }
    
}
