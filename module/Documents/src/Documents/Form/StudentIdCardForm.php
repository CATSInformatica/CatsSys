<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Documents\Form;

/**
 * Description of StudentIdCardForm
 *
 * @author Gabriel Pereira <rickardch@gmail.com>
 */
class StudentIdCardForm extends StudentsBoardForm 
{
    
    use \Database\Service\EntityManagerService;
        
    public function __construct($configIds = array(), $classNames = array())
    {
        parent::__construct($configIds, $classNames);
                
        $this->add(array(
                'name' => 'expiry_date',
                'attributes' => array(
                    'type' => 'text',
                    'class' => 'datepicker text-center',
                    'placeholder' => 'Ex: 22/04/1500', 
                ),
                'options' => array(
                    'label' => 'Data de expiração',
                    'add-on-prepend' => '<i class="glyphicon glyphicon-calendar"></i>',
                ),
                    
        ));
    }
}
