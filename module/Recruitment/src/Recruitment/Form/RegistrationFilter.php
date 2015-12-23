<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Recruitment\Form;

use Zend\InputFilter\InputFilter;

/**
 * Description of RegistrationFilter
 *
 * @author marcio
 */
class RegistrationFilter extends InputFilter
{

    public function __construct()
    {
        $this->add(array(
            'name' => 'person_firstname',
            'required' => true,
            'filters' => array(
                array('name' => 'StripTags'),
                array('name' => 'StringToUpper'),
            ),
            'validators' => array(
                array(
                    'name' => 'Zend\Validator\StringLength',
                    'options' => array(
                        'min' => '3',
                        'max' => '80',
                    )
                ),
            ),
        ))->add(array(
            'name' => 'person_lastname',
            'required' => true,
            'filters' => array(
                array('name' => 'StripTags'),
                array('name' => 'StringToUpper'),
            ),
            'validators' => array(
                array(
                    'name' => 'Zend\Validator\StringLength',
                    'options' => array(
                        'min' => '3',
                        'max' => '200',
                    ),
                ),
            ),
        ))->add(array(
            'name' => 'person_gender',
            'required' => true,
        ))->add(array(
            'name' => 'person_birthday',
            'required' => true,
            'filters' => array(
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
                    'name' => 'Zend\Validator\Date',
                    'options' => array(
                        'format' => 'Y-m-d',
                    ),
                ),
            ),
        ))->add(array(
            'name' => 'person_cpf',
            'required' => true,
            'filters' => array(
                array('name' => 'StripTags')
            ),
            'validators' => array(
                array(
                    'name' => 'Recruitment\Validator\Cpf',
                ),
            ),
        ))->add(array(
            'name' => 'person_rg',
            'required' => true,
            'filters' => array(
                array('name' => 'StringTrim'),
                array('name' => 'StringToUpper'),
            ),
            'validadors' => array(
                array(
                    'name' => 'Zend\Validator\StringLength',
                    'options' => array(
                        'min' => 6,
                        'max' => 25,
                    ),
                ),
            ),
        ))->add(array(
            'name' => 'person_phone',
            'required' => true,
            'filters' => array(
                array('name' => 'StringTrim')
            ),
            'validators' => array(
                array(
                    'name' => 'Zend\Validator\StringLength',
                    'options' => array(
                        'min' => 6,
                        'max' => 20,
                    ),
                ),
            ),
        ))->add(array(
            'name' => 'person_email',
            'required' => true,
            'validators' => array(
                array(
                    'name' => 'Zend\Validator\StringLength',
                    'options' => array(
                        'min' => 9,
                        'max' => 50,
                    ),
                ),
                array(
                    'name' => 'Zend\Validator\EmailAddress',
                ),
            )
        ))->add(array(
            'name' => 'person_confirm_email',
            'required' => true,
            'validators' => array(
                array(
                    'name' => 'Zend\Validator\Identical',
                    'options' => array(
                        'token' => 'person_email',
                    ),
                ),
            )
        ))->add(array(
            'name' => 'registration_consent',
            'required' => true,
        ))->add(array(
            'name' => 'registration_captcha',
            'required' => true,
        ));
    }

}
