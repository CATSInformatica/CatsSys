<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace SchoolManagement\Form;

use Zend\InputFilter\InputFilter;

/**
 * Description of StudentWarningFilter
 *
 * @author Márcio Dias <marciojr91@gmail.com>
 */
class StudentWarningFilter extends InputFilter
{

    public function __construct()
    {
        $this->add(array(
                    'name' => 'warning_type_name',
                    'required' => true,
                    'filters' => array(
                        array('name' => 'StringTrim'),
                        array('name' => 'StripTags'),
                        array(
                            'name' => 'StringToUpper',
                            'options' => array(
                                'encoding' => 'UTF-8',
                            ),
                        ),
                    ),
                    'validators' => array(
                        array(
                            'name' => 'Zend\Validator\StringLength',
                            'options' => array(
                                'min' => 5,
                                'max' => 80,
                            )
                        )
                    )
                ))
                ->add(array(
                    'name' => 'warning_type_description',
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
