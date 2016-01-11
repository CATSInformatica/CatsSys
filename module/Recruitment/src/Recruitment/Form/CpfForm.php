<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Recruitment\Form;

use Recruitment\Form\Settings\CpfSettings;
use Zend\Form\Form;

/**
 * Description of CpfForm
 *
 * @author Márcio Dias <marciojr91@gmail.com>
 */
class CpfForm extends Form
{

    public function __construct($name = null, $options = array())
    {
        parent::__construct($name, $options);

        $this->add(CpfSettings::createCpfElement())
            ->add(array(
                'name' => 'submit',
                'type' => 'submit',
                'attributes' => array(
                    'class' => 'btn btn-success btn-block',
                    'value' => 'Prosseguir',
                )
        ));
    }

}
