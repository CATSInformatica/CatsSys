<?php

namespace SchoolManagement\Form\Fieldset;

use Zend\Form\Fieldset;
use Zend\InputFilter\InputFilterProviderInterface;

/**
 * Description of attendanceDateFieldset
 *
 * @author Márcio Dias <marciojr91@gmail.com>
 */
class AttendanceDateFieldset extends Fieldset implements InputFilterProviderInterface
{

    public function __construct($name = null, $options = array())
    {
        parent::__construct($name, $options);

        $this->setAttribute('class', 'col-md-4 col-xs-6');

        $this->add(array(
            'name' => 'attendanceDate',
            'type' => 'text',
            'attributes' => array(
                'class' => 'text-center',
                'value' => date('d/m/Y'),
            ),
            'options' => array(
                'add-on-prepend' => '<i class="glyphicon glyphicon-calendar"></i>',
            ),
        ));
    }

    public function getInputFilterSpecification()
    {
        return array(
            'attendanceDate' => array(
                'required' => true,
                'filters' => array(
                    array(
                        'name' => 'Recruitment\Filter\DateToFormat',
                        'options' => array(
                            'inputFormat' => 'd/m/Y',
                            'outputFormat' => 'Y-m-d'
                        ),
                    ),
                ),
                'validators' => array(
                    array(
                        'name' => 'Zend\Validator\Date',
                        'options' => array(
                            'format' => 'Y-m-d',
                        ),
                    ),
                ),
            ),
        );
    }

}
