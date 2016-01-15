<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Recruitment\Form;

use Zend\InputFilter\InputFilter;

/**
 * Description of CpfFormFilter
 *
 * @author Márcio Dias <marciojr91@gmail.com>
 */
class CpfFilter extends InputFilter
{

    public function __construct()
    {
        $this->add(array(
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
        ));
    }

}
