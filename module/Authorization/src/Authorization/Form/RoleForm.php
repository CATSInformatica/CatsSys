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
class RoleForm extends Form
{

    public function __construct($roles = null)
    {
        parent::__construct('Role');

        $this->add(array(
                    'name' => 'role_name',
                    'attributes' => array(
                        'type' => 'text',
                        'class' => 'form-control',
                        'placeholder' => 'Role name',
                    ),
                ))
                ->add(array(
                    'name' => 'role_parent',
                    'type' => 'Zend\Form\Element\Select',
                    'options' => array(
                        'value_options' => $roles,
                    ),
                    'attributes' => array(
                        'class' => 'form-control',
                        'multiple' => 'multiple',
                    ),
                ))
                ->add(array(
                    'name' => 'Submit',
                    'attributes' => array(
                        'type' => 'button',
                        'class' => 'btn btn-primary btn-lg',
                        'value' => 'Go',
                    ),
        ));
    }

}
