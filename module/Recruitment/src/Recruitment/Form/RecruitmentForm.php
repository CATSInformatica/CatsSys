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

namespace Recruitment\Form;

use DateTime;
use Recruitment\Entity\Recruitment;
use Zend\Form\Form;
use Zend\InputFilter\InputFilterProviderInterface;

/**
 * Modela os campos da entidade Recruitment\Entity\Recruitment
 *
 * @author Márcio Dias <marciojr91@gmail.com>
 */
class RecruitmentForm extends Form implements InputFilterProviderInterface
{

    //put your code here
    public function __construct()
    {
        parent::__construct('Recruitment');
        $this->setAttribute('method', 'post');
        $this->setAttribute('enctype', 'multipart/form-data');

        $this->add(array(
                'name' => 'recruitment_number',
                'type' => 'Zend\Form\Element\Select',
                'options' => array(
                    'label' => 'Número do processo seletivo',
                    'empty_option' => '',
                    'value_options' => array(
                        1 => '1º',
                        2 => '2º',
                        3 => '3º',
                        4 => '4º',
                        5 => '5º',
                        6 => '6º',
                        7 => '7º',
                        8 => '8º',
                        9 => '9º',
                    ),
                ),
            ))
            ->add(array(
                'name' => 'recruitment_year',
                'type' => 'Zend\Form\Element\Select',
                'options' => array(
                    'label' => 'Ano do processo seletivo',
                    'empty_option' => 'Ano do processo seletivo',
                    'value_options' => $this->getYears(),
                ),
                'attributes' => array(
                    'class' => 'text-center',
                ),
            ))
            ->add(array(
                'name' => 'recruitment_begindate',
                'attributes' => array(
                    'type' => 'text',
                    'class' => 'form-control datepicker text-center',
                ),
                'options' => array(
                    'label' => 'Data de início',
                    'add-on-prepend' => '<i class="glyphicon glyphicon-calendar"></i>',
                ),
            ))
            ->add(array(
                'name' => 'recruitment_enddate',
                'attributes' => array(
                    'type' => 'text',
                    'class' => 'form-control datepicker text-center',
                ),
                'options' => array(
                    'label' => 'Data de término',
                    'add-on-prepend' => '<i class="glyphicon glyphicon-calendar"></i>',
                ),
            ))
            ->add(array(
                'name' => 'recruitment_public_notice',
                'attributes' => array(
                    'type' => 'file',
                ),
                'options' => array(
                    'label' => 'Arquivo do edital',
                ),
            ))
            ->add(array(
                'name' => 'recruitment_type',
                'type' => 'Zend\Form\Element\Select',
                'options' => array(
                    'label' => 'Tipo de processo seletivo',
                    'empty_option' => '',
                    'value_options' => array(
                        Recruitment::STUDENT_RECRUITMENT_TYPE => 'Processo Seletivo de Alunos',
                        Recruitment::VOLUNTEER_RECRUITMENT_TYPE => 'Processo Seletivo de Voluntários',
                    ),
                ),
                'attributes' => array(
                    'class' => 'text-center',
                ),
            ))
            ->add([
                'name' => 'recruitmentSocioeconomicTarget',
                'type' => 'number',
                'options' => [
                    'label' => 'Socioeconômico',
                ],
                'attributes' => [
                    'class' => 'input-slider',
                    'data-slider-min'=> 0,
                    'data-slider-step' => 0.01,
                    'data-slider-max'=> 10,
                    'data-slider-id' => 'yellow',
                    'min' => 0,
                    'max' => 10,
                    'step' => 0.01,
                ]
            ])
            ->add([
                'name' => 'recruitmentVulnerabilityTarget',
                'type' => 'number',
                'options' => [
                    'label' => 'Vulerabilidade',
                ],
                'attributes' => [
                    'class' => 'input-slider',
                    'data-slider-min'=> 0,
                    'data-slider-step' => 0.01,
                    'data-slider-max'=> 10,
                    'data-slider-id' => 'red',
                    'min' => 0,
                    'max' => 10,
                    'step' => 0.01,
                ]
            ])
            ->add([
                'name' => 'recruitmentStudentTarget',
                'type' => 'number',
                'options' => [
                    'label' => 'Perfil de estudante',
                ],
                'attributes' => [
                    'class' => 'input-slider',
                    'data-slider-min'=> 0,
                    'data-slider-step' => 0.01,
                    'data-slider-max'=> 10,
                    'data-slider-id' => 'green',
                    'min' => 0,
                    'max' => 10,
                    'step' => 0.01,
                ]
            ])
            ->add([
                'name' => 'Submit',
                'type' => 'submit',
                'attributes' => [
                    'class' => 'btn btn-primary btn-block',
                    'value' => 'Criar',
                ]
        ]);
    }

    protected function getYears()
    {
        $year = (new DateTime('now'))->format('Y');
        return array(
            $year => $year,
            ++$year => $year,
        );
    }

    public function getInputFilterSpecification()
    {
        return [
            'recruitment_number' => [
                'required' => true,
            ],
            'recruitment_year' => [
                'required' => true,
            ],
            'recruitment_begindate' => [
                'required' => true,
                'filters' => [
                    ['name' => 'StripTags'],
                    ['name' => 'StringTrim'],
                    [
                        'name' => 'Recruitment\Filter\DateToFormat',
                        'options' => [
                            'inputFormat' => 'd/m/Y',
                            'outputFormat' => 'Y-m-d'
                        ],
                    ],
                ],
                'validators' => [
                    [
                        'name' => 'Date',
                        'options' => [
                            'format' => 'Y-m-d',
                        ]
                    ],
                    [
                        'name' => 'Recruitment\Validator\DateGratherThan',
                        'options' => [
                            'format' => 'Y-m-d',
                            'inclusive' => true,
                        ],
                    ],
                ],
            ],
            'recruitment_enddate' => [
                'require' => true,
                'filters' => [
                    ['name' => 'StripTags'],
                    ['name' => 'StringTrim'],
                    [
                        'name' => 'Recruitment\Filter\DateToFormat',
                        'options' => [
                            'inputFormat' => 'd/m/Y',
                            'outputFormat' => 'Y-m-d'
                        ],
                    ],
                ],
                'validators' => [
                    [
                        'name' => 'Date',
                        'options' => [
                            'format' => 'Y-m-d',
                        ]
                    ],
                    [
                        'name' => 'Recruitment\Validator\DateGratherThan',
                        'options' => [
                            'format' => 'Y-m-d',
                            'compareWith' => [
                                'name' => 'recruitment_begindate',
                                'format' => 'd/m/Y',
                            ],
                        ],
                    ],
                ],
            ],
            'recruitment_public_notice' => [
                'required' => true,
                'validators' => [
                    [
                        'name' => 'Zend\Validator\File\Extension',
                        'options' => [
                            'extension' => [
                                'pdf',
                            ],
                        ],
                    ],
                    [
                        'name' => 'Zend\Validator\File\Size',
                        'options' => [
                            'min' => '1000',
                            'max' => '5000000',
                        ]
                    ]
                ]
            ],
            'recruitment_type' => [
                'required' => true,
            ],
            'recruitmentSocioeconomicTarget' => [
                'required' => false,
            ],
            'recruitmentVulnerabilityTarget' => [
                'required' => false,
            ],
            'recruitmentStudentTarget' => [
                'required' => false,
            ],
        ];
    }
}
