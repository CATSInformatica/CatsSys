<?php

/*
 * Copyright (C) 2016 Gabriel Pereira <rickardch@gmail.com>
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

namespace SchoolManagement\Form\Fieldset;

use Doctrine\Common\Persistence\ObjectManager;
use DoctrineModule\Stdlib\Hydrator\DoctrineObject as DoctrineHydrator;
use Zend\Form\Fieldset;
use SchoolManagement\Entity\Warning;
use Zend\InputFilter\InputFilterProviderInterface;

/**
 * Description of GiveWarningFieldset
 *
 * @author Gabriel Pereira <rickardch@gmail.com>
 */
class GiveWarningFieldset extends Fieldset implements InputFilterProviderInterface
{
    
    public function __construct(ObjectManager $objectManager, $warningTypeNames)
    {
        parent::__construct('give-warning-fieldset');
        
        $this->setHydrator(new DoctrineHydrator($objectManager))
            ->setObject(new Warning());
        
        $this        
            ->add(array(
                'name' => 'enrollment',
                'type' => 'Zend\Form\Element\Select',
                'options' => array(
                    'label' => 'Aluno',
                    'disable_inarray_validator' => true
                ),
                'attributes' => array(
                    'type' => 'select',
                    'id' => 'students-input'
                )
            ))  
            ->add(array(
                'name' => 'warningType',
                'type' => 'Zend\Form\Element\Select',
                'options' => array(
                    'label' => 'Tipo de Advertência',
                    'value_options' => $warningTypeNames,
                ),
                'attributes' => array(
                    'type' => 'select',
                )
            ))         
            ->add(array(
                'name' => 'warning_date',
                'type' => 'Zend\Form\Element\Date',
                'options' => array(
                    'label' => 'Data da Advertência',
                    'add-on-prepend' => '<i class="glyphicon glyphicon-calendar"></i>',
                ),
                'attributes' => array(
                    'type' => 'text',
                    'class' => 'datepicker text-center',
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
        ));
    }

    public function getInputFilterSpecification()
    {
        return array(
            'enrollment' => array(
                'required' => true,
            ),
            'warningType' => array(
                'required' => true,
            ),
            'warning_date' => array(
                'required' => true,
                'filters' => array(
                    array('name' => 'StripTags'),
                    array('name' => 'StringTrim'),
                    array(
                        'name' => 'Callback',
                        'options' => array(
                            'callback' => function($value) {
                                if (is_string($value)) {
                                    $value = \DateTime::createFromFormat('d/m/Y', $value);
                                }
                                return $value;
                            },
                        ),
                    ),
                ),
            ),
            'warning_comment' => array(
                'required' => false,
                'filters'  => array(
                    array('name' => 'StripTags'),
                    array('name' => 'StringTrim'),
                ),
                'validators' => array(
                    array(
                        'name'    => 'StringLength',
                        'options' => array(
                            'encoding' => 'UTF-8',
                            'min'      => 1,
                            'max'      => 500,
                        ),
                    ),
                ),                
            )
        );
    }
    
}
