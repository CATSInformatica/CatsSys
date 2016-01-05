<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace SchoolManagement\Form;

use Zend\Form\Form;

/**
 * Description of StudentWarningForm
 *
 * @author Márcio Dias <marciojr91@gmail.com>
 */
class StudentWarningForm extends Form
{

    public function __construct($name = null, $options = array())
    {
        parent::__construct($name, $options);

        $this->add(array(
                    'name' => 'warning_type_name',
                    'attributes' => array(
                        'type' => 'text',
                        'class' => 'form-control',
                    )
                ))
                ->add(array(
                    'name' => 'warning_type_description',
                    'attributes' => array(
                        'type' => 'textarea',
                        'class' => 'form-control',
                        'rows' => 5,
                    )
                ))
                ->add(array(
                    'name' => 'submit',
                    'attributes' => array(
                        'type' => 'text',
                        'class' => 'btn btn-primary btn-block',
                        'value' => 'Criar',
                    )
        ));
    }

}
