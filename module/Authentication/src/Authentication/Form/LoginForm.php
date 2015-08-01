<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Authentication\Form;

use Zend\Form\Form;

/**
 * Description of LoginForm
 *
 * @author marcio
 */
class LoginForm extends Form
{
    public function __construct($name = null)
    {
        parent::__construct('login');
        
        $this->setAttribute('method', 'post')
                // user_name
                ->add(array(
                    'name' => 'username',
                    'attributes' => array(
                        'type' => 'text',
                        'placeholder' => 'Username',
                        'id' => 'username',
                    ),
                ))
                // user_password
                ->add(array(
                    'name' => 'password',
                    'attributes' => array(
                        'type' => 'password',
                        'placeholder' => 'Password',
                        'id' => 'password',
                    )
                ))
                // Remember Me checkbox
                ->add(array(
                    'name' => 'rememberme',
                    'type' => 'checkbox',
                ))
                // Submit Button
                ->add(array(
                    'name' => 'submit',
                    'attributes' => array(
                        'type' => 'submit',
                        'value' => 'Login',
                        'id' => 'submitbutton',
                    ),
                ));
    }
}
