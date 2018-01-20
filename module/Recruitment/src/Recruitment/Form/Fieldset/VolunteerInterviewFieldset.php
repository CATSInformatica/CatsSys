<?php

namespace Recruitment\Form\Fieldset;

use Doctrine\Common\Persistence\ObjectManager;
use DoctrineModule\Stdlib\Hydrator\DoctrineObject as DoctrineHydrator;
use Recruitment\Entity\VolunteerInterview;
use Zend\Form\Fieldset;
use Zend\InputFilter\InputFilterProviderInterface;

/**
 * Description of PreInterviewFieldset
 *
 * @author Márcio Dias <marciojr91@gmail.com>
 */
class VolunteerInterviewFieldset extends Fieldset implements InputFilterProviderInterface
{

    public function __construct(ObjectManager $obj)
    {
        parent::__construct('volunteerInterview');

        $this->setHydrator(new DoctrineHydrator($obj))
            ->setObject(new VolunteerInterview());

        $this
            ->add([
                'name' => 'date',
                'type' => 'DateTime',
                'options' => [
                    'label' => 'Data da entrevista',
                    'add-on-prepend' => '<i class="fa fa-calendar-o"></i>',
                    'format' => 'd/m/Y'
                ],
                'attributes' => [
                    'class' => 'datepicker',
                    'id' => 'interview-date',
                ]
            ])
            ->add([
                'name' => 'startTime',
                'type' => 'DateTime',
                'options' => [
                    'label' => 'Início da entrevista',
                    'add-on-prepend' => '<i class="fa fa-clock-o"></i>',
                ],
                'attributes' => [
                    'class' => 'interview-time',
                ]
            ])
            ->add([
                'name' => 'interviewers',
                'type' => 'text',
                'options' => [
                    'label' => 'Entrevistadores (separados por "' . VolunteerInterview::INTERVIEWER_SEPARATOR . '")'
                ],
            ])
            ->add([
                'name' => 'interviewersInitialComments',
                'type' => 'textarea',
                'options' => [
                    'label' => 'Comentários dos entrevistadores (Colocar a ordem de preferência dos cargos desejados)',
                ],
                'attributes' => [
                    'rows' => 6,
                    'class' => 'col-xs-12 form-control',
                ],
            ])
            ->add([
                'name' => 'hometown',
                'type' => 'text',
                'options' => [
                    'label' => 'Cidade de origem',
                ],
                'attributes' => [
                    'class' => 'col-xs-12 form-control',
                    'maxlength' => 40,
                ],
            ])
            ->add([
                'name' => 'interests',
                'type' => 'textarea',
                'options' => [
                    'label' => 'O que gosta de fazer nas horas livres?',
                ],
                'attributes' => [
                    'rows' => 6,
                    'class' => 'col-xs-12 form-control',
                ],
            ])
            ->add([
                'name' => 'interpersonalRelationship',
                'type' => 'textarea',
                'options' => [
                    'label' => 'Com que tipo de pessoa prefere trabalhar? Com que tipo tem dificuldade?',
                ],
                'attributes' => [
                    'rows' => 6,
                    'class' => 'col-xs-12 form-control',
                ],
            ])
            ->add([
                'name' => 'proactivity',
                'type' => 'textarea',
                'options' => [
                    'label' => 'Caso se depare com algo que não concorda, você estuda um jeito de sugerir uma mudança ou tenta se adaptar?',
                ],
                'attributes' => [
                    'rows' => 6,
                    'class' => 'col-xs-12 form-control',
                ],
            ])
            ->add([
                'name' => 'qualities',
                'type' => 'textarea',
                'options' => [
                    'label' => 'Fale uma qualidade. / Como essa qualidade pode ajudar no CATS?',
                ],
                'attributes' => [
                    'rows' => 6,
                    'class' => 'col-xs-12 form-control',
                ],
            ])
            ->add([
                'name' => 'flaws',
                'type' => 'textarea',
                'options' => [
                    'label' => 'Fale um defeito. / Esse defeito poderia atrapalhar de alguma forma o CATS?',
                ],
                'attributes' => [
                    'rows' => 6,
                    'class' => 'col-xs-12 form-control',
                ],
            ])
            ->add([
                'name' => 'potentialIssues',
                'type' => 'textarea',
                'options' => [
                    'label' => 'Se você entrasse no CATS e fosse desligado um mês depois, por qual motivo seria?',
                ],
                'attributes' => [
                    'rows' => 6,
                    'class' => 'col-xs-12 form-control',
                ],
            ])
            ->add([
                'name' => 'flexibilityAndResponsability',
                'type' => 'textarea',
                'options' => [
                    'label' => 'Se você só tivesse uma escolha, preferiria fazer seu trabalho no horário ou corretamente?',
                ],
                'attributes' => [
                    'rows' => 6,
                    'class' => 'col-xs-12 form-control',
                ],
            ])
            ->add([
                'name' => 'coherenceTest',
                'type' => 'textarea',
                'options' => [
                    'label' => 'Caso seja aluno da UNIFEI: Você já cumpriu as horas complementares exigidas pelo seu curso? Se a pessoa dizer que não, perguntar se ela se interessaria pela vaga mesmo que não ganhasse horas complementares.',
                ],
                'attributes' => [
                    'rows' => 6,
                    'class' => 'col-xs-12 form-control',
                ],
            ])
            ->add([
                'name' => 'expectedContribution',
                'type' => 'textarea',
                'options' => [
                    'label' => 'Quanto tempo pretende ficar no CATS?',
                ],
                'attributes' => [
                    'rows' => 6,
                    'class' => 'col-xs-12 form-control',
                ],
            ])
            ->add([
                'name' => 'interestRating',
                'type' => 'text',
                'options' => [
                    'label' => 'De 0 a 10, quanto você quer entrar no CATS?',
                ],
                'attributes' => [
                    'class' => 'input-slider',
                    'data-min' => VolunteerInterview::INTEREST_RATING_MIN,
                    'data-max' => VolunteerInterview::INTEREST_RATING_MAX,
                    'data-step' => VolunteerInterview::INTEREST_RATING_STEP,
                    'data-default' => (int)(VolunteerInterview::INTEREST_RATING_MAX / 2) 
                ],
            ])
            ->add([
                'name' => 'endTime',
                'type' => 'DateTime',
                'options' => [
                    'label' => 'Término da entrevista',
                    'add-on-prepend' => '<i class="fa fa-clock-o"></i>',
                ],
                'attributes' => [
                    'class' => 'interview-time',
                ]
            ])
        ;
    }

