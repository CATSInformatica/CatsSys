<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Documents\Form;

use Zend\InputFilter\InputFilter;

/**
 * Description of StudentsBoardFilter
 *
 * @author Gabriel Pereira <rickardch@gmail.com>
 */
class StudentsBoardFilter extends InputFilter
{
    
    public function __construct()
    {
        $this->add(array(
                    'name' => 'config_id',
                    'required' => true,
                ))
                ->add(array(
                    'name' => 'class_id',
                    'required' => true,
        ));
    }
    
}
