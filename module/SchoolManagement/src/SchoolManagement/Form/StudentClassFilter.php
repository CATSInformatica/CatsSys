<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace SchoolManagement\Form;

use Zend\InputFilter\InputFilter;

/**
 * Description of StudentClassFilter
 *
 * @author Márcio Dias <marciojr91@gmail.com>
 */
class StudentClassFilter extends InputFilter
{

    public function __construct()
    {
        $this->add(array(
                    'name' => 'class_name',
                    'required' => true,
                    'filters' => array(
                        array('name' => 'StringTrim'),
                        array('name' => 'StripTags'),
                    ),
                    'validators' => array(
                        array(
                            'name' => 'Zend\Validator\StringLength',
                            'options' => array(
                                'min' => '6',
                                'max' => '80',
                            ),
                        ),
                    ),
                ))
                ->add(array(
                    'name' => 'class_begin_date',
                    'required' => true,
                    'filters' => array(
                        array('name' => 'StripTags'),
                        array('name' => 'StringTrim'),
                        array(
                            'name' => 'Recruitment\Filter\DateToFormat',
                            'options' => array(
                                'inputFormat' => 'd/m/Y',
                                'outputFormat' => 'Y-m-d',
                            ),
                        ),
                    ),
                    'validators' => array(
                        array(
                            'name' => 'Date',
                            'options' => array(
                                'format' => 'Y-m-d',
                            )
                        ),
                        array(
                            'name' => 'Recruitment\Validator\DateGratherThan',
                            'options' => array(
                                'format' => 'Y-m-d',
                                'inclusive' => false,
                            ),
                        ),
                    ),
                ))
                ->add(array(
                    'name' => 'class_end_date',
                    'required' => true,
                    'filters' => array(
                        array('name' => 'StripTags'),
                        array('name' => 'StringTrim'),
                        array(
                            'name' => 'Recruitment\Filter\DateToFormat',
                            'options' => array(
                                'inputFormat' => 'd/m/Y',
                                'outputFormat' => 'Y-m-d',
                            ),
                        ),
                    ),
                    'validators' => array(
                        array(
                            'name' => 'Date',
                            'options' => array(
                                'format' => 'Y-m-d',
                            )
                        ),
                        array(
                            'name' => 'Recruitment\Validator\DateGratherThan',
                            'options' => array(
                                'format' => 'Y-m-d',
                                'compareWith' => array(
                                    'name' => 'class_begin_date',
                                    'format' => 'd/m/Y',
                                ),
                            ),
                        ),
                    ),
        ));
    }

}
