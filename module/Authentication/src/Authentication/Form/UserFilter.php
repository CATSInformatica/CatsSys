<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Authentication\Form;

/**
 * Description of UserForm
 *
 * @author marcio
 */
use Zend\InputFilter\InputFilter;
use Zend\Validator\Identical;

class UserFilter extends InputFilter
{

    public function __construct()
    {
        $this->add(array(
            'name' => 'user_name',
            'required' => true,
            'filters' => array(
                array('name' => 'StripTags'),
                array('name' => 'StringTrim'),
            ),
            'validators' => array(
                array(
                    'name' => 'StringLength',
                    'options' => array(
                        'encoding' => 'UTF-8',
                        'min' => 5,
                        'max' => 100,
                    ),
                ),
            ),
        ));
//        $this->add(array(
//            'name' => 'user_email',
//            'required' => true,
//            'validators' => array(
//                array(
//                    'name' => 'EmailAddress',
//                ),
//            ),
//        ));
//
//        $this->add(array(
//            'name' => 'user_email_confirm',
//            'required' => true,
//            'validators' => array(
//                new Identical('user_email')
//            ),
//        ));

        $this->add(array(
            'name' => 'user_password',
            'required' => true,
            'filters' => array(
                array('name' => 'StripTags'),
                array('name' => 'StringTrim'),
            ),
            'validators' => array(
                array(
                    'name' => 'StringLength',
                    'options' => array(
                        'encoding' => 'UTF-8',
                        'min' => 6,
                        'max' => 20,
                    ),
                ),
            ),
        ));

        $this->add(array(
            'name' => 'user_password_confirm',
            'validators' => array(
                new Identical('user_password')
            ),
        ));


//        $this->add(array(
//            'name' => 'user_active',
//            'required' => false,
//            'filters' => array(
//                array('name' => 'Int'),
//            ),
//            'validators' => array(
//                array(
//                    'name' => 'Digits',
//                ),
//            ),
//        ));
    }

}
