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
                    'placeholder' => 'Role name',
                ),
                'options' => array(
                    'label' => 'Nome do papel',
                )
            ))
            ->add(array(
                'name' => 'role_parent',
                'type' => 'Zend\Form\Element\Select',
                'options' => array(
                    'label' => 'Ancestrais',
                    'value_options' => $roles,
                ),
                'attributes' => array(
                    'multiple' => 'multiple',
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
