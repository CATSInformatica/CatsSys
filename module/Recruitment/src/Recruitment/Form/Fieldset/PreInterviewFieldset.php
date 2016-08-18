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
use Recruitment\Entity\PreInterview;
use Zend\Form\Fieldset;
use Zend\InputFilter\InputFilterProviderInterface;

/**
 * Define os campos para os atributos da entidade Recruitment\Entity\PreInterview.
 *
 * @author Márcio Dias <marciojr91@gmail.com>
 */
class PreInterviewFieldset extends Fieldset implements InputFilterProviderInterface
{

    public function __construct(ObjectManager $obj)
    {
        parent::__construct('preInterview');

        $this->setHydrator(new DoctrineHydrator($obj))
            ->setObject(new PreInterview());


        $numberedOptions = [
            '0' => 'Nenhum',
            '1' => '1',
            '2' => '2',
            '3' => '3',
            '4' => '4',
            '5' => '5',
            '6 ou mais' => '6 ou mais',
        ];

        // SOCIOECONÔMICO
        $familyExpense = new FamilyIncomeExpenseFieldset($obj, FamilyIncomeExpenseFieldset::EXPENSE);

        $this->add(array(
            'type' => 'Zend\Form\Element\Collection',
            'name' => 'familyExpenses',
            'options' => array(
                'count' => 1,
                'target_element' => $familyExpense,
                'allow_add' => true,
                'allow_sub' => true,
                'should_create_template' => true,
                'should_wrap' => false,
                'label' => 'Despesas da família. Para '
                . ' adicionar cada despesa da família (conta de água, energia '
                . 'elétrica, internet, ...) utilize botão +. '
                . 'Você deverá preencher todos os campos criados. Se quiser '
                . 'retirar a última despesa adicionada utilize o botão -. '
                . 'O valor das despesas deve ser adicionado utilizando ponto'
                . 'ao invés de vírgula. Ex: R$ 1.520,19 ⟶ 1520.19. '
                . 'Ao menos uma despesa deverá ser preenchida.',
            ),
        ));

        $familyIncome = new FamilyIncomeExpenseFieldset($obj, FamilyIncomeExpenseFieldset::INCOME);

        $this->add(array(
            'type' => 'Zend\Form\Element\Collection',
            'name' => 'familyIncome',
            'options' => array(
                'count' => 1,
                'target_element' => $familyIncome,
                'allow_add' => true,
                'allow_sub' => true,
                'should_create_template' => true,
                'should_wrap' => false,
                'label' => 'Receitas da família. Para '
                . ' adicionar cada receita da família (salário, ganhos com '
                . 'locação de casa, pensão, ...) utilize botão +. '
                . 'Você deverá preencher todos os campos criados. Se quiser '
                . 'retirar a última receita adicionada utilize o botão -. '
                . 'O valor das receitas deve ser adicionado utilizando ponto'
                . 'ao invés de vírgula. Ex: R$ 2.580,59 ⟶ 2580.59. Ao menos uma receita deverá ser preenchida.',
            ),
        ));
        // VULNERABILIDADE


        $this->add([
            'name' => 'familyEthnicity',
            'type' => 'radio',
            'options' => [
                'label' => 'Você considera sua família:',
                'value_options' => PreInterview::getFamilyEthnicityArray(),
            ]
        ]);

        $familyHealth = new FamilyHealthFieldset($obj);

        $this->add([
            'type' => 'Zend\Form\Element\Collection',
            'name' => 'familyHealth',
            'options' => array(
                'count' => 0,
                'target_element' => $familyHealth,
                'allow_add' => true,
                'allow_sub' => true,
                'should_create_template' => true,
                'should_wrap' => false,
                'label' => 'Problemas de saúde de membros da família. Para '
                . ' adicionar cada membro da família com problemas de saúde '
                . 'utilize botão +. Você deverá preencher todos os campos '
                . 'criados. Se quiser retirar a última pessoa adicionada '
                . 'utilize o botão -.',
            ),
        ]);

        $familyGoods = new FamilyGoodFieldset($obj);

        $this->add([
            'type' => 'Zend\Form\Element\Collection',
            'name' => 'familyGoods',
            'options' => array(
                'count' => 1,
                'target_element' => $familyGoods,
                'allow_add' => true,
                'allow_sub' => true,
                'should_create_template' => true,
                'should_wrap' => false,
                'label' => 'Bens móveis. Para adicionar cada bem móvel da '
                . 'família utilize botão +. Você deverá preencher todos os '
                . 'campos criados. Se quiser retirar o último bem móvel '
                . 'adicionado utilize o botão -. O valor estimado do móvel '
                . 'deve ser adicionado utilizando ponto ao invés de vírgula. '
                . 'Ex: R$ 1.205,17 ⟶ 1205.17. Ao menos um bem móvel deverá ser preenchido.',
            ),
        ]);

        $familyProperties = new FamilyPropertyFieldset($obj);

        $this->add([
            'type' => 'Zend\Form\Element\Collection',
            'name' => 'familyProperties',
            'options' => array(
                'count' => 1,
                'target_element' => $familyProperties,
                'allow_add' => true,
                'allow_sub' => true,
                'should_create_template' => true,
                'should_wrap' => false,
                'label' => 'Bens imóveis (propriedades). Para adicionar cada '
                . 'imóvel da família utilize botão +. Você deverá preencher '
                . 'todos os campos criados. Se quiser retirar o último imóvel '
                . 'adicionado utilize o botão -. Ao menos um bem imóvel deverá ser preenchido.',
            ),
        ]);

        $candidateFamily = new CandidateFamilyFieldset($obj);
        $this->add(array(
            'type' => 'Zend\Form\Element\Collection',
            'name' => 'familyMembers',
            'options' => array(
                'count' => 1,
                'target_element' => $candidateFamily,
                'allow_add' => true,
                'allow_sub' => true,
                'should_create_template' => true,
                'should_wrap' => false,
                'label' => 'Membros da família. Para cada membro da'
                . ' família adicionado, botão +, você deverá preencher todos'
                . ' os campos criados. Se quiser retirar o último membro '
                . 'familiar adicionado utilize o botão -.',
            ),
        ));

        $this->add([
            'name' => 'addButton',
            'type' => 'button',
            'options' => [
                'label' => ' ',
                'glyphicon' => 'plus',
            ],
            'attributes' => [
                'class' => 'btn btn-flat btn-app bg-green add-button',
            ]
        ]);

        $this->add([
            'name' => 'delButton',
            'type' => 'button',
            'options' => [
                'label' => ' ',
                'glyphicon' => 'minus',
            ],
            'attributes' => [
                'class' => 'btn btn-flat btn-app bg-red del-button',
            ]
        ]);

        $this->add(array(
                'name' => 'elementarySchoolType',
                'type' => 'radio',
                'options' => array(
                    'label' => 'A Instituição de ensino na qual cursou o ensino fundamental é?',
                    'value_options' => PreInterview::getElementarySchoolTypeArray(),
                ),
                'inline' => false,
            ))
            ->add(array(
                'name' => 'highSchoolType',
                'type' => 'radio',
                'options' => array(
                    'label' => 'Você cursou/cursa o ensino médio em escola(s):',
                    'value_options' => PreInterview::getHighSchoolTypeArray(),
                )
            ))
            ->add([
                'name' => 'highSchoolAdmissionYear',
                'type' => 'number',
                'options' => [
                    'label' => 'Ano de ingresso no ensino médio?',
                ],
            ])
            ->add([
                'name' => 'highSchoolConclusionYear',
                'type' => 'number',
                'options' => [
                    'label' => 'Ano de conclusão/previsão de conclusão do ensino médio?',
                ],
            ])
            ->add([
                'name' => 'siblingsUndergraduate',
                'type' => 'radio',
                'options' => [
                    'label' => 'Tem irmãos que cursaram/cursam o ensino superior?',
                    'value_options' => [
                        false => 'Não',
                        true => 'Sim',
                    ],
                ],
            ])
            ->add([
                'name' => 'otherLanguages',
                'type' => 'textarea',
                'options' => [
                    'label' => 'Fala algum idioma estrangeiro? Se sim, como estudou? (Cursos preparatórios, por conta '
                    . 'própria, com amigos, ...)',
                ],
                'attributes' => [
                    'rows' => 3,
                ],
            ])
            ->add([
                'name' => 'homeStatus',
                'type' => 'radio',
                'options' => [
                    'label' => 'Imovel em que reside é?',
                    'value_options' => PreInterview::getHomeStatusArray(),
                ],
            ])
            ->add([
                'name' => 'homeDescription',
                'type' => 'radio',
                'options' => [
                    'label' => 'Marque a característica que melhor descreve a sua casa?',
                    'value_options' => PreInterview::getHomeDescriptionArray(),
                ],
            ])
            ->add([
                'name' => 'transport',
                'type' => 'radio',
                'options' => [
                    'label' => 'Transporte utilizado para comparecer às aulas:',
                    'value_options' => PreInterview::getTransportArray(),
                ],
            ])
            // PERFIL DE ESTUDANTE
            ->add([
                'name' => 'extraCourses',
                'type' => 'textarea',
                'options' => [
                    'label' => 'Fez algum curso extraclasse? Se sim, qual(is) curso(s)?',
                ],
                'attributes' => [
                    'rows' => 3,
                ],
            ])
            ->add([
                'name' => 'preparationCourse',
                'type' => 'textarea',
                'options' => [
                    'label' => 'já fez curso pré-vestibular? Se sim, qual(is) curso(s) pré-vestibular(es)?',
                ],
                'attributes' => [
                    'rows' => 3,
                ],
            ])
            ->add([
                'name' => 'entranceExam',
                'type' => 'textarea',
                'options' => [
                    'label' => 'Já prestou algum vestibular ou concurso? Se sim, qual(is) vestibular(es)?',
                ],
                'attributes' => [
                    'rows' => 3,
                ],
            ])
            ->add([
                'name' => 'undergraduateCourse',
                'type' => 'textarea',
                'options' => [
                    'label' => 'Já ingressou no ensino superior? Se sim, ainda cursa?',
                ],
                'attributes' => [
                    'rows' => 3,
                ],
            ])
            ->add([
                'name' => 'waitingForUs',
                'type' => 'textarea',
                'options' => [
                    'label' => 'O que espera de nós e o que pretende alcançar caso seja aprovado?',
                ],
                'attributes' => [
                    'rows' => 3,
                ],
            ])
            //SOCIOECONOMICO
            ->add(array(
                'name' => 'live',
                'type' => 'radio',
                'options' => array(
                    'label' => 'Você mora:',
                    'value_options' => PreInterview::getLiveArray(),
                )
            ))
            ->add(array(
                'name' => 'responsibleFinancial',
                'type' => 'radio',
                'options' => array(
                    'label' => 'Quem é(são) o(os) responsável(is) pela manutenção financeira do grupo familiar?',
                    'value_options' => PreInterview::getResponsibleFinancialArray(),
                )
            ))
            ->add(array(
                'name' => 'infrastructureElements',
                'type' => 'DoctrineModule\Form\Element\ObjectMultiCheckbox',
                'options' => array(
                    'label' => 'A casa onde mora tem (permite marcar mais de uma alternativa):',
                    'object_manager' => $obj,
                    'target_class' => 'Recruitment\Entity\InfrastructureElement',
                    'property' => 'infrastructureElementDescription',
                ),
            ))
            ->add(array(
                'name' => 'liveArea',
                'type' => 'radio',
                'options' => array(
                    'label' => 'Você reside em:',
                    'value_options' => PreInterview::getLiveAreaArray(),
                )
            ))
            ->add(array(
                'name' => 'itemTv',
                'type' => 'radio',
                'options' => array(
                    'label' => 'Tv',
                    'value_options' => $numberedOptions,
                    'inline' => true,
                )
            ))
            ->add(array(
                'name' => 'itemBathroom',
                'type' => 'radio',
                'options' => array(
                    'label' => 'Banheiro',
                    'value_options' => $numberedOptions,
                    'inline' => true,
                )
            ))
            ->add(array(
                'name' => 'itemSalariedHousekeeper',
                'type' => 'radio',
                'options' => array(
                    'label' => 'empregada mensalista',
                    'value_options' => $numberedOptions,
                    'inline' => true,
                )
            ))
            ->add(array(
                'name' => 'itemDailyHousekeeper',
                'type' => 'radio',
                'options' => array(
                    'label' => 'empregada diarista',
                    'value_options' => $numberedOptions,
                    'inline' => true,
                )
            ))
            ->add(array(
                'name' => 'itemWashingMachine',
                'type' => 'radio',
                'options' => array(
                    'label' => 'máquina de lavar roupa',
                    'value_options' => $numberedOptions,
                    'inline' => true,
                )
            ))
            ->add(array(
                'name' => 'itemRefrigerator',
                'type' => 'radio',
                'options' => array(
                    'label' => 'geladeira',
                    'value_options' => $numberedOptions,
                    'inline' => true,
                )
            ))
            ->add(array(
                'name' => 'itemCableTv',
                'type' => 'radio',
                'options' => array(
                    'label' => 'TV a cabo',
                    'value_options' => $numberedOptions,
                    'inline' => true,
                )
            ))
            ->add(array(
                'name' => 'itemComputer',
                'type' => 'radio',
                'options' => array(
                    'label' => 'computador',
                    'value_options' => $numberedOptions,
                    'inline' => true,
                )
            ))
            ->add(array(
                'name' => 'itemSmartphone',
                'type' => 'radio',
                'options' => array(
                    'label' => 'smartphones',
                    'value_options' => $numberedOptions,
                    'inline' => true,
                )
            ))
            ->add(array(
                'name' => 'itemBedroom',
                'type' => 'radio',
                'options' => array(
                    'label' => 'quartos',
                    'value_options' => $numberedOptions,
                    'inline' => true,
                )
            ))
            ->add([
                'name' => 'moreInformation',
                'type' => 'textarea',
                'options' => [
                    'label' => 'Informe ou esclareça sobre dados não contemplados neste formulário ou situações '
                    . 'especiais que julgar conveniente',
                ],
                'attributes' => [
                    'rows' => 6,
                ]
            ])
        ;
    }

