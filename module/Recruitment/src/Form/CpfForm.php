<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Recruitment\Form;

use Zend\Form\Form;
use Zend\InputFilter\InputFilterProviderInterface;

/**
 * Description of CpfForm
 *
 * @author Márcio Dias <marciojr91@gmail.com>
 */
class CpfForm extends Form implements InputFilterProviderInterface
{

    public function __construct($name = null, $options = array())
    {
        parent::__construct($name, $options);

        $this->add(array(
                'name' => 'person_cpf',
                'type' => 'text',
                'attributes' => array(
                    'placeholder' => '999.999.999-99',
                ),
                'options' => array(
                    'label' => 'Número do Cpf*',
                ),
            ))
            ->add(array(
                'name' => 'submit',
                'type' => 'submit',
                'attributes' => array(
                    'class' => 'btn btn-success btn-block',
                    'value' => 'Prosseguir',
                )
        ));
    }

    public function getInputFilterSpecification()
    {
        return array(
            'person_cpf' => array(
                'required' => true,
                'filters' => array(
                    array('name' => 'StripTags')
                ),
                'validators' => array(
                    array(
                        'name' => 'Recruitment\Validator\Cpf',
                    ),
                ),
            ),
        );
    }

}
