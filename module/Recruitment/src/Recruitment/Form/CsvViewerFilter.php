<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Recruitment\Form;

use Zend\InputFilter\InputFilter;

/**
 * Description of CsvViewerFilter
 *
 * @author Gabriel Pereira <rickardch@gmail.com>
 */
class CsvViewerFilter extends InputFilter
{
    
    public function __construct()
    {
        $this->add(array(
                'name' => 'csv_file',
                'required' => true,
                'validators' => array(
                    array(
                        'name' => 'Zend\Validator\File\Extension',
                        'options' => array(
                            'extension' => array(
                                'csv',
                            ),
                        ),
                    ),
                    array(
                        'name' => 'Zend\Validator\File\Size',
                        'options' => array(
                            'min' => '5',
                            'max' => '1000000',
                        )
                    )
            ),
        ));
    }
    
}
