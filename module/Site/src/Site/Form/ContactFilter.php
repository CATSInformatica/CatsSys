<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Site\Form;

use Zend\InputFilter\InputFilter;

/**
 * Description of ContactFilter
 *
 * @author Gabriel Pereira <rickardch@gmail.com>
 */
class ContactFilter extends InputFilter
{
    
    public function __construct()
    {
        $this->add(array(
                    'name' => 'name',
                    'required' => true,
                    'filters' => array(
                        array('name' => 'StringTrim'),
                        array('name' => 'StripTags'),
                        array('name' => 'StringToUpper'),
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
                    'name' => 'email',
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
                ))
                ->add(array(
                    'name' => 'subject',
                    'required' => true,
                    'filters' => array(
                        array('name' => 'StringTrim'),
                        array('name' => 'StripTags'),
                        array('name' => 'StringToUpper'),
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
                    'name' => 'message',
                    'required' => true,
                    'filters' => array(
                        array('name' => 'StringTrim'),
                        array('name' => 'StripTags'),
                        array('name' => 'StringToUpper'),
                    ),
                    'validators' => array(
                        array(
                            'name' => 'Zend\Validator\StringLength',
                            'options' => array(
                                'min' => 5,
                                'max' => 800,
                            )
                        )
                    )
        ));
    }
    
}
