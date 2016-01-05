<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Authorization\Form;

use Zend\Form\Form;

/**
 * Description of UserRoleForm
 *
 * @author marcio
 */
class UserRoleForm extends Form {

    //put your code here
    public function __construct($name = null, $options = array()) {
        parent::__construct($name);

        $this->add(array(
                    'name' => 'user_id',
                    'type' => 'Zend\Form\Element\Select',
                    'options' => array(
                        'value_options' => $options['users'],
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
                        'type' => 'button',
                        'class' => 'btn btn-primary btn-block',
                        'value' => 'Go',
                    )
        ));
    }

}
