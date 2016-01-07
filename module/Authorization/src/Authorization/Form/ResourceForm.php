<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Authorization\Form;

use Zend\Form\Form;

/**
 * Description of RoleForm
 *
 * @author Márcio Dias <marciojr91@gmail.com>
 */
class ResourceForm extends Form
{

    public function __construct()
    {
        parent::__construct('Resource');

        $this->add(array(
                'name' => 'resource_name',
                'attributes' => array(
                    'type' => 'text',
                    'placeholder' => 'Resource name',
                ),
                'options' => array(
                    'label' => 'Nome do recurso',
                ),
            ))
            ->add(array(
                'name' => 'Submit',
                'attributes' => array(
                    'type' => 'submit',
                    'class' => 'btn btn-primary btn-block',
                    'value' => 'Go',
                ),
        ));
    }

}
