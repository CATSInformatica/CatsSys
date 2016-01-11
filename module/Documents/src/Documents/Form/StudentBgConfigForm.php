<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Documents\Form;

use Zend\Form\Form;

/**
 * Description of StudentBgConfigForm
 *
 * @author Gabriel Pereira <rickardch@gmail.com>
 */
class StudentBgConfigForm extends Form {

    public function __construct($name = null, $options = array()) {
        parent::__construct($name, $options);
        $this->setAttribute('method', 'post');
        $this->setAttribute('enctype', 'multipart/form-data');

        $this->add(array(
                    'name' => 'bg_phrase',
                    'attributes' => array(
                        'type' => 'text',
                    ),
                    'options' => array(
                        'label' => 'Frase',
                    ),
                ))
                ->add(array(
                    'name' => 'bg_author',
                    'attributes' => array(
                        'type' => 'text',
                    ),
                    'options' => array(
                        'label' => 'Autor',
                    ),
                ))
                ->add(array(
                    'name' => 'bg_img',
                    'attributes' => array(
                        'type' => 'file',
                        'id' => 'bg_img',
                    ),
                    'options' => array(
                        'label' => 'Imagem de fundo',
                    ),
                ))
                ->add(array(
                    'name' => 'Submit',
                    'attributes' => array(
                        'type' => 'submit',
                        'class' => 'btn btn-primary btn-block',
                        'value' => 'Criar',
                    ),
        ));
    }

}
