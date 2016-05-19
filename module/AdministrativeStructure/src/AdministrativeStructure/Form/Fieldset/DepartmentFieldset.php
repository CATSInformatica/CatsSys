<?php

namespace AdministrativeStructure\Form\Fieldset;

use AdministrativeStructure\Entity\Department;
use Doctrine\Common\Persistence\ObjectManager;
use Zend\Form\Fieldset;
use Zend\InputFilter\InputFilterProviderInterface;
use DoctrineModule\Stdlib\Hydrator\DoctrineObject as DoctrineHydrator;

/**
 * Description of DepartmentFieldset
 *
 * @author Márcio Dias <marciojr91@gmail.com>
 */
class DepartmentFieldset extends Fieldset implements InputFilterProviderInterface
{

    public function __construct(ObjectManager $obj, $name = null, $options = array())
    {
        parent::__construct($name, $options);

        $this->setHydrator(new DoctrineHydrator($obj))
            ->setObject(new Department());

        $this
            ->add(array(
                'name' => 'departmentName',
                'type' => 'text',
                'attributes' => array(
                    'placeholder' => 'Ex: Recursos Humanos',
                ),
                'options' => array(
                    'label' => 'Nome do departamento',
                ),
            ))
            ->add(array(
                'name' => 'departmentIcon',
                'type' => 'text',
                'attributes' => array(
                    'placeholder' => 'Ex: glyphicon glyphicon-picture',
                ),
                'options' => array(
                    'label' => 'Ícone',
                    'add-on-append' => '<i class="glyphicon glyphicon-sunglasses"></i>',
                ),
            ))
            ->add(array(
                'name' => 'parent',
                'type' => 'DoctrineModule\Form\Element\ObjectSelect',
                'options' => array(
                    'label' => 'Departamento superior',
                    'object_manager' => $obj,
                    'target_class' => 'AdministrativeStructure\Entity\Department',
                    'property' => 'departmentName',
                    'display_empty_item' => true,
                    'empty_item_label' => 'Nenhum',
                    'option_attributes' => array(
                        'data-icon' => function (Department $entity) {
                            return $entity->getDepartmentIcon();
                        }
                    ),
                    'add-on-prepend' => '<i class="fa fa-sticky-note-o"></i>',
                ),
            ))
            ->add(array(
                'name' => 'departmentDescription',
                'type' => 'textarea',
                'attributes' => array(
                    'rows' => 6,
                ),
                'options' => array(
                    'label' => 'Descrição',
                ),
            ))
            ->add(array(
                'name' => 'departmentDescription',
                'type' => 'textarea',
                'attributes' => array(
                    'rows' => 6,
                ),
                'options' => array(
                    'label' => 'Descrição',
                ),
            ))
            ->add(array(
                'name' => 'isActive',
                'type' => 'checkbox',
                'options' => array(
                    'label' => 'Ativo / Inativo',
                ),
            ))
        ;
    }

    public function getInputFilterSpecification()
    {
        return array(
            'departmentName' => array(
                'required' => true,
                'filters' => array(
                    array('name' => 'StringTrim'),
                    array('name' => 'StripTags'),
                    array(
                        'name' => 'StringToUpper',
                        'options' => [
                            'encoding' => 'UTF-8',
                        ],
                    ),
                ),
                'validators' => array(
                    array(
                        'name' => 'StringLength',
                        'options' => array(
                            'min' => 5,
                            'max' => 50,
                        ),
                    ),
                ),
            ),
            'departmentIcon' => array(
                'required' => true,
            ),
            'departmentDescription' => array(
                'required' => true,
                'filters' => array(
                    array('name' => 'StringTrim'),
                ),
                'validators' => array(
                    array(
                        'name' => 'StringLength',
                        'options' => array(
                            'min' => 10,
                        ),
                    ),
                ),
            ),
            'parent' => array(
                'required' => false,
            ),
            'isActive' => array(
                'required' => false,
            ),
        );
    }

}
