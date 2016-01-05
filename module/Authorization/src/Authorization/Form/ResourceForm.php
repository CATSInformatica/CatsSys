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
 * @author marcio
 */
class ResourceForm extends Form
{

    public function __construct()
    {
        parent::__construct('Resource');

//        $this->setAttributes(array(
//            'action' =>,
//        ));

        $this->add(array(
                    'name' => 'resource_name',
                    'attributes' => array(
                        'type' => 'text',
                        'class' => 'form-control',
                        'placeholder' => 'Resource name',
                    ),
                ))
                ->add(array(
                    'name' => 'Submit',
                    'attributes' => array(
                        'type' => 'button',
                        'class' => 'btn btn-primary btn-block',
                        'value' => 'Go',
                    ),
        ));
    }

}
