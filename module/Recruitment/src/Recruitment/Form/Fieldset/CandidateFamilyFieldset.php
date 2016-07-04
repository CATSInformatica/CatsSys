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
use Recruitment\Entity\CandidateFamily;
use Zend\Form\Fieldset;
use Zend\InputFilter\InputFilterProviderInterface;

/**
 * Fieldset para a entidade Recruitment\Entity\CandidateFamily.
 *
 * @author Márcio Dias <marciojr91@gmail.com>
 */
class CandidateFamilyFieldset extends Fieldset implements InputFilterProviderInterface
{

    public function __construct(ObjectManager $obj)
    {
        parent::__construct('candidate-family');

        $this->setHydrator(new DoctrineHydrator($obj))
            ->setObject(new CandidateFamily());

        $this
            ->add([
                'name' => 'candidateFamilyName',
                'type' => 'text',
                'options' => [
                    'label' => 'Nome da pessoa',
                ],
            ])
            ->add([
                'name' => 'candidateFamilyAge',
                'type' => 'number',
                'options' => [
                    'label' => 'Idade',
                ],
                'attributes' => [
                    'step' => '1',
                ]
            ])
            ->add([
                'name' => 'maritalStatus',
                'type' => 'select',
                'options' => [
                    'label' => 'Estado civil',
                    'empty_option' => '',
                    'value_options' => CandidateFamily::getMaritalStatusArray(),
                ],
            ])
            ->add([
                'name' => 'relationship',
                'type' => 'select',
                'options' => [
                    'label' => 'Parentesco',
                    'empty_option' => '',
                    'value_options' => CandidateFamily::getRelationshipArray(),
                ],
            ])
            ->add([
                'name' => 'scholarity',
                'type' => 'select',
                'options' => [
                    'label' => 'Escolaridade',
                    'empty_option' => '',
                    'value_options' => CandidateFamily::getScholarityArray(),
                ],
            ])
            ->add([
                'name' => 'workSituation',
                'type' => 'select',
                'options' => [
                    'label' => 'Situação de trabalho',
                    'empty_option' => '',
                    'value_options' => CandidateFamily::getWorkSituationArray(),
                ],
            ])
        ;
    }

    public function getInputFilterSpecification()
    {
        return [
            'candidateFamilyName' => [
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
                            'min' => 2,
                            'max' => 100,
                        ]
                    ]
                ]
            ],
            'candidateFamilyAge' => [
                'required' => true,
                'validators' => [
                    [
                        'name' => 'Zend\I18n\Validator\IsInt',
                    ],
                    [
                        'name' => 'Zend\Validator\Between',
                        'options' => [
                            'min' => 0,
                            'max' => 110,
                        ]
                    ]
                ],
            ],
            'maritalStatus' => [
                'required' => true,
            ],
            'relationship' => [
                'required' => true,
            ],
            'scholarity' => [
                'required' => true,
            ],
            'workSituation' => [
                'required' => true,
            ],
        ];
    }
}
