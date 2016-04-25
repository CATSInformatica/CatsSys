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

    public function __construct($name = null)
    {

        parent::__construct($name);

        $this->add(array(
                    'name' => 'name',
                    'attributes' => array(
                        'type' => 'text',
                    ),
                    'options' => array(
                        'label' => 'Nome',
                        'add-on-prepend' => '<i class="fa fa-user"></i>'
                    ),
                ))
                ->add(array(
                    'name' => 'email',
                    'attributes' => array(
                        'type' => 'text',
                    ),
                    'options' => array(
                        'label' => 'Email',
                        'add-on-prepend' => '<i class="fa fa-envelope-o"></i>'
                    ),
                ))
                ->add(array(
                    'name' => 'subject',
                    'attributes' => array(
                        'type' => 'text',
                    ),
                    'options' => array(
                        'label' => 'Assunto',
                        'add-on-prepend' => '<span class="glyphicon glyphicon-tag"></span>'
                    ),
                ))
                ->add(array(
                    'name' => 'message',
                    'attributes' => array(
                        'type' => 'textarea',
                        'rows' => 6,
                    ),
                    'options' => array(
                        'label' => 'Mensagem',
                        'add-on-prepend' => '<i class="fa fa-text-width"></i>',
                    ),
                ))
                ->add(array(
                    'name' => 'contactCsrf',
                    'type' => 'Zend\Form\Element\Csrf',
                ))
                ->add(array(
                    'name' => 'Submit',
                    'type' => 'button',
                    'attributes' => array(
                        'class' => 'btn btn-primary btn-block',
                        'value' => 'Enviar',
                    ),
        ));
    }

}
