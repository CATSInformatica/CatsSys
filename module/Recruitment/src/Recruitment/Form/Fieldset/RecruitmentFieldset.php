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

use DateTime;
use Doctrine\Common\Persistence\ObjectManager;
use DoctrineModule\Stdlib\Hydrator\DoctrineObject as DoctrineHydrator;
use Recruitment\Entity\Recruitment;
use Zend\Form\Fieldset;
use Zend\InputFilter\InputFilterProviderInterface;

/**
 * Modela os campos da entidade \Recruitment\Entity\Recruitment
 *
 * @author Márcio Dias <marciojr91@gmail.com>
 */
class RecruitmentFieldset extends Fieldset implements InputFilterProviderInterface
{

    public function __construct(ObjectManager $obj, $name = null, $options = array())
    {
        parent::__construct($name, $options);

        $this->setHydrator(new DoctrineHydrator($obj))
            ->setObject(new Recruitment());

        $openJobOptions = $this->getJobs($obj);
        
        $this->add(array(
                'name' => 'recruitmentNumber',
                'type' => 'Zend\Form\Element\Select',
                'options' => array(
                    'label' => 'Número do processo seletivo',
                    'empty_option' => 'Escolha entre 1º e 9º',
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
                'name' => 'recruitmentYear',
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
                'name' => 'recruitmentBeginDate',
                'attributes' => array(
                    'type' => 'text',
                    'class' => 'form-control datepicker text-center',
                ),
                'options' => array(
                    'label' => 'Data de início das inscrições',
                    'add-on-prepend' => '<i class="glyphicon glyphicon-calendar"></i>',
                ),
            ))
            ->add(array(
                'name' => 'recruitmentEndDate',
                'attributes' => array(
                    'type' => 'text',
                    'class' => 'form-control datepicker text-center',
                ),
                'options' => array(
                    'label' => 'Data de término das inscrições',
                    'add-on-prepend' => '<i class="glyphicon glyphicon-calendar"></i>',
                ),
            ))
            ->add(array(
                'name' => 'recruitmentPublicNotice',
                'attributes' => array(
                    'type' => 'file',
                ),
                'options' => array(
                    'label' => 'Arquivo do edital',
                ),
            ))
            ->add(array(
                'name' => 'recruitmentType',
                'type' => 'Zend\Form\Element\Select',
                'options' => array(
                    'label' => 'Tipo de processo seletivo',
                    'empty_option' => 'Alunos ou Voluntários?',
                    'value_options' => array(
                        Recruitment::STUDENT_RECRUITMENT_TYPE => 'Processo Seletivo de Alunos',
                        Recruitment::VOLUNTEER_RECRUITMENT_TYPE => 'Processo Seletivo de Voluntários',
                    ),
                ),
                'attributes' => array(
                    'class' => 'text-center',
                    'data-student-type' => Recruitment::STUDENT_RECRUITMENT_TYPE,
                    'data-volunteer-type' => Recruitment::VOLUNTEER_RECRUITMENT_TYPE,
                ),
            ))
            ->add(array(
                'name' => 'openJobs',
                'type' => 'Zend\Form\Element\Select',
                'attributes' => array(
                    'multiple' => 'multiple',
                    'size' => count($openJobOptions),
                ),
                'options' => array(
                    'label' => 'Selecione os cargos abertos',
                    'value_options' => $openJobOptions,
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
                    'data-slider-min' => 0,
                    'data-slider-step' => 0.1,
                    'data-slider-max' => 10,
                    'data-slider-id' => 'yellow',
                    'data-slider-value' => 4,
                    'min' => 0,
                    'max' => 10,
                    'step' => 0.1,
                ],
            ])
            ->add([
                'name' => 'recruitmentVulnerabilityTarget',
                'type' => 'number',
                'options' => [
                    'label' => 'Vulerabilidade',
                ],
                'attributes' => [
                    'class' => 'input-slider',
                    'data-slider-min' => 0,
                    'data-slider-step' => 0.1,
                    'data-slider-max' => 10,
                    'data-slider-id' => 'red',
                    'data-slider-value' => 5,
                    'min' => 0,
                    'max' => 10,
                    'step' => 0.1,
                ],
            ])
            ->add([
                'name' => 'recruitmentStudentTarget',
                'type' => 'number',
                'options' => [
                    'label' => 'Perfil de estudante',
                ],
                'attributes' => [
                    'class' => 'input-slider',
                    'data-slider-min' => 0,
                    'data-slider-step' => 0.1,
                    'data-slider-max' => 10,
                    'data-slider-id' => 'green',
                    'data-slider-value' => 6,
                    'min' => 0,
                    'max' => 10,
                    'step' => 0.1,
                ],
            ])
            ->add([
                'name' => 'subscriptionDescription',
                'type' => 'textarea',
                'options' => [
                    'label' => 'Descrição para o período de inscrições',
                ],
                'attributes' => [
                    'rows' => 6,
                    'placeholder' => 'As inscrições estão abertas de 01 de Dezembro de 2016 a 25 de Janeiro de 2017. '
                    . 'Para fazer sua inscrição, o candidato deve preencher um formulário de indentificação. Após prencher o formulário o candidato passa a ter acesso a area de acompanhamento de '
                    . 'inscrição, onde poderá acompanhar cada etapa do processo seletivo.',
                ]
            ])
            ->add(array(
                'name' => 'confirmationStartDate',
                'attributes' => array(
                    'type' => 'text',
                    'class' => 'form-control datepicker text-center',
                ),
                'options' => array(
                    'label' => 'Data de início do período de confirmação',
                    'add-on-prepend' => '<i class="glyphicon glyphicon-calendar"></i>',
                ),
            ))
            ->add(array(
                'name' => 'confirmationEndDate',
                'attributes' => array(
                    'type' => 'text',
                    'class' => 'form-control datepicker text-center',
                ),
                'options' => array(
                    'label' => 'Data de término do período de confirmação',
                    'add-on-prepend' => '<i class="glyphicon glyphicon-calendar"></i>',
                ),
            ))
            ->add([
                'name' => 'confirmationDescription',
                'type' => 'textarea',
                'options' => [
                    'label' => 'Descrição para o período de confirmações',
                ],
                'attributes' => [
                    'rows' => 6,
                    'placeholder' => 'Para completar a inscrição no processo seletivo de alunos o candidato deve comparecer ao campus da UNIFEI (bairro Pinheirinho), '
                    . 'Bloco I (prédio da Elétrica), sala I.1.2.47, levando consigo o RG, 2kg de alimento (exceto sal, açucar e farinha) ou 1kg de ração para animais (cachorro ou gato). '
                    . 'Todos os itens arrecadados serão doados ao final do processo seletivo.',
                ]
            ])
            ->add(array(
                'name' => 'examDate',
                'attributes' => array(
                    'type' => 'text',
                    'class' => 'form-control datepicker text-center',
                ),
                'options' => array(
                    'label' => 'Data da prova',
                    'add-on-prepend' => '<i class="glyphicon glyphicon-calendar"></i>',
                ),
            ))
            ->add([
                'name' => 'examDescription',
                'type' => 'textarea',
                'options' => [
                    'label' => 'Descrição para a prova',
                ],
                'attributes' => [
                    'rows' => 6,
                    'placeholder' => 'A prova será aplicada no campus da UNIFEI (Av. BPS, 1303, Pinheirinho Itajubá/MG) no Bloco I (prédio da elétrica), das 13h30min às 18h30min.'
                    . 'O candidato deverá portar o cartão recebido na etapa de confirmação, documento com foto e caneta esferográfica preta. Por favor, consulte a seção IV do edital para verificar todas as '
                    . 'instruções para o dia de prova.'
                ]
            ])
            ->add(array(
                'name' => 'examResultDate',
                'attributes' => array(
                    'type' => 'text',
                    'class' => 'form-control datepicker text-center',
                ),
                'options' => array(
                    'label' => 'Data de liberação do resultado da prova',
                    'add-on-prepend' => '<i class="glyphicon glyphicon-calendar"></i>',
                ),
            ))
            ->add([
                'name' => 'examResultDescription',
                'type' => 'textarea',
                'options' => [
                    'label' => 'Descrição para o resultado da prova',
                ],
                'attributes' => [
                    'rows' => 6,
                    'placeholder' => 'O resultado da prova será disponibilizado no dia 11/05. Para visualizá-lo o candidato deverá acessar a área de acompanhamento de inscrição.'
                    . ' Os candidatos aprovados deverão consultar o edital e verificar os documentos necessários parar a etapa de pré-entrevista. '
                    . 'Candidados na lista de espera poderão ser convocados caso haja desistência de candidatos convocados.',
                ]
            ])
            ->add(array(
                'name' => 'preInterviewStartDate',
                'attributes' => array(
                    'type' => 'text',
                    'class' => 'form-control datepicker text-center',
                ),
                'options' => array(
                    'label' => 'Data de início do período de pré-entrevistas',
                    'add-on-prepend' => '<i class="glyphicon glyphicon-calendar"></i>',
                ),
            ))
            ->add([
                'name' => 'preInterviewDescription',
                'type' => 'textarea',
                'options' => [
                    'label' => 'Descrição para o período de pré-entrevistas',
                ],
                'attributes' => [
                    'rows' => 6,
                    'placeholder' => 'A lista dos candidatos convocados para pré-entrevista será divulgada no dia 11/07 pelo site do CATS. Caso se faça necessário, '
                    . 'uma segunda chamada será feita. Os candidatos convocados deverão preencher o formulário on-line disponibilizado na área de acompanhamento de inscrição.',
                ]
            ])
            ->add(array(
                'name' => 'interviewStartDate',
                'attributes' => array(
                    'type' => 'text',
                    'class' => 'form-control datepicker text-center',
                ),
                'options' => array(
                    'label' => 'Data de início do período de entrevistas',
                    'add-on-prepend' => '<i class="glyphicon glyphicon-calendar"></i>',
                ),
            ))
            ->add(array(
                'name' => 'interviewEndDate',
                'attributes' => array(
                    'type' => 'text',
                    'class' => 'form-control datepicker text-center',
                ),
                'options' => array(
                    'label' => 'Data de término do período de entrevistas',
                    'add-on-prepend' => '<i class="glyphicon glyphicon-calendar"></i>',
                ),
            ))
            ->add([
                'name' => 'interviewDescription',
                'type' => 'textarea',
                'options' => [
                    'label' => 'Descrição para o período de entrevistas',
                ],
                'attributes' => [
                    'rows' => 6,
                    'data-student-placeholder' => 'Os candidatos convocados deverão comparecer ao campus da UNIFEI para a entrevista. O dia de entrevista é definido pela colocação na prova. '
                    . 'As datas e documentos exigidos para a entrevista podem ser conferidos no edital (seção VIII).',
                    'data-volunteer-placeholder' => 'Os candidatos convocados deverão comparecer ao campus da UNIFEI para a entrevista. O dia de entrevista é agendado mediante a convocação do candidato.',
                    'class' => 'undefined-placeholder',
                ]
            ])
            ->add([
                'name' => 'testClassDescription',
                'type' => 'textarea',
                'options' => [
                    'label' => 'Descrição para a aula teste',
                ],
                'attributes' => [
                    'rows' => 6,
                    'placeholder' => 'Os candidatos que desejam lecionar são convocados a apresentar uma aula teste.',
                ]
            ])
            ->add(array(
                'name' => 'resultDate',
                'attributes' => array(
                    'type' => 'text',
                    'class' => 'form-control datepicker text-center',
                ),
                'options' => array(
                    'label' => 'Data do resultado final',
                    'add-on-prepend' => '<i class="glyphicon glyphicon-calendar"></i>',
                ),
            ))
            ->add([
                'name' => 'resultDescription',
                'type' => 'textarea',
                'options' => [
                    'label' => 'Descrição para a data do resultado final',
                ],
                'attributes' => [
                    'rows' => 6,
                    'data-student-placeholder' => 'O resultado da entrevista será liberado no dia 20/07. Os candidatos aprovados deverão aguardar o período de matrícula e aqueles em lista de espera '
                    . 'poderão ser chamados caso haja desistência de candidatos aprovados.',
                    'data-volunteer-placeholder' => 'O resultado será liberado no dia 20/07. Os candidatos aprovados deverão aguardar o contato de um voluntário do CATS.',
                    'class' => 'undefined-placeholder',
                ]
            ])
            ->add(array(
                'name' => 'enrollmentStartDate',
                'attributes' => array(
                    'type' => 'text',
                    'class' => 'form-control datepicker text-center',
                ),
                'options' => array(
                    'label' => 'Data de início do período de matrícula',
                    'add-on-prepend' => '<i class="glyphicon glyphicon-calendar"></i>',
                ),
            ))
            ->add(array(
                'name' => 'enrollmentEndDate',
                'attributes' => array(
                    'type' => 'text',
                    'class' => 'form-control datepicker text-center',
                ),
                'options' => array(
                    'label' => 'Data de término do período de matrícula',
                    'add-on-prepend' => '<i class="glyphicon glyphicon-calendar"></i>',
                ),
            ))
            ->add([
                'name' => 'enrollmentDescription',
                'type' => 'textarea',
                'options' => [
                    'label' => 'Descrição para o período de matrícula',
                ],
                'attributes' => [
                    'rows' => 6,
                    'placeholder' => 'A matrícula do candidato aprovado na entrevista deverá ser realizada no período de 01/08/2016 a 10/08/2016. '
                    . 'O custo da matrícula é de R$30,00 (trinta reais). Menores de 18 anos deverão estar acompanhados de um responsável.',
                ]
            ])
        ;
    }

    protected function getYears()
    {
        $year = (new DateTime('now'))->format('Y');
        return array(
            $year => $year,
            ++$year => $year,
        );
    }
    
    /**
     * Retorna um array associativo com os ids e nomes de todos os cargos ativos.
     * O array tem a forma conforme a seguir:
     *  [
     *      <id> => <jobName>,
     *      .
     *      .
     *      .
     *  ]
     * 
     * @param ObjectManager $obj - entity manager
     * @return array
     */
    protected function getJobs(ObjectManager $obj) {
        $jobs = $obj->getRepository('\AdministrativeStructure\Entity\Job')->findBy([
            'isAvailable' => true
        ]);
        $jobsNames = [];
        
        foreach ($jobs as $job) {
            $jobsNames[$job->getJobId()] = $job->getJobName();
        }
        
        return $jobsNames;
    }

    public function getInputFilterSpecification()
    {        
        return [
            'recruitmentNumber' => [
                'required' => true,
            ],
            'recruitmentYear' => [
                'required' => true,
            ],
            'recruitmentBeginDate' => [
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
            'recruitmentEndDate' => [
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
                                'name' => 'recruitmentBeginDate',
                                'format' => 'd/m/Y',
                            ],
                        ],
                    ],
                ],
            ],
            'recruitmentPublicNotice' => [
                'required' => false,
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
            'recruitmentType' => [
                'required' => true,
            ],
            'openJobs' => [
                'required' => false,
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
            'subscriptionDescription' => [
                'required' => false,
            ],
            'confirmationStartDate' => [
                'required' => false,
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
                                'name' => 'recruitmentBeginDate',
                                'format' => 'd/m/Y',
                            ],
                        ],
                    ],
                ],
            ],
            'confirmationEndDate' => [
                'required' => false,
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
                                'name' => 'confirmationStartDate',
                                'format' => 'd/m/Y',
                                'inclusive' => true,
                            ],
                        ],
                    ],
                ],
            ],
            'confirmationDescription' => [
                'required' => false,
            ],
            'examDate' => [
                'required' => false,
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
                                'name' => 'confirmationEndDate',
                                'format' => 'd/m/Y',
                                'inclusive' => true,
                            ],
                        ],
                    ],
                ],
            ],
            'examDescription' => [
                'required' => false,
            ],
            'examResultDate' => [
                'required' => false,
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
                                'name' => 'examDate',
                                'format' => 'd/m/Y',
                                'inclusive' => true,
                            ],
                        ],
                    ],
                ],
            ],
            'examResultDescription' => [
                'required' => false,
            ],
            'preInterviewStartDate' => [
                'required' => false,
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
                                'name' => 'examResultDate',
                                'format' => 'd/m/Y',
                                'inclusive' => true,
                            ],
                        ],
                    ],
                ],
            ],
            'preInterviewDescription' => [
                'required' => false,
            ],
            'interviewStartDate' => [
                'required' => false,
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
                                'name' => 'preInterviewStartDate',
                                'format' => 'd/m/Y',
                                'inclusive' => true,
                            ],
                        ],
                    ],
                ],
            ],
            'interviewEndDate' => [
                'required' => false,
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
                                'name' => 'interviewStartDate',
                                'format' => 'd/m/Y',
                                'inclusive' => true,
                            ],
                        ],
                    ],
                ],
            ],
            'interviewDescription' => [
                'required' => false,
            ],
            'resultDate' => [
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
                            'compareWith' => [
                                'name' => 'interviewEndDate',
                                'format' => 'd/m/Y',
                                'inclusive' => true,
                            ],
                        ],
                    ],
                ],
            ],
            'resultDescription' => [
                'required' => false,
            ],
            'enrollmentStartDate' => [
                'required' => false,
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
                                'name' => 'resultDate',
                                'format' => 'd/m/Y',
                                'inclusive' => true,
                            ],
                        ],
                    ],
                ],
            ],
            'enrollmentEndDate' => [
                'required' => false,
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
                                'name' => 'enrollmentStartDate',
                                'format' => 'd/m/Y',
                                'inclusive' => true,
                            ],
                        ],
                    ],
                ],
            ],
            'enrollmentDescription' => [
                'required' => false,
            ],
        ];
    }
}
