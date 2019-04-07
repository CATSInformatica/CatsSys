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
use Recruitment\Entity\FamilyHealth;
use Zend\Form\Fieldset;
use Zend\InputFilter\InputFilterProviderInterface;

/**
 * Fieldset para a entidade \Recruitment\Entity\FamilyHealth.
 *
 * @author Márcio Dias <marciojr91@gmail.com>
 */
class FamilyHealthFieldset extends Fieldset implements InputFilterProviderInterface
{

    public function __construct(ObjectManager $obj)
    {
        parent::__construct('family-health');

        $this->setHydrator(new DoctrineHydrator($obj))
            ->setObject(new FamilyHealth());

        $this
            ->add([
                'name' => 'familyHealthName',
                'type' => 'text',
                'options' => [
                    'label' => 'Nome da pessoa',
                ]
            ])
            ->add([
                'name' => 'healthProblem',
                'type' => 'text',
                'options' => [
                    'label' => 'Problema de saúde',
                ]
            ])
            ->add([
                'name' => 'disableForWork',
                'type' => 'radio',
                'options' => [
                    'label' => 'Impede a pessoa de trabalhar?',
                    'value_options' => [
                        1 => 'Sim',
                        0 => 'Não',
                    ],
                ]
            ])
            ->add([
                'name' => 'dailyDependency',
                'type' => 'radio',
                'options' => [
                    'label' => 'A pessoa precisa de acompanhamento diário?',
                    'value_options' => [
                        1 => 'Sim',
                        0 => 'Não',
                    ],
                ]
            ])
        ;
    }

    public function getInputFilterSpecification()
    {
        return [
            'familyHealthName' => [
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
                            'max' => 100,
                        ]
                    ]
                ]
            ],
            'healthProblem' => [
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
                            'max' => 150,
                        ]
                    ]
                ]
            ],
            'disableForWork' => [
                'required' => true,
            ],
            'dailyDependency' => [
                'required' => true,
            ],
        ];
    }

}
