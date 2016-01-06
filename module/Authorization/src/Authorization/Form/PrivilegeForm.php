<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Authorization\Form;

use Zend\Form\Form;

/**
 * Description of PrivilegeForm
 *
 * @author marcio
 */
class PrivilegeForm extends Form {

    public function __construct($name = null, $options = array()) {
        parent::__construct($name);

        $this->add(array(
                    'name' => 'privilege_name',
                    'attributes' => array(
                        'type' => 'text',
                        'class' => 'form-control',
                        'placeholder' => 'Privilege name',
                    ),
                ))
                ->add(array(
                    'name' => 'privilege_permission_allow',
                    'type' => 'checkbox',
                    'class' => 'form-control',
                ))
                ->add(array(
                    'name' => 'resource_id',
                    'type' => 'Zend\Form\Element\Select',
                    'options' => array(
                        'value_options' => $options['resources'],
                    ),
                    'attributes' => array(
                        'type' => 'select',
                        'class' => 'form-control',
                    ),
                ))
                ->add(array(
                    'name' => 'role_id',
                    'type' => 'Zend\Form\Element\Select',
                    'options' => array(
                        'value_options' => $options['roles'],
                    ),
                    'attributes' => array(
                        'type' => 'select',
                        'class' => 'form-control',
                    )
                ))
                ->add(array(
                    'name' => 'Submit',
                    'attributes' => array(
                        'type' => 'submit',
                        'class' => 'btn btn-primary btn-block',
                        'value' => 'Go',
                    )
        ));
    }

}
