<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Documents\Form;

use Zend\Form\Form;

/**
 * Description of StudentBoardForm
 *
 * @author Gabriel Pereira <rickardch@gmail.com>
 */
class StudentsBoardForm extends Form 
{        
    public function __construct($configIds = array(), $classNames = array())
    {
        parent::__construct('Student ID Form');
                
        $this->add(array(
                    'name' => 'config_id',
                    'type' => 'select',
                    'options' => array(
                        'value_options' => $configIds,
                        'label' => 'Configuração de Fundo',
                    ),
                ))
                ->add(array(
                    'name' => 'class_id',
                    'type' => 'select',
                    'options' => array(
                        'value_options' => $classNames,
                        'label' => 'Turma',
                    ),
                ))
                ->add(array(
                'name' => 'Submit',
                'attributes' => array(
                    'type' => 'submit',
                    'class' => 'btn btn-primary btn-block',
                    'value' => 'Gerar',
                )
        ));
    }
}
