<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Site\Form;

use Zend\Form\Form;


/**
 * Description of ContactForm
 *
 * @author Gabriel Pereira <rickardch@gmail.com>
 */
class ContactForm extends Form
{
    
    public function __construct($name = null) {
        
        parent::__construct($name);
        
        $this->add(array(
                    'name' => 'name',
                    'attributes' => array(
                        'type' => 'text',
                    ),
                    'options' => array(
                        'label' => 'Nome',
                    ),
                ))
                ->add(array(
                    'name' => 'email',
                    'attributes' => array(
                        'type' => 'text',
                    ),
                    'options' => array(
                        'label' => 'Email',
                    ),
                ))
                ->add(array(
                    'name' => 'subject',
                    'attributes' => array(
                        'type' => 'text',
                    ),
                    'options' => array(
                        'label' => 'Assunto',
                    ),
                ))
                ->add(array(
                    'name' => 'message',
                    'attributes' => array(
                        'type' => 'textarea',
                    ),
                    'options' => array(
                        'label' => 'Mensagem',
                    ),
                ))
                ->add(array(
                    'name' => 'Submit',
                    'attributes' => array(
                        'type' => 'submit',
                        'class' => 'btn btn-primary btn-block',
                        'value' => 'Enviar',
                    ),
        ));
    }
}
