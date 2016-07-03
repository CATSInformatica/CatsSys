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
use Recruitment\Entity\FamilyIncomeExpense;
use Zend\Form\Fieldset;
use Zend\InputFilter\InputFilterProviderInterface;
use DoctrineModule\Stdlib\Hydrator\DoctrineObject as DoctrineHydrator;

/**
 * Fieldset para a entidade \Recruitment\Entity\FamilyIncomeExpense.
 *
 * @author Márcio Dias <marciojr91@gmail.com>
 */
class FamilyIncomeExpenseFieldset extends Fieldset implements InputFilterProviderInterface
{
    const INCOME = 'familyIncome';
    const EXPENSE = 'familyExpense';

    public function __construct(ObjectManager $obj, $type)
    {
        parent::__construct($type);

        $this->setHydrator(new DoctrineHydrator($obj))
            ->setObject(new FamilyIncomeExpense());

        $this
            ->add([
                'name' => 'familyIncomeExpValue',
                'type' => 'number',
                'options' => [
                    'label' => 'Valor',
                ],
                'attributes' => [
                    'step' => 'any',
                ]
            ])
            ->add([
                'name' => 'familyIncomeExpDescription',
                'type' => 'textarea',
                'options' => [
                    'label' => 'Descrição',
                ],
                'attributes' => [
                    'rows' => '3',
                ],
        ]);
    }

    public function getInputFilterSpecification()
    {
        return [
            'familyIncomeExpValue' => [
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
            'familyIncomeExpDescription' => [
                'required' => true,
                'filters' => [
                    [
                        'name' => 'StripTags',
                    ],
                    [
                        'name' => 'StringTrim',
                    ],
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
