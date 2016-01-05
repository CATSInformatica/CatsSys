<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Documents\Form;

use Zend\Form\Element;
use Zend\Form\Form;

/**
 * Description of StudentBgConfigForm
 *
 * @author Gabriel Pereira <rickardch@gmail.com>
 */
class StudentBgConfigForm extends Form 
{
    public function __construct($name = null, $options = array())
    {
        parent::__construct($name, $options);

        $bg_img = new Element\File('bg_img');
        $bg_img->setAttribute('type', 'file');
        $bg_img->setAttribute('id', 'bg_img');
        
        $this->add(array(
                    'name' => 'bg_phrase',
                    'attributes' => array(
                        'type' => 'text',
                        'class' => 'form-control',
                    ),
                ))
                ->add(array(
                    'name' => 'bg_author',
                    'attributes' => array(
                        'type' => 'text',
                        'class' => 'form-control',
                    ),
                ))
                ->add($bg_img)
                ->add(array(
                    'name' => 'Submit',
                    'attributes' => array(
                        'type' => 'button',
                        'class' => 'btn btn-primary btn-block',
                        'value' => 'Criar',
                    )
        ));
    }
}
