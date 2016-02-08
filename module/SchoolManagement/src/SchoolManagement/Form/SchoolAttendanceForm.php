<?php

namespace SchoolManagement\Form;

use Doctrine\Common\Persistence\ObjectManager;
use Zend\Form\Form;
use Zend\InputFilter\InputFilterProviderInterface;

/**
 * Description of SchoolAttendanceForm
 *
 * @author Márcio Dias <marciojr91@gmail.com>
 */
class SchoolAttendanceForm extends Form implements InputFilterProviderInterface
{

    public function __construct(ObjectManager $obj, $name = null, $options = array())
    {
        parent::__construct($name, $options);

        $this
            ->add(array(
                'name' => 'schoolClasses',
                'type' => 'DoctrineModule\Form\Element\ObjectSelect',
                'options' => array(
                    'label' => 'Turma',
                    'object_manager' => $obj,
                    'target_class' => 'SchoolManagement\Entity\StudentClass',
                    'property' => 'className',
                    'find_method' => array(
                        'name' => 'findByEndDateGratherThan',
                        'params' => array(
                            'endDate' => new \DateTime('now'),
                        ),
                    ),
                ),
            ))
            ->add(array(
                'name' => 'attendanceType',
                'type' => 'DoctrineModule\Form\Element\ObjectMultiCheckbox',
                'options' => array(
                    'label' => 'Tipos',
                    'object_manager' => $obj,
                    'target_class' => 'SchoolManagement\Entity\AttendanceType',
                    'property' => 'attendanceType',
                ),
            ))
            ->add(array(
                'type' => 'Zend\Form\Element\Collection',
                'name' => 'attendanceDate',
                'options' => array(
                    'label' => 'Data(s)',
                    'count' => 1,
                    'should_create_template' => true,
                    'allow_add' => true,
                    'target_element' => array(
                        'type' => 'text',
                        'options' => array(
                            'add-on-prepend' => '<i class="glyphicon glyphicon-calendar"></i>',
                        ),
                    ),
                ),
            ))
            ->add(array(
                'name' => 'addAttendanceDate',
                'type' => 'button',
                'attributes' => array(
                    'class' => 'col-md-6 col-xs-6 btn-success',
                    'id' => 'addAttendanceDate',
                ),
                'options' => array(
                    'label' => ' ',
                    'glyphicon' => 'plus',
                ),
            ))
            ->add(array(
                'name' => 'removeAttendanceDate',
                'type' => 'button',
                'attributes' => array(
                    'class' => 'col-md-6 col-xs-6 btn-danger',
                    'id' => 'removeAttendanceDate',
                ),
                'options' => array(
                    'label' => ' ',
                    'glyphicon' => 'minus',
                ),
            ))
            ->add(array(
                'name' => 'submit',
                'type' => 'submit',
                'attributes' => array(
                    'value' => 'Gerar Lista',
                    'class' => 'btn-primary btn-block',
                ),
            ))
        ;
    }

    public function getInputFilterSpecification()
    {
        return array(
            'schoolClasses' => array(
                'required' => true,
            ),
//            'attendanceDate' => array(
//                'required' => true,
//            ),
            'attendanceType' => array(
                'required' => true,
            )
        );
    }

}
