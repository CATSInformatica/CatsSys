<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace SchoolManagement\Form;

use Zend\Form\Form;

/**
 * Description of StudentClassForm
 *
 * @author Márcio Dias <marciojr91@gmail.com>
 */
class StudentClassForm extends Form
{

    public function __construct($name = null, $options = array())
    {
        parent::__construct($name, $options);

        $this->add(array(
                'name' => 'class_name',
                'attributes' => array(
                    'type' => 'text',
                    'class' => 'form-control',
                    'placeholder' => 'Ex.: Turma de 2015',
                ),
                'options' => array(
                    'label' => 'Nome da turma',
                ),
            ))
            ->add(array(
                'name' => 'class_begin_date',
                'attributes' => array(
                    'type' => 'text',
                    'class' => 'datepicker text-center',
                    'placeholder' => 'Ex: 01/02/2015',
                ),
                'options' => array(
                    'label' => 'Data de início das aulas',
                    'add-on-prepend' => '<i class="glyphicon glyphicon-calendar"></i>',
                ),
            ))
            ->add(array(
                'name' => 'class_end_date',
                'attributes' => array(
                    'type' => 'text',
                    'class' => 'datepicker text-center',
                    'add-on-prepend' => '<i class="glyphicon glyphicon-calendar"></i>',
                    'placeholder' => 'Ex: 01/12/2015',
                ),
                'options' => array(
                    'label' => 'Data de término das aulas',
                    'add-on-prepend' => '<i class="glyphicon glyphicon-calendar"></i>',
                ),
            ))
            ->add(array(
                'name' => 'Submit',
                'type' => 'submit',
                'attributes' => array(
                    'class' => 'btn btn-primary btn-block',
                    'value' => 'Criar',
                ),
        ));
    }

}
