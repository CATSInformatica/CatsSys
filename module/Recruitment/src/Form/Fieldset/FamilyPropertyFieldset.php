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

namespace Recruitment\Form\Fieldset;

use Doctrine\Common\Persistence\ObjectManager;
use DoctrineModule\Stdlib\Hydrator\DoctrineObject as DoctrineHydrator;
use Recruitment\Entity\FamilyProperty;
use Zend\Form\Fieldset;
use Zend\InputFilter\InputFilterProviderInterface;

/**
 * Fieldset para a entidade Recruitment\Entity\FamilyProperty
 *
 * @author Márcio Dias <marciojr91@gmail.com>
 */
class FamilyPropertyFieldset extends Fieldset implements InputFilterProviderInterface
{

    public function __construct(ObjectManager $obj)
    {
        parent::__construct('family-properties');

        $this->setHydrator(new DoctrineHydrator($obj))
            ->setObject(new FamilyProperty());

        $this
            ->add([
                'name' => 'propertyName',
                'type' => 'text',
                'options' => [
                    'label' => 'Tipo do imóvel (Ex: casa, lote, terreno, ...)'
                ]
            ])
            ->add([
                'name' => 'propertyDescription',
                'type' => 'textarea',
                'options' => [
                    'label' => 'Descrição (Valor do IPTU, a quantos anos possui a propriedade, ...)'
                ]
            ])
            ->add([
                'name' => 'propertyAddress',
                'type' => 'textarea',
                'options' => [
                    'label' => 'Endereço'
                ],
                'attribuites' => [
                    'rows' => 3,
                ]
            ])
        ;
    }

    public function getInputFilterSpecification()
    {
        return [
            'propertyName' => [
                'required' => true,
                'filters' => [
                    [
                        'name' => 'StringToUpper',
                        'options' => [
                            'encoding' => 'UTF-8',
                        ]
                    ],
                    [
                        'name' => 'StripTags',
                    ],
                    [
                        'name' => 'StringTrim',
                    ]
                ],
                'validators' => [
                    [
                        'name' => 'StringLength',
                        'options' => [
                            'min' => 3,
                            'max' => 200,
                        ]
                    ]
                ]
            ],
            'propertyDescription' => [
                'required' => false,
                'filters' => [
                    [
                        'name' => 'StripTags',
                    ],
                    [
                        'name' => 'StringTrim',
                    ]
                ],
                'validators' => [
                    [
                        'name' => 'StringLength',
                        'options' => [
                            'max' => 500,
                        ]
                    ]
                ]
            ],
            'propertyAddress' => [
                'required' => false,
                'filters' => [
                    [
                        'name' => 'StringToUpper',
                        'options' => [
                            'encoding' => 'UTF-8',
                        ]
                    ],
                    [
                        'name' => 'StripTags',
                    ],
                    [
                        'name' => 'StringTrim',
                    ]
                ],
                'validators' => [
                    [
                        'name' => 'StringLength',
                        'options' => [
                            'min' => 3,
                            'max' => 500,
                        ]
                    ]
                ]
            ]
        ];
    }

}
