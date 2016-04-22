<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Authentication\Form;

/**
 * Description of UserForm
 *
 * @author marcio
 */
use Zend\Form\Form;

class UserForm extends Form
{

    public function __construct($name = null)
    {
        parent::__construct('user');
        $this->setAttribute('method', 'post');
        $this->add(array(
            'name' => 'user_name',
            'attributes' => array(
                'type' => 'text',
            ),
            'options' => array(
                'label' => 'Nome de usuário',
            ),
        ));

        $this->add(array(
            'name' => 'user_password',
            'attributes' => array(
                'type' => 'password',
            ),
            'options' => array(
                'label' => 'Senha',
            ),
        ));

        $this->add(array(
            'name' => 'user_password_confirm',
            'attributes' => array(
                'type' => 'password',
            ),
            'options' => array(
                'label' => 'Confirmação de senha',
            ),
        ));

//        $this->add(array(
//            'name' => 'user_email',
//            'attributes' => array(
//                'type' => 'email',
//            ),
//        ));
//        
//        $this->add(array(
//            'name' => 'user_email_confirm',
//            'attributes' => array(
//                'type' => 'email',
//            ),
//        ));

        $this->add(array(
            'name' => 'submit',
            'type' => 'Submit',
            'attributes' => array(
                'value' => 'Salvar',
                'id' => 'submitbutton',
                'class' => 'btn btn-primary btn-block'
            ),
        ));
    }

}
