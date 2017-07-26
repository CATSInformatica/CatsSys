<?php

/*
 * Copyright (C) 2017 Gabriel Pereira <rickardch@gmail.com>
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
use Recruitment\Entity\InterviewerEvaluation;
use Recruitment\Entity\VolunteerInterview;
use Zend\Form\Fieldset;
use Zend\InputFilter\InputFilterProviderInterface;

/**
 * Description of InterviewerEvaluationFieldset
 *
 * @author Gabriel Pereira <rickardch@gmail.com>
 */
class InterviewerEvaluationFieldset extends Fieldset implements InputFilterProviderInterface
{
    
    public function __construct(ObjectManager $obj, VolunteerInterview $interview)
    {
        parent::__construct('interviewerEvaluationFieldset');

        $this->setHydrator(new DoctrineHydrator($obj))
            ->setObject(new InterviewerEvaluation());
        
        $ratingMax = InterviewerEvaluation::RATING_MAX;
        $ratingMin = InterviewerEvaluation::RATING_MIN;
        $ratingStep = InterviewerEvaluation::RATING_STEP;
        $ratingDefault = (int)(InterviewerEvaluation::RATING_MAX / 2);
        $this
            ->add(array(
                'name' => 'interviewerName',
                'type' => 'select',
                'options' => array(
                    'label' => 'Entrevistador',
                    'empty_option' => 'Escolha o entrevistador',
                    'value_options' => $this->getInterviewerOptions($interview->getInterviewers()),
                ),
                'attributes' => array(
                    'id' => 'interviewer-select',
                    'data-reg-id' => $interview->getRegistration()->getRegistrationId()
                )
            ))
            ->add(array(
                'name' => 'volunteerProfileRating',
                'type' => 'text',
                'options' => array(
                    'label' => 'CRITÉRIO 1 - PERFIL COMO VOLUNTÁRIO',
                ),
                'attributes' => array(
                    'class' => 'input-slider',
                    'data-min' => $ratingMin,
                    'data-max' => $ratingMax,
                    'data-step' => $ratingStep,
                    'data-default' => $ratingDefault 
                ),
            ))
            ->add(array(
                'name' => 'volunteerProfile',
                'type' => 'textarea',
                'options' => array(
                    'label' => 'Justificativa para a nota no critério 1',
                ),
                'attributes' => array(
                    'rows' => 6,
                ),
            ))
            ->add(array(
                'name' => 'volunteerAvailabilityRating',
                'type' => 'text',
                'options' => array(
                    'label' => 'CRITÉRIO 2 - DISPONIBILIDADE',
                ),
                'attributes' => array(
                    'class' => 'input-slider',
                    'data-min' => $ratingMin,
                    'data-max' => $ratingMax,
                    'data-step' => $ratingStep,
                    'data-default' => $ratingDefault 
                ),
            ))
            ->add(array(
                'name' => 'volunteerAvailability',
                'type' => 'textarea',
                'options' => array(
                    'label' => 'Justificativa para a nota no critério 2',
                ),
                'attributes' => array(
                    'rows' => 6,
                ),
            ))
            ->add(array(
                'name' => 'volunteerResponsabilityAndCommitmentRating',
                'type' => 'text',
                'options' => array(
                    'label' => 'CRITÉRIO 3 - RESPONSABILIDADE E COMPROMETIMENTO',
                ),
                'attributes' => array(
                    'class' => 'input-slider',
                    'data-min' => $ratingMin,
                    'data-max' => $ratingMax,
                    'data-step' => $ratingStep,
                    'data-default' => $ratingDefault 
                ),
            ))
            ->add(array(
                'name' => 'volunteerResponsabilityAndCommitment',
                'type' => 'textarea',
                'options' => array(
                    'label' => 'Justificativa para a nota no critério 3',
                ),
                'attributes' => array(
                    'rows' => 6,
                ),
            ))
            ->add(array(
                'name' => 'volunteerOverallRating',
                'type' => 'text',
                'options' => array(
                    'label' => 'Parecer do entrevistador',
                ),
                'attributes' => array(
                    'class' => 'input-slider',
                    'data-min' => $ratingMin,
                    'data-max' => $ratingMax,
                    'data-step' => $ratingStep,
                    'data-default' => $ratingDefault 
                ),
            ))
            ->add(array(
                'name' => 'volunteerOverallRemarks',
                'type' => 'textarea',
                'options' => array(
                    'label' => 'Justificativa para o parecer',
                ),
                'attributes' => array(
                    'rows' => 6,
                ),
            ))
        ;
    }

