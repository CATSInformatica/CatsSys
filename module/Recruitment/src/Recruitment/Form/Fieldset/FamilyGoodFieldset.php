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
use Recruitment\Entity\FamilyGood;
use Zend\Form\Fieldset;
use Zend\InputFilter\InputFilterProviderInterface;

/**
 * Description of FamilyGoodFieldset
 *
 * @author Márcio Dias <marciojr91@gmail.com>
 */
class FamilyGoodFieldset extends Fieldset implements InputFilterProviderInterface
{

    public function __construct(ObjectManager $obj)
    {
        parent::__construct('family-good');

        $this->setHydrator(new DoctrineHydrator($obj))
            ->setObject(new FamilyGood());

        $this
            ->add([
                'name' => 'goodName',
                'type' => 'text',
                'options' => [
                    'label' => 'Tipo do bem (Ex: carro, motocileta, jet ski, ...)'
                ]
            ])
            ->add([
                'name' => 'goodValue',
                'type' => 'number',
                'options' => [
                    'label' => 'Valor estimado'
                ]
            ])
            ->add([
                'name' => 'goodDescription',
                'type' => 'textarea',
                'options' => [
                    'label' => 'Descrição do bem'
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
            'goodName' => [
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
            'goodValue' => [
                'required' => true,
                'validators' => [
                    [
                        'name' => 'Zend\I18n\Validator\IsFloat',
                    ],
                    [
                        'name' => 'Zend\Validator\GreaterThan',
                        'options' => [
                            'min' => 0
                        ]
                    ]
                ],
            ],
            'goodDescription' => [
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
                            'max' => 500,
                        ]
                    ]
                ]
            ]
        ];
    }

}