    public function getInputFilterSpecification()
    {
        return [
            'familyEthnicity' => [
                'required' => true,
            ],
            'elementarySchoolType' => [
                'required' => true,
            ],
            'highSchoolType' => [
                'required' => true,
            ],
            'highSchoolAdmissionYear' => [
                'required' => true,
            ],
            'highSchoolConclusionYear' => [
                'required' => true,
            ],
            'siblingsUndergraduate' => [
                'required' => true,
            ],
            'otherLanguages' => [
                'required' => false,
            ],
            'homeStatus' => [
                'required' => true,
            ],
            'homeDescription' => [
                'required' => true,
            ],
            'transport' => [
                'required' => true,
            ],
            'transport' => [
                'required' => true,
            ],
            'extraCourses' => [
                'required' => false,
            ],
            'preparationCourse' => [
                'required' => false,
            ],
            'entranceExam' => [
                'required' => false,
            ],
            'undergraduateCourse' => [
                'required' => false,
            ],
            'waitingForUs' => [
                'required' => true,
            ],
            'live' => [
                'required' => true,
            ],
            'responsibleFinancial' => [
                'required' => true,
            ],
            'infrastructureElements' => [
                'required' => false,
            ],
            'liveArea' => [
                'required' => true,
            ],
            'itemTv' => [
                'required' => true,
            ],
            'itemBathroom' => [
                'required' => true,
            ],
            'itemSalariedHousekeeper' => [
                'required' => true,
            ],
            'itemDailyHousekeeper' => [
                'required' => true,
            ],
            'itemWashingMachine' => [
                'required' => true,
            ],
            'itemRefrigerator' => [
                'required' => true,
            ],
            'itemCableTv' => [
                'required' => true,
            ],
            'itemComputer' => [
                'required' => true,
            ],
            'itemSmartphone' => [
                'required' => true,
            ],
            'itemBedroom' => [
                'required' => true,
            ],
            'moreInformation' => [
                'required' => false,
            ],
        ];
    }

    protected static function getYears()
    {
        $year = date('Y') + 2;
        $options = [];
        for ($i = 1; $i < 51; $i++) {
            $options[$year] = $year--;
        }
        return $options;
    }
}