    public function getInputFilterSpecification()
    {
        $ratingMax = InterviewerEvaluation::RATING_MAX;
        $ratingMin = InterviewerEvaluation::RATING_MIN;
        
        return array(
            'interviewerName' => array(
                'required' => true
            ),
            'volunteerProfileRating' => array(
                'required' => true,
                'validator' => array(
                    array(
                        'name' => 'Digits'
                    ),
                    array(
                        'name' => 'Between', 
                        'options' => array(
                            'min' => $ratingMin,
                            'max' => $ratingMax,
                            'inclusive' => true                            
                        )
                    ),
                ),
            ),
            'volunteerProfile' => array(
                'required' => true,
                'filters' => array(
                    array('name' => 'StringTrim'),
                    array('name' => 'StripTags'),
                ),
                'validators' => array(
                    array(
                        'name' => 'StringLength',
                        'options' => array(
                            'min' => 3,
                            'max' => 500,
                        ),
                    ),
                ),
            ),
            'volunteerAvailabilityRating' => array(
                'required' => true,
                'validator' => array(
                    array(
                        'name' => 'Digits'
                    ),
                    array(
                        'name' => 'Between', 
                        'options' => array(
                            'min' => $ratingMin,
                            'max' => $ratingMax,
                            'inclusive' => true                            
                        )
                    ),
                ),
            ),
            'volunteerAvailability' => array(
                'required' => true,
                'filters' => array(
                    array('name' => 'StringTrim'),
                    array('name' => 'StripTags'),
                ),
                'validators' => array(
                    array(
                        'name' => 'StringLength',
                        'options' => array(
                            'min' => 3,
                            'max' => 500,
                        ),
                    ),
                ),
            ),
            'volunteerResponsabilityAndCommitmentRating' => array(
                'required' => true,
                'validator' => array(
                    array(
                        'name' => 'Digits'
                    ),
                    array(
                        'name' => 'Between', 
                        'options' => array(
                            'min' => $ratingMin,
                            'max' => $ratingMax,
                            'inclusive' => true                            
                        )
                    ),
                ),
            ),
            'volunteerResponsabilityAndCommitment' => array(
                'required' => true,
                'filters' => array(
                    array('name' => 'StringTrim'),
                    array('name' => 'StripTags'),
                ),
                'validators' => array(
                    array(
                        'name' => 'StringLength',
                        'options' => array(
                            'min' => 3,
                            'max' => 500,
                        ),
                    ),
                ),
            ),
            'volunteerOverallRating' => array(
                'required' => true,
                'validator' => array(
                    array(
                        'name' => 'Digits'
                    ),
                    array(
                        'name' => 'Between', 
                        'options' => array(
                            'min' => $ratingMin,
                            'max' => $ratingMax,
                            'inclusive' => true                            
                        )
                    ),
                ),
            ),
            'volunteerOverallRemarks' => array(
                'required' => true,
                'filters' => array(
                    array('name' => 'StringTrim'),
                    array('name' => 'StripTags'),
                ),
                'validators' => array(
                    array(
                        'name' => 'StringLength',
                        'options' => array(
                            'min' => 3,
                            'max' => 500,
                        ),
                    ),
                ),
            ),
        );
    }
    
    public static function getInterviewerOptions($interviewers) {
        $interviewersNames = explode(VolunteerInterview::INTERVIEWER_SEPARATOR, $interviewers);
        
        $options = [];
        foreach ($interviewersNames as $interviewerName) {
            $options[$interviewerName] = $interviewerName;
        }
        
        return $options;
    }
            
}
