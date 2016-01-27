<?php

namespace Recruitment\Form;

use Zend\Form\Form;
use Zend\InputFilter\InputFilterProviderInterface;

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class TimestampForm extends Form implements InputFilterProviderInterface
{

    public function __construct($name = null, $options = array())
    {
        parent::__construct($name, $options);

        $this->add(array(
            'name' => 'timestamp',
            'type' => 'text',
            'attributes' => array(
                'class' => 'text-center',
                'placeholder' => date('d/m/Y H:i'),
            ),
            'options' => array(
                'label' => 'Data de agendamento',
                'add-on-prepend' => '<i class="fa fa-clock-o"></i>',
            ),
        ));
    }

    public function getInputFilterSpecification()
    {
        return array(
            'timestamp' => array(
                'required' => true,
                'filters' => array(
                    array(
                        'name' => 'Recruitment\Filter\DateToFormat',
                        'options' => array(
                            'inputFormat' => 'd/m/Y H:i',
                            'outputFormat' => 'Y-m-d H:i',
                        ),
                    ),
                ),
                'validators' => array(
                    array(
                        'name' => 'Zend\Validator\Date',
                        'options' => array(
                            'format' => 'Y-m-d H:i',
                        ),
                    ),
                    array(
                        'name' => 'Recruitment\Validator\DateGratherThan',
                        'options' => array(
                            'format' => 'Y-m-d H:i',
                            'inclusive' => true,
                        ),
                    ),
                ),
            ),
        );
    }

}
