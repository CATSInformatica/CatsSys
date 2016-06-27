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
                'name' => 'interviewBeginDate',
                'type' => 'text',
                'options' => [
                    'label' => 'Início da entrevista',
                    'add-on-prepend' => '<i class="fa fa-clock-o"></i>',
                ],
                'attributes' => [
                    'id' => 'interview-begin-date', // para o datetimepicker,
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
                'name' => 'interviewerCommentSocioeconomic',
                'type' => 'textarea',
                'options' => [
                    'label' => 'Comentários e visões do entrevistador',
                ],
                'attributes' => [
                    'rows' => 10,
                ]
            ])
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
                'name' => 'interviewerCommentEvalSocioec',
                'type' => 'textarea',
                'options' => [
                    'label' => 'Comentários sobre a avaliação socioeconômica',
                ],
                'attributes' => [
                    'rows' => 5,
                ]
            ])
            ->add([
                'name' => 'interviewFamEthnicity',
                'type' => 'checkbox',
                'options' => [
                    'label' => 'Família negra, parda ou indígena',
                    'use_hidden_element' => true,
                    'checked_value' => StudentInterview::FAM_ETHNICITY_NOCAUCASIAN,
                    'unchecked_value' => StudentInterview::FAM_ETHNICITY_CAUCASIAN,
                ],
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
                    'label' => 'Família negra, parda ou indígena',
                    'use_hidden_element' => true,
                    'checked_value' => StudentInterview::HIGHSCHOOL_PUBLIC_YES,
                    'unchecked_value' => StudentInterview::HIGHSCHOOL_PUBLIC_NO,
                ],
            ])
            ->add([
                'name' => 'interviewFamSupport',
                'type' => 'checkbox',
                'options' => [
                    'label' => 'Falta de apoio da família nos estudos',
                    'use_hidden_element' => true,
                    'checked_value' => StudentInterview::FAMILYSUPPORT_YES,
                    'unchecked_value' => StudentInterview::FAMILYSUPPORT_NO,
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
                    'label' => 'Família depende de terceiros',
                    'use_hidden_element' => true,
                    'checked_value' => StudentInterview::FAMNEEDTOWORK_YES,
                    'unchecked_value' => StudentInterview::FAMNEEDTOWORK_NO,
                ],
            ])
            ->add([
                'name' => 'interviewSingleton',
                'type' => 'checkbox',
                'options' => [
                    'label' => 'Precisa trabalhar',
                    'use_hidden_element' => true,
                    'checked_value' => StudentInterview::SINGLETON_YES,
                    'unchecked_value' => StudentInterview::SINGLETON_NO,
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
                ]
            ])
            ->add([
                'name' => 'interviewStudentQuestion',
                'type' => 'radio',
                'options' => [
                    'label' => 'Qual o perfil do estudante?',
                    'value_options' => StudentInterview::getInterviewStudentVulnerabilityArray(),
                ],
            ])
            ->add([
                'name' => 'interviewStudentGrade',
                'type' => 'number',
                'options' => [
                    'label' => 'Nota no critério perfil de estudante',
                ]
            ])
            ->add([
                'name' => 'interviewerGeneralComment',
                'type' => 'textarea',
                'options' => [
                    'label' => 'Comentários gerais do entrevistador',
                ],
                'attributes' => [
                    'rows' => 5,
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
            'interviewBeginDate' => [
                'required' => true,
            ],
            'interviewers' => [
                'required' => true,
            ],
            'interviewerCommentIntro' => [
                'required' => false,
            ],
            'interviewerCommentSocioeconomic' => [
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
            'interviewerCommentEvalSocioec' => [
                'required' => false,
            ],
            'interviewFamEthnicity' => [
                'required' => false,
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
            'interviewStudentQuestion' => [
                'required' => true,
            ],
            'interviewStudentGrade' => [
                'required' => true,
            ],
            'interviewerGeneralComment' => [
                'required' => true,
            ],
        ];
    }
}
