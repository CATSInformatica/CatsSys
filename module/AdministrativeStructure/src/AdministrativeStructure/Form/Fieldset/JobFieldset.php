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

namespace AdministrativeStructure\Form\Fieldset;

use AdministrativeStructure\Entity\Job;
use Doctrine\Common\Persistence\ObjectManager;
use DoctrineModule\Stdlib\Hydrator\DoctrineObject as DoctrineHydrator;
use Zend\Form\Fieldset;
use Zend\InputFilter\InputFilterProviderInterface;

/**
 * Description of JobFieldset
 *
 * @author Márcio Dias <marciojr91@gmail.com>
 */
class JobFieldset extends Fieldset implements InputFilterProviderInterface
{

    public function __construct(ObjectManager $obj, $disabledJobId = null, $name = null, $options = array())
    {
        parent::__construct($name, $options);

        $this
            ->setHydrator(new DoctrineHydrator($obj))
            ->setObject(new Job());

        $this
            ->add([
                'name' => 'jobName',
                'type' => 'text',
                'attributes' => [
                    'placeholder' => 'Diretor de Informática',
                ],
                'options' => [
                    'label' => 'Cargo',
                ],
            ])
            ->add([
                'name' => 'department',
                'type' => 'DoctrineModule\Form\Element\ObjectSelect',
                'options' => [
                    'label' => 'Departamento',
                    'object_manager' => $obj,
                    'target_class' => 'AdministrativeStructure\Entity\Department',
                    'property' => 'departmentName',
                    'display_empty_item' => true,
                    'empty_item_label' => 'Nenhum',
                ],
            ])
            ->add([
                'name' => 'isAvailable',
                'type' => 'checkbox',
                'options' => [
                    'label' => 'Ativo',
                ],
            ])
            ->add([
                'name' => 'jobDescription',
                'type' => 'textarea',
                'attributes' => [
                    'placeholder' => 'Responsável pela gestão da área de informática que corresponde à ...',
                    'rows' => 10,
                ],
                'options' => [
                    'label' => 'Descrição',
                    'add-on-prepend' => '<i class="fa fa-font"></i>',
                ],
            ])
            ->add([
                'name' => 'parent',
                'type' => 'DoctrineModule\Form\Element\ObjectRadio',
                'options' => array(
                    'object_manager' => $obj,
                    'target_class' => 'AdministrativeStructure\Entity\Job',
                    'property' => 'jobName',
                    'label' => 'Cargo Superior',
                    'is_method' => true,
                    'find_method' => array(
                        'name' => 'findIgnoring',
                        'params' => array(
                            'ignoredId' => $disabledJobId
                        ),
                    ),
                ),
        ]);
    }

    public function getInputFilterSpecification()
    {
        return [
            'jobName' => [
                'required' => true,
                'filters' => [
                    ['name' => 'StringToUpper'],
                ],
                'validators' => [
                    [
                        'name' => 'StringLength',
                        'options' => [
                            'min' => '5',
                            'max' => '100',
                        ],
                    ],
                ],
            ],
            'jobDescription' => [
                'required' => true,
                'validators' => [
                    [
                        'name' => 'StringLength',
                        'options' => [
                            'min' => '5',
                            'max' => '1000',
                        ],
                    ],
                ],
            ],
            'children' => [
                'required' => false,
            ],
            'parent' => [
                'required' => false,
            ],
            'isAvailable' => [
                'required' => false,
            ]
        ];
    }

}
