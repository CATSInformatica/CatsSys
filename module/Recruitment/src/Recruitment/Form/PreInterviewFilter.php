<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Recruitment\Form;

use Zend\InputFilter\InputFilter;

/**
 * Description of PreInterviewFilter
 *
 * @author Márcio Dias <marciojr91@gmail.com>
 */
class PreInterviewFilter extends InputFilter
{

    public function __construct()
    {
        $this->add(array(
                'name' => 'postal_code',
                'required' => true,
                'validators' => array(
                    array(
                        'name' => 'Zend\Validator\Regex',
                        'options' => array(
                            'pattern' => '/^[0-9]{5}-[0-9]{3}$/',
                        ),
                    ),
                ),
            ))
            ->add(array(
                'name' => 'state',
                'required' => true,
            ))
            ->add(array(
                'name' => 'city',
                'required' => true,
                'filters' => array(
                    array('name' => 'StringToUpper'),
                ),
                'validators' => array(
                    array(
                        'name' => 'Zend\Validator\StringLength',
                        'options' => array(
                            'min' => 3,
                            'max' => 50,
                        ),
                    ),
                ),
            ))
            ->add(array(
                'name' => 'neighborhood',
                'required' => true,
                'filters' => array(
                    array('name' => 'StringToUpper'),
                ),
                'validators' => array(
                    array(
                        'name' => 'Zend\Validator\StringLength',
                        'options' => array(
                            'min' => 3,
                            'max' => 50,
                        ),
                    ),
                ),
            ))
            ->add(array(
                'name' => 'street',
                'required' => true,
                'filters' => array(
                    array('name' => 'StringToUpper'),
                ),
                'validators' => array(
                    array(
                        'name' => 'Zend\Validator\StringLength',
                        'options' => array(
                            'min' => 1,
                            'max' => 100,
                        ),
                    ),
                ),
            ))
            ->add(array(
                'name' => 'number',
                'required' => false,
                'validators' => array(
                    array('name' => 'Zend\Validator\Digits'),
                ),
            ))
            ->add(array(
                'name' => 'complement',
                'required' => false,
                'filters' => array(
                    array('name' => 'StringToUpper'),
                ),
                'validators' => array(
                    array(
                        'name' => 'Zend\Validator\StringLength',
                        'options' => array(
                            'min' => 1,
                            'max' => 100,
                        ),
                    ),
                ),
            ))
            ->add(array(
                'name' => 'elementary_school_type',
                'required' => true,
            ))
            ->add(array(
                'name' => 'high_school_type',
                'required' => true,
            ))
            ->add(array(
                'name' => 'high_school',
                'required' => true,
                'filters' => array(
                    array('name' => 'StringToUpper'),
                ),
                'validators' => array(
                    array(
                        'name' => 'Zend\Validator\StringLength',
                        'options' => array(
                            'min' => 5,
                            'max' => 150,
                        ),
                    ),
                ),
            ))
            ->add(array(
                'name' => 'hs_conclusion_year',
                'required' => true,
            ))
            ->add(array(
                'name' => 'preparation_school',
                'required' => true,
            ))
            ->add(array(
                'name' => 'language_course',
                'required' => true,
            ))
            ->add(array(
                'name' => 'current_study',
                'required' => true,
            ))
            ->add(array(
                'name' => 'live_with_number',
                'required' => true,
            ))
            ->add(array(
                'name' => 'live_with_you',
                'required' => true,
            ))
            ->add(array(
                'name' => 'number_of_rooms',
                'required' => true,
            ))
            ->add(array(
                'name' => 'means_of_transport',
                'required' => true,
            ))
            ->add(array(
                'name' => 'monthly_income',
                'required' => true,
            ))
            ->add(array(
                'name' => 'father_education_grade',
                'required' => true,
            ))
            ->add(array(
                'name' => 'mother_education_grade',
                'required' => true,
            ))
            ->add(array(
                'name' => 'expect_from_us',
                'required' => true,
                'filters' => array(
                    array('name' => 'StringTrim'),
                    array('name' => 'StripTags'),
                ),
                'validators' => array(
                    array(
                        'name' => 'Zend\Validator\StringLength',
                        'options' => array(
                            'min' => 20,
                            'max' => 200,
                        )
                    )
                )
        ));
    }

}
