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
use Recruitment\Entity\StudentInterview;
use Zend\Form\Fieldset;
use Zend\InputFilter\InputFilterProviderInterface;
use DoctrineModule\Stdlib\Hydrator\DoctrineObject as DoctrineHydrator;

/**
 * Contém campos para a entidade StudentInterview.
 *
 * @author Márcio Dias <marciojr91@gmail.com>
 */
class StudentInterviewFieldset extends Fieldset implements InputFilterProviderInterface
{

    public function __construct(ObjectManager $obj)
    {
        parent::__construct('studentInterview');

        $this
            ->setHydrator(new DoctrineHydrator($obj))
            ->setObject(new StudentInterview());

        $this
            ->add([
                'name' => 'interviewStartTime',
                'type' => 'text',
                'options' => [
                    'label' => 'Início da entrevista',
                    'add-on-prepend' => '<i class="fa fa-clock-o"></i>',
                ],
                'attributes' => [
                    'class' => 'interview-time', // para o datetimepicker,
                ]
            ])
            ->add([
                'name' => 'interviewEndTime',
                'type' => 'text',
                'options' => [
                    'label' => 'Término da entrevista',
                    'add-on-prepend' => '<i class="fa fa-clock-o"></i>',
                ],
                'attributes' => [
                    'class' => 'interview-time', // para o datetimepicker,
                ]
            ])
            ->add([
                'name' => 'interviewers',
                'type' => 'text',
                'options' => [
                    'label' => 'Entrevistadores (separados por ";")',
                ],
            ])
            ->add([
                'name' => 'interviewerCommentIntro',
                'type' => 'textarea',
                'options' => [
                    'label' => 'Comentários e visões do entrevistador',
                ],
                'attributes' => [
                    'rows' => 10,
                ]
            ])
            ->add([
                'name' => 'interviewHomeSitComm',
                'type' => 'textarea',
                'options' => [
                    'label' => 'Como é a sua casa no que diz respeito a infraestrutura, acabamento e localização? Casa própria, cedida, alugada ou financiada?',
                ],
                'attributes' => [
                    'rows' => 2,
                ]
            ])
            ->add([
                'name' => 'interviewHomeSitCommGrade',
                'type' => 'radio',
                'options' => [
                    'label' => 'Dê uma nota de 1 a 5 para sua casa.',
                    'value_options' => StudentInterview::getInterviewHomeSitCommGradeArray(),
                ],
            ])
            ->add([
                'name' => 'interviewExpComm',
                'type' => 'textarea',
                'options' => [
                    'label' => 'Quais são os bens? Quais são as despesas? (reforçar o que foi visto na pré-entrevista e
investigar outros possíveis bens/despesas)',
                ],
                'attributes' => [
                    'rows' => 2,
                ]
            ])
            ->add([
                'name' => 'interviewFamIncComm',
                'type' => 'textarea',
                'options' => [
                    'label' => 'Quem mora na sua casa? O que cada pessoa faz? (estuda, trabalha etc)',
                ],
                'attributes' => [
                    'rows' => 2,
                ]
            ])
            ->add([
                'name' => 'interviewFamIncCommInc',
                'type' => 'textarea',
                'options' => [
                    'label' => 'Dessas pessoas, quem contribui com a renda? Alguém de fora contribui com a renda? (Ex.: avós, tios, etc)',
                ],
                'attributes' => [
                    'rows' => 2,
                ]
            ])
            ->add([
                'name' => 'interviewFamProbComm',
                'type' => 'textarea',
                'options' => [
                    'label' => 'Tem algum problema familiar? (Problema com vícios, drogas, doenças, violência etc)',
                ],
                'attributes' => [
                    'rows' => 2,
                ]
            ])
            ->add([
                'name' => 'interviewFamSuppComm',
                'type' => 'textarea',
                'options' => [
                    'label' => 'Como é sua relação com sua família? Eles te apoiam a estudar? Tem pressão para trabalhar?',
                ],
                'attributes' => [
                    'rows' => 2,
                ]
            ])
            ->add([
                'name' => 'interviewRoutComm',
                'type' => 'textarea',
                'options' => [
                    'label' => 'Você não tem condição de pagar um cursinho? (Levar em conta cada caso para fazer essa pergunta).',
                ],
                'attributes' => [
                    'rows' => 2,
                ]
            ])
            ->add([
                'name' => 'interviewSecondarySchool',
                'type' => 'textarea',
                'options' => [
                    'label' => 'Onde cursou o Ensino Fundamental e o Ensino Médio? Era escola particular ou pública? (Se for particular, questionar se tinha bolsa)',
                ],
                'attributes' => [
                    'rows' => 2,
                ]
            ])
            ->add([
                'name' => 'interviewStudBehaComm',
                'type' => 'textarea',
                'options' => [
                    'label' => 'Como era seu comportamento como aluno?',
                ],
                'attributes' => [
                    'rows' => 2,
                ]
            ])
            ->add([
                'name' => 'interviewCoursComm',
                'type' => 'textarea',
                'options' => [
                    'label' => 'Você já fez algum cursinho pré-vestibular ou já ingressou no ensino superior?',
                ],
                'attributes' => [
                    'rows' => 2,
                ]
            ])
            ->add([
                'name' => 'interviewStudWayComm',
                'type' => 'textarea',
                'options' => [
                    'label' => 'Você já fez cursos extracurriculares? (curso técnico, curso de idiomas, etc).',
                ],
                'attributes' => [
                    'rows' => 2,
                ]
            ])
            ->add([
                'name' => 'interviewStudExpComm',
                'type' => 'textarea',
                'options' => [
                    'label' => 'Você já fez simulados, vestibulares ou ENEM?',
                ],
                'attributes' => [
                    'rows' => 2,
                ]
            ])
            // adicionadas 12/2019
            ->add([
                'name' => 'interviewStudWhichCourse',
                'type' => 'textarea',
                'options' => [
                    'label' => 'Já tem algum curso em mente? Alguma faculdade específica?',
                ],
                'attributes' => [
                    'rows' => 2,
                ]
            ])
            ->add([
                'name' => 'interviewStudWhyStd',
                'type' => 'textarea',
                'options' => [
                    'label' => 'Por que quer ingressar no ensino superior? Qual a importância disso para você?',
                ],
                'attributes' => [
                    'rows' => 2,
                ]
            ])
            ->add([
                'name' => 'interviewStudRoutine',
                'type' => 'textarea',
                'options' => [
                    'label' => 'Como é o seu dia (rotina)? Tem algum momento reservado para os estudos? Se sim, utiliza cronogramas ou algum método específico?',
                ],
                'attributes' => [
                    'rows' => 2,
                ]
            ])
            ->add([
                'name' => 'interviewStudRoutineEnough',
                'type' => 'textarea',
                'options' => [
                    'label' => 'Acha que a rotina de estudos atual é suficiente para alcançar seus objetivos? Se não, o que você acha que precisa fazer para melhorar?',
                ],
                'attributes' => [
                    'rows' => 2,
                ]
            ])
            // /adicionadas 12/2019
            ->add([
                'name' => 'interviewerCommentStudent',
                'type' => 'textarea',
                'options' => [
                    'label' => 'Comentários e visões do entrevistador',
                ],
                'attributes' => [
                    'rows' => 10,
                ]
            ])
            ->add([
                'name' => 'interviewerOurActivities',
                'type' => 'textarea',
                'options' => [
                    'label' => 'Comentários e visões do entrevistador',
                ],
                'attributes' => [
                    'rows' => 10,
                ]
            ])
            ->add([
                'name' => 'interviewBasicSalary',
                'type' => 'number',
                'options' => [
                    'label' => 'Valor do Salário Mínimo',
                ],
                'attributes' => [
                    'value' => 937,
                    'id' => 'minimumSalary',
                ]
            ])
            ->add([
                'name' => 'interviewTotalIncome',
                'type' => 'radio',
                'options' => [
                    'label' => 'Total de rendimentos',
                    'value_options' => StudentInterview::getInterviewTotalIncomeArray(),
                ],
            ])
            ->add([
                'name' => 'interviewNumberOfFamilyMembers',
                'type' => 'radio',
                'options' => [
                    'label' => 'Quantidade de membros residentes na família?',
                    'value_options' => StudentInterview::getInterviewNumberOfFamilyMembersArray(),
                ],
            ])
            ->add([
                'name' => 'interviewMaxScholarity',
                'type' => 'radio',
                'options' => [
                    'label' => 'Qual a escolaridade do provedor da família?',
                    'value_options' => StudentInterview::getInterviewMaxScholarityArray(),
                ],
            ])
            ->add([
                'name' => 'interviewHomeType',
                'type' => 'radio',
                'options' => [
                    'label' => 'Qual é a situação da casa em que vive o candidato?',
                    'value_options' => StudentInterview::getInterviewHomeTypeArray(),
                ],
            ])
            ->add([
                'name' => 'interviewHomeSituation',
                'type' => 'radio',
                'options' => [
                    'label' => 'Avaliando o tipo, modalidade, acomodações, localização e '
                    . 'infra-estrutura. Qual item descreve melhor a casa do candidato.',
                    'value_options' => StudentInterview::getInterviewHomeSituationArray(),
                ],
            ])
            ->add([
                'name' => 'interviewMaxPosition',
                'type' => 'radio',
                'options' => [
                    'label' => 'Qual o nível ocupacional do provedor da família?',
                    'value_options' => StudentInterview::getInterviewMaxPositionArray(),
                ],
            ])
            ->add([
                'name' => 'interviewerSocioecGradeJustification',
                'type' => 'textarea',
                'options' => [
                    'label' => 'Justificativa para a nota no critério Socioeconômico',
                ],
                'attributes' => [
                    'rows' => 9,
                ]
            ])
            ->add([
                'name' => 'interviewFamilyProvider',
                'type' => 'checkbox',
                'options' => [
                    'label' => 'Provedor da família',
                    'use_hidden_element' => true,
                    'checked_value' => StudentInterview::FAM_PROVIDER_YES,
                    'unchecked_value' => StudentInterview::FAM_PROVIDER_NO,
                ],
            ])
            ->add([
                'name' => 'interviewHasChildren',
                'type' => 'checkbox',
                'options' => [
                    'label' => 'Tem filhos',
                    'use_hidden_element' => true,
                    'checked_value' => StudentInterview::HASCHILDREN_YES,
                    'unchecked_value' => StudentInterview::HASCHILDREN_NO,
                ],
            ])
            ->add([
                'name' => 'interviewHasDisease',
                'type' => 'checkbox',
                'options' => [
                    'label' => 'Doenças incapacitantes na família',
                    'use_hidden_element' => true,
                    'checked_value' => StudentInterview::HIGHSCHOOL_PUBLIC_YES,
                    'unchecked_value' => StudentInterview::HIGHSCHOOL_PUBLIC_NO,
                ],
            ])
            ->add([
                'name' => 'interviewHighSchool',
                'type' => 'checkbox',
                'options' => [
                    'label' => 'Ensino médio em escola pública',
                    'use_hidden_element' => true,
                    'checked_value' => StudentInterview::HIGHSCHOOL_PUBLIC_YES,
                    'unchecked_value' => StudentInterview::HIGHSCHOOL_PUBLIC_NO,
                ],
            ])
            ->add([
                'name' => 'interviewFamSupport',
                'type' => 'checkbox',
                'options' => [
                    'label' => 'Falta de apoio da família para estudar',
                    'use_hidden_element' => true,
                    'checked_value' => StudentInterview::FAMILYSUPPORT_NO,
                    'unchecked_value' => StudentInterview::FAMILYSUPPORT_YES,
                ],
            ])
            ->add([
                'name' => 'interviewFamDependency',
                'type' => 'checkbox',
                'options' => [
                    'label' => 'Família depende de terceiros',
                    'use_hidden_element' => true,
                    'checked_value' => StudentInterview::FAMDEPENDENCY_YES,
                    'unchecked_value' => StudentInterview::FAMDEPENDENCY_NO,
                ],
            ])
            ->add([
                'name' => 'intervewNeedToWork',
                'type' => 'checkbox',
                'options' => [
                    'label' => 'Precisar trabalhar para auxiliar nos rendimentos da família',
                    'use_hidden_element' => true,
                    'checked_value' => StudentInterview::FAMNEEDTOWORK_YES,
                    'unchecked_value' => StudentInterview::FAMNEEDTOWORK_NO,
                ],
            ])
            ->add([
                'name' => 'interviewSingleton',
                'type' => 'checkbox',
                'options' => [
                    'label' => 'Tem irmãos',
                    'use_hidden_element' => true,
                    'checked_value' => StudentInterview::SINGLETON_NO,
                    'unchecked_value' => StudentInterview::SINGLETON_YES,
                ],
            ])
            ->add([
                'name' => 'intervewFamilyPropAndGoods',
                'type' => 'checkbox',
                'options' => [
                    'label' => 'Possui somente imóveis/móveis necessários ao cotidiano',
                    'use_hidden_element' => true,
                    'checked_value' => StudentInterview::FAMILYPROPANDGOODS_JUSTNEEDED_YES,
                    'unchecked_value' => StudentInterview::FAMILYPROPANDGOODS_JUSTNEEDED_NO,
                ],
            ])
            ->add([
                'name' => 'intervewUnemploymentInFamily',
                'type' => 'checkbox',
                'options' => [
                    'label' => 'Desemprego recente na família',
                    'use_hidden_element' => true,
                    'checked_value' => StudentInterview::UNEMPLOYMENT_IN_FAMILY_YES,
                    'unchecked_value' => StudentInterview::UNEMPLOYMENT_IN_FAMILY_NO,
                ],
            ])
            ->add([
                'name' => 'intervewChemicalDependency',
                'type' => 'checkbox',
                'options' => [
                    'label' => 'Dependência química',
                    'use_hidden_element' => true,
                    'checked_value' => StudentInterview::CHEMICAL_DEPENDENCY_YES,
                    'unchecked_value' => StudentInterview::CHEMICAL_DEPENDENCY_NO,
                ],
            ])
            ->add([
                'name' => 'interviewStudentVulnerability',
                'type' => 'radio',
                'options' => [
                    'label' => 'Em que perfil de vulnerabilidade o candidato se encaixa? (lembre-se que renda é avaliado como socioeconômico e não vulnerabilidade)',
                    'value_options' => StudentInterview::getInterviewStudentVulnerabilityArray(),
                ],
            ])
            ->add([
                'name' => 'interviewVulnerabilityGrade',
                'type' => 'number',
                'options' => [
                    'label' => 'Nota no critério vulnerabilidade',
                ],
                'attributes' => [
                    'step' => 'any',
                ]
            ])
            ->add([
                'name' => 'interviewerVulnerabilityGradeJustification',
                'type' => 'textarea',
                'options' => [
                    'label' => 'Justificativa para a nota no critério Vulnerabilidade',
                ],
                'attributes' => [
                    'rows' => 9,
                ]
            ])
            ->add([
                'name' => 'interviewStudentQuestion',
                'type' => 'radio',
                'options' => [
                    'label' => 'Qual o perfil do estudante?',
                    'value_options' => StudentInterview::getInterviewStudentQuestionArray(),
                ],
            ])
            ->add([
                'name' => 'interviewStudentGrade',
                'type' => 'number',
                'options' => [
                    'label' => 'Nota no critério perfil de estudante',
                ],
                'attributes' => [
                    'step' => 'any',
                ]
            ])
            ->add([
                'name' => 'interviewerStudentGradeJustification',
                'type' => 'textarea',
                'options' => [
                    'label' => 'Justificativa para a nota no critério perfil de estudante',
                ],
                'attributes' => [
                    'rows' => 9,
                ]
            ])
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function getInputFilterSpecification()
    {
        return [
            'interviewStartTime' => [
                'required' => true,
            ],
            'interviewers' => [
                'required' => true,
            ],
            'interviewerCommentIntro' => [
                'required' => false,
            ],
            'interviewHomeSitComm' => [
                'required' => false,
            ],
            'interviewExpComm' => [
                'required' => false,
            ],
            'interviewFamIncComm' => [
                'required' => false,
            ],
            'interviewFamProbComm' => [
                'required' => false,
            ],
            'interviewFamSuppComm' => [
                'required' => false,
            ],
            'interviewRoutComm' => [
                'required' => false,
            ],
            'interviewSecondarySchool' => [
                'required' => true,
            ],
            'interviewStudBehaComm' => [
                'required' => false,
            ],
            'interviewCoursComm' => [
                'required' => false,
            ],
            'interviewStudWayComm' => [
                'required' => false,
            ],
            'interviewStudExpComm' => [
                'required' => false,
            ],
            'interviewerCommentStudent' => [
                'required' => false,
            ],
            'interviewBasicSalary' => [
                'required' => false,
            ],
            'interviewTotalIncome' => [
                'required' => true,
            ],
            'interviewNumberOfFamilyMembers' => [
                'required' => true,
            ],
            'interviewMaxScholarity' => [
                'required' => true,
            ],
            'interviewHomeType' => [
                'required' => true,
            ],
            'interviewHomeSituation' => [
                'required' => true,
            ],
            'interviewMaxPosition' => [
                'required' => true,
            ],
            'interviewerSocioecGradeJustification' => [
                'required' => true,
            ],
            'interviewFamilyProvider' => [
                'required' => false,
            ],
            'interviewHasChildren' => [
                'required' => false,
            ],
            'interviewHasDisease' => [
                'required' => false,
            ],
            'interviewHighSchool' => [
                'required' => false,
            ],
            'interviewFamSupport' => [
                'required' => false,
            ],
            'interviewFamDependency' => [
                'required' => false,
            ],
            'intervewNeedToWork' => [
                'required' => false,
            ],
            'interviewSingleton' => [
                'required' => false,
            ],
            'intervewFamilyPropAndGoods' => [
                'required' => false,
            ],
            'intervewUnemploymentInFamily' => [
                'required' => false,
            ],
            'intervewChemicalDependency' => [
                'required' => false,
            ],
            'interviewStudentVulnerability' => [
                'required' => true,
            ],
            'interviewVulnerabilityGrade' => [
                'required' => true,
            ],
            'interviewerVulnerabilityGradeJustification' => [
                'required' => true,
            ],
            'interviewStudentQuestion' => [
                'required' => true,
            ],
            'interviewStudentGrade' => [
                'required' => true,
            ],
            'interviewerStudentGradeJustification' => [
                'required' => true,
            ],
        ];
    }
}
