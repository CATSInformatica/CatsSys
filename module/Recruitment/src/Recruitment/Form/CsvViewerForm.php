<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Recruitment\Form;

use Zend\Form\Form;

/**
 * Description of CsvViewerForm
 *
 * @author gabriel
 */
class CsvViewerForm extends Form
{
    public function __construct($name = null) {
        parent::__construct($name);
        $this->setAttribute('method', 'post');
        $this->setAttribute('enctype', 'multipart/form-data');

        $this->add(array(
                'name' => 'csv_file',
                'attributes' => array(
                    'type' => 'file',
                    'id' => 'csv_file',
                ),
                'options' => array(
                    'label' => 'Arquivo CSV',
                ),
            ))
            ->add(array(
                'name' => 'Submit',
                'attributes' => array(
                    'type' => 'submit',
                    'class' => 'btn btn-primary btn-block',
                    'value' => 'Visualizar',
                ),
        ));
    }
}
