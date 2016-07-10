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
                    'label' => 'Situação da casa e localização',
                ],
                'attributes' => [
                    'rows' => 2,
                ]
            ])
            ->add([
                'name' => 'interviewExpComm',
                'type' => 'textarea',
                'options' => [
                    'label' => 'Bens e despesas básicas',
                ],
                'attributes' => [
                    'rows' => 2,
                ]
            ])
            ->add([
                'name' => 'interviewFamIncComm',
                'type' => 'textarea',
                'options' => [
                    'label' => 'Membros da família e renda',
                ],
                'attributes' => [
                    'rows' => 2,
                ]
            ])
            ->add([
                'name' => 'interviewFamProbComm',
                'type' => 'textarea',
                'options' => [
                    'label' => 'Problemas com os membros (Procure por vícios, drogas. Doenças graves ou crônicas.)',
                ],
                'attributes' => [
                    'rows' => 2,
                ]
            ])
            ->add([
                'name' => 'interviewFamSuppComm',
                'type' => 'textarea',
                'options' => [
                    'label' => 'Membros da família e sua relação e pensamento sobre os estudos/trabalho',
                ],
                'attributes' => [
                    'rows' => 2,
                ]
            ])
            ->add([
                'name' => 'interviewRoutComm',
                'type' => 'textarea',
                'options' => [
                    'label' => 'Trabalhos do candidato e rotina atual (atividades e hábitos)',
                ],
                'attributes' => [
                    'rows' => 2,
                ]
            ])
            ->add([
                'name' => 'interviewStudBehaComm',
                'type' => 'textarea',
                'options' => [
                    'label' => 'Histórico escolar e comportamento como aluno',
                ],
                'attributes' => [
                    'rows' => 2,
                ]
            ])
            ->add([
                'name' => 'interviewCoursComm',
                'type' => 'textarea',
                'options' => [
                    'label' => 'Cursos técnicos, profissionalizantes, de idioma etc',
                ],
                'attributes' => [
                    'rows' => 2,
                ]
            ])
            ->add([
                'name' => 'interviewStudWayComm',
                'type' => 'textarea',
                'options' => [
                    'label' => 'Rotina de estudos e melhores formas de estudar (horas por semana, agenda, estudar por tarefas)',
                ],
                'attributes' => [
                    'rows' => 2,
                ]
            ])
            ->add([
                'name' => 'interviewStudExpComm',
                'type' => 'textarea',
                'options' => [
                    'label' => 'Verifique se o candidato já fez simulados, '
                    . 'vestibulares e concursos...',
                ],
                'attributes' => [
                    'rows' => 2,
                ]
            ])
            ->add([
                'name' => 'interviewerCommentStudent',
                'type' => 'textarea',
                'options' => [
                    'label' => 'Comentários e visões do entrevistador (sobre os objetivos do candidato)',
                ],
                'attributes' => [
                    'rows' => 10,
                ]
            ])
            ->add([
                'name' => 'interviewerOurActivities',
                'type' => 'textarea',
                'options' => [
                    'label' => 'Comentários e visões do entrevistador (sobre nossas atividades)',
                ],
                'attributes' => [
                    'rows' => 10,
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
                    'label' => 'Qual a maior escolaridade registrada entre os membros da família?',
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
                    'label' => 'Maior nível ocupacional dentre os membros da família?',
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
                'name' => 'interviewStudentVulnerability',
                'type' => 'radio',
                'options' => [
                    'label' => 'Em que perfil de vulnerabilidade o candidato se encaixa?',
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
            'interviewTotalIncome' => [
                'required' => true,
            ],
            'interviewNumberOfFamilyMembers' => [
                'required' => true,
            ],
            'interviewMaxScholarity' => [
                'required' => true,
            ],
            'interviewTotalIncome' => [
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
