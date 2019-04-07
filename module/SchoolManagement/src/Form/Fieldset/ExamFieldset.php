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
                'name' => 'name',
                'type' => 'text',
                'options' => array(
                    'label' => 'Nome do simulado',
                ),
                'attributes' => array(
                    'id' => 'exam-name-input',
                    'class' => 'form-control',
                    'placeholder' => 'Ex: 1º Vestibulinho 2016 - Prova do Dia 1',
                ),
            ))
            ->add(array(
                'name' => 'date',
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
            ->add(array(
                'name' => 'startTime',
                'type' => 'DateTime',
                'options' => array(
                    'label' => 'Hora de início',
                    'add-on-prepend' => '<i class="glyphicon glyphicon-time"></i>',
                    'format' => 'H:i'
                ),
                'attributes' => array(
                    'class' => 'text-center datepicker',
                    'id' => 'exam-start-time',
                ),
            ))
            ->add(array(
                'name' => 'endTime',
                'type' => 'DateTime',
                'options' => array(
                    'label' => 'Hora de término',
                    'add-on-prepend' => '<i class="glyphicon glyphicon-time"></i>',
                    'format' => 'H:i'
                ),
                'attributes' => array(
                    'class' => 'text-center datepicker',
                    'id' => 'exam-end-time',
                ),
            ))
            ->add(array(
                'name' => 'examContent',
                'type' => 'checkbox',
                'options' => array(
                    'label' => 'Hora de término',
                    'add-on-prepend' => '<i class="glyphicon glyphicon-time"></i>',
                ),
                'attributes' => array(
                    'class' => 'text-center datepicker',
                    'id' => 'exam-end-time',
                ),
            ))
            ->add(array(
                'type' => 'DoctrineModule\Form\Element\ObjectSelect',
                'name' => 'examContent',
                'options' => array(
                    'label' => 'Conteúdo',
                    'object_manager' => $obj,
                    'target_class' => 'SchoolManagement\Entity\ExamContent',
                    'label_generator' => function($targetEntity) {
                        return $targetEntity->getExamContentId() . ' - ' . substr($targetEntity->getDescription(), 0, 100);
                    },
                )
            ))
        ;
    }

    public function getInputFilterSpecification()
    {
        return array(
            'name' => array(
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
            'date' => array(
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
            'startTime' => array(
                'required' => false,
                'filters' => array(
                    array('name' => 'StripTags'),
                    array('name' => 'StringTrim'),
                ),
                'validators' => array(
                    array(
                        'name' => 'Date',
                        'options' => array(
                            'format' => 'H:i',
                        ),
                    ),
                ),
            ),
            'endTime' => array(
                'required' => false,
                'filters' => array(
                    array('name' => 'StripTags'),
                    array('name' => 'StringTrim'),
                ),
                'validators' => array(
                    array(
                        'name' => 'Date',
                        'options' => array(
                            'format' => 'H:i',
                        ),
                    ),
                ),
            ),
        );
    }
}
