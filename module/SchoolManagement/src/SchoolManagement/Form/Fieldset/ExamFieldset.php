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
use SchoolManagement\Entity\Exam;
use Zend\InputFilter\InputFilterProviderInterface;

/**
 * Description of ExamFieldset
 *
 * @author Gabriel Pereira <rickardch@gmail.com>
 */
class ExamFieldset extends Fieldset implements InputFilterProviderInterface
{
    
    public function __construct(ObjectManager $obj)
    {
        parent::__construct('exam-fieldset');

        $this->setHydrator(new DoctrineHydrator($obj))
            ->setObject(new Exam());

        $this
            ->add(array(
                'name' => 'examName',
                'type' => 'text',
                'options' => array(
                    'label' => 'Nome do simulado',
                ),
                'attributes' => array(
                    'id' => 'exam-name-input',
                    'class' => 'form-control',
                    'placeholder' => 'Ex: 1º Simulado Oficial do CATS - 2016',
                ),
            ))
            ->add(array(
                'name' => 'examDate',
                'type' => 'DateTime',
                'options' => array(
                    'label' => 'Data do simulado',
                    'add-on-prepend' => '<i class="glyphicon glyphicon-calendar"></i>',
                    'format' => 'd/m/Y'
                ),
                'attributes' => array(
                    'class' => 'text-center datepicker',
                    'id' => 'exam-day',
                ),
            ))
        ;
    }

    public function getInputFilterSpecification()
    {
        return array(
            'examName' => array(
                'required' => true,
                'filters' => array(
                    array('name' => 'StripTags'),
                ),
                'validators' => array(
                    array(
                        'name' => 'Zend\Validator\StringLength',
                        'options' => array(
                            'min' => 3,
                            'max' => 80,
                        )
                    ),
                ),
            ),
            'examDate' => array(
                'required' => false,
                'filters' => array(
                    array('name' => 'StripTags'),
                    array('name' => 'StringTrim'),
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
                        'name' => 'Date',
                        'options' => array(
                            'format' => 'Y-m-d',
                        ),
                    ),
                ),
            ),            
        );
    }
    
}
