<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Documents\Form;

/**
 * Description of StudentIdCardFilter
 *
 * @author Gabriel Pereira <rickardch@gmail.com>
 */
class StudentIdCardFilter extends StudentsBoardFilter 
{
    public function __construct()
    {
        $this->add(array(
                    'name' => 'expiry_date',
                    'required' => true,
                    'filters' => array(
                        array('name' => 'StripTags'),
                        array('name' => 'StringTrim'),
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
                            'name' => 'Date',
                            'options' => array(
                                'format' => 'Y-m-d',
                            )
                        ),
                    ),
        ));
    }
}
