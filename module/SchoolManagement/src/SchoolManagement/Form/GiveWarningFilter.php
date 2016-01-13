<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace SchoolManagement\Form;

use Zend\InputFilter\InputFilter;

/**
 * Description of GiveWarningFilter
 *
 * @author Gabriel Pereira <rickardch@gmail.com>
 */
class GiveWarningFilter extends InputFilter
{
    
    public function __construct()
    {
        $this->add(array(
                    'name' => 'person_id',
                    'required' => true,
                ))
                ->add(array(
                    'name' => 'class_id',
                    'required' => true,
                ))
                ->add(array(
                    'name' => 'warning_id',
                    'required' => true,
                ))
                ->add(array(
                    'name'     => 'warning_comment',
                    'required' => false,
                    'filters'  => array(
                        array('name' => 'StripTags'),
                        array('name' => 'StringTrim'),
                    ),
                    'validators' => array(
                        array(
                            'name'    => 'StringLength',
                            'options' => array(
                                'encoding' => 'UTF-8',
                                'min'      => 1,
                                'max'      => 500,
                            ),
                        ),
                    ),
        ));
    }
    
}
