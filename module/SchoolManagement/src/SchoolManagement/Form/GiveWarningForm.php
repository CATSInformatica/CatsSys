<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace SchoolManagement\Form;

use Zend\Form\Form;

/**
 * Description of GiveWarningForm
 *
 * @author Gabriel Pereira <rickardch@gmail.com>
 */
class GiveWarningForm extends Form
{

    public function __construct($name = null, $options = array())
    {
        parent::__construct($name);

        $this->add(array(
                    'name' => 'person_id',
                    'type' => 'Zend\Form\Element\Select',
                    'options' => array(
                        'label' => 'Aluno',
                        'value_options' => $options['names'],
                    ),
                    'attributes' => array(
                        'type' => 'select',
                    ),
                ))
                ->add(array(
                    'name' => 'class_id',
                    'type' => 'Zend\Form\Element\Select',
                    'options' => array(
                        'label' => 'Turma',
                        'value_options' => $options['class_names'],
                    ),
                    'attributes' => array(
                        'type' => 'select',
                    )
                ))
                ->add(array(
                    'name' => 'warning_date',
                    'options' => array(
                        'label' => 'Data da Advertência',
                        'add-on-prepend' => '<i class="glyphicon glyphicon-calendar"></i>',
                    ),
                    'attributes' => array(
                        'type' => 'text',
                        'class' => 'datepicker text-center',
                        'placeholder' => 'Ex: 22/04/1500',
                    )
                ))
                ->add(array(
                    'name' => 'warning_id',
                    'type' => 'Zend\Form\Element\Select',
                    'options' => array(
                        'label' => 'Tipo de Advertência',
                        'value_options' => $options['warning_names'],
                    ),
                    'attributes' => array(
                        'type' => 'select',
                    )
                ))
                ->add(array(
                    'name' => 'warning_comment',
                    'type' => 'textarea',
                    'options' => array(
                        'label' => 'Comentário',
                        'add-on-prepend' => '<i class="fa fa-paragraph"></i>',
                    ),
                    'attributes' => array(
                        'rows' => '5',
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