    public function getInputFilterSpecification()
    {
        return [
            'date' => [
                'required' => true,
                'filters' => array(
                    array('name' => 'StripTags'),
                    array('name' => 'StringTrim'),
                    array(
                        'name' => 'Recruitment\Filter\DateToFormat',
                        'options' => array(
                            'inputFormat' => 'd/m/Y',
                            'outputFormat' => 'Y-m-d'
                        ),
                    ),
                ),
                'validators' => array(
                    array(
                        'name' => 'Date',
                        'options' => array(
                            'format' => 'Y-m-d',
                        ),
                    ),
                ),
            ],
            'startTime' => [
                'required' => true
            ],
            'interviewers' => [
                'required' => true,
                'filters' => [
                    ['name' => 'StringTrim'],
                    ['name' => 'StripTags']
                ],
                'validators' => [
                    [
                        'name' => 'StringLength',
                        'options' => [
                            'min' => 3,
                            'max' => 300,
                        ],
                    ],
                ],
            ],
            'interviewersInitialComments' => [
                'required' => true,
                'filters' => [
                    ['name' => 'StringTrim'],
                    ['name' => 'StripTags']
                ],
                'validators' => [
                    [
                        'name' => 'StringLength',
                        'options' => [
                            'min' => 3,
                            'max' => 500,
                        ],
                    ],
                ],
            ],
            'hometown' => [
                'required' => true,
                'filters' => [
                    ['name' => 'StringTrim'],
                    ['name' => 'StripTags']
                ],
                'validators' => [
                    [
                        'name' => 'StringLength',
                        'options' => [
                            'min' => 3,
                            'max' => 40,
                        ],
                    ],
                ],
            ],
            'interests' => [
                'required' => true,
                'filters' => [
                    ['name' => 'StringTrim'],
                    ['name' => 'StripTags']
                ],
                'validators' => [
                    [
                        'name' => 'StringLength',
                        'options' => [
                            'min' => 3,
                            'max' => 500,
                        ],
                    ],
                ],
            ],
            'interpersonalRelationship' => [
                'required' => true,
                'filters' => [
                    ['name' => 'StringTrim'],
                    ['name' => 'StripTags']
                ],
                'validators' => [
                    [
                        'name' => 'StringLength',
                        'options' => [
                            'min' => 3,
                            'max' => 500,
                        ],
                    ],
                ],
            ],
            'proactivity' => [
                'required' => true,
                'filters' => [
                    ['name' => 'StringTrim'],
                    ['name' => 'StripTags']
                ],
                'validators' => [
                    [
                        'name' => 'StringLength',
                        'options' => [
                            'min' => 3,
                            'max' => 500,
                        ],
                    ],
                ],
            ],
            'qualities' => [
                'required' => true,
                'filters' => [
                    ['name' => 'StringTrim'],
                    ['name' => 'StripTags']
                ],
                'validators' => [
                    [
                        'name' => 'StringLength',
                        'options' => [
                            'min' => 3,
                            'max' => 500,
                        ],
                    ],
                ],
            ],
            'flaws' => [
                'required' => true,
                'filters' => [
                    ['name' => 'StringTrim'],
                    ['name' => 'StripTags']
                ],
                'validators' => [
                    [
                        'name' => 'StringLength',
                        'options' => [
                            'min' => 3,
                            'max' => 500,
                        ],
                    ],
                ],
            ],
            'potentialIssues' => [
                'required' => true,
                'filters' => [
                    ['name' => 'StringTrim'],
                    ['name' => 'StripTags']
                ],
                'validators' => [
                    [
                        'name' => 'StringLength',
                        'options' => [
                            'min' => 3,
                            'max' => 500,
                        ],
                    ],
                ],
            ],
            'flexibilityAndResponsability' => [
                'required' => true,
                'filters' => [
                    ['name' => 'StringTrim'],
                    ['name' => 'StripTags']
                ],
                'validators' => [
                    [
                        'name' => 'StringLength',
                        'options' => [
                            'min' => 3,
                            'max' => 500,
                        ],
                    ],
                ],
            ],
            'expectedContribution' => [
                'required' => true,
                'filters' => [
                    ['name' => 'StringTrim'],
                    ['name' => 'StripTags']
                ],
                'validators' => [
                    [
                        'name' => 'StringLength',
                        'options' => [
                            'min' => 3,
                            'max' => 500,
                        ],
                    ],
                ],
            ],
            'coherenceTest' => [
                'required' => true,
                'filters' => [
                    ['name' => 'StringTrim'],
                    ['name' => 'StripTags']
                ],
                'validators' => [
                    [
                        'name' => 'StringLength',
                        'options' => [
                            'min' => 3,
                            'max' => 500,
                        ],
                    ],
                ],
            ],
            'interestRating' => [
                'required' => true,
                'validator' => [
                    [
                        'name' => 'Digits'
                    ],
                    [
                        'name' => 'Between', 
                        'options' => [
                            'min' => VolunteerInterview::INTEREST_RATING_MIN,
                            'max' => VolunteerInterview::INTEREST_RATING_MAX,
                            'inclusive' => true                            
                        ]
                    ],
                ],
            ],
            'endTime' => [
                'required' => true
            ],
        ];
    }

}
