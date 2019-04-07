<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Authorization\Form;

use Zend\InputFilter\InputFilter;

/**
 * Description of RoleFilter
 *
 * @author marcio
 */
class RoleFilter extends InputFilter
{

    public function __construct()
    {
        $this->add(array(
            'name' => 'role_name',
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
                        'min' => 4,
                        'max' => 200,
                    ),
                ),
            ),
        ));
        $this->add(array(
            'name' => 'role_parent',
            'required' => false,
        ));
    }

}
