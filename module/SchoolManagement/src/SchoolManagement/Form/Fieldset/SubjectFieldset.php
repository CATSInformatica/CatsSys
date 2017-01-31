<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace SchoolManagement\Form\Fieldset;

use Doctrine\Common\Persistence\ObjectManager;
use DoctrineModule\Stdlib\Hydrator\DoctrineObject as DoctrineHydrator;
use Zend\Form\Fieldset;
use SchoolManagement\Entity\Subject;
use Zend\InputFilter\InputFilterProviderInterface;

/**
 * Description of SubjectFieldset
 *
 * @author Gabriel Pereira <rickardch@gmail.com>
 */
class SubjectFieldset extends Fieldset implements InputFilterProviderInterface
{

    public function __construct(ObjectManager $obj)
    {
        parent::__construct('subject-fieldset');

        $this->setHydrator(new DoctrineHydrator($obj))
            ->setObject(new Subject());

        $this
            ->add(array(
                'name' => 'subjectName',
                'type' => 'text',
                'options' => array(
                    'label' => 'Nome da disciplina',
                ),
                'attributes' => array(
                    'class' => 'subject-name-input',
                ),
            ))
            ->add(array(
                'name' => 'subjectParent',
                'type' => 'number',
                'attributes' => array(
                    'min' => '0',
                    'step' => '1',
                    'class' => 'subject-parent-input',
                ),
            ))
            ->add(array(
                'name' => 'subjectDescription',
                'type' => 'textarea',
                'attributes' => array(
                    'rows' => 2,
                    'class' => 'subject-description-input',
                ),
                'options' => array(
                    'label' => 'Descrição da disciplina',
                ),
            ))
        ;
    }

    public function getInputFilterSpecification()
    {
        return array(
            'subjectName' => array(
                'required' => true,
                'filters' => array(
                    array('name' => 'StripTags'),
                    array(
                        'name' => 'StringToUpper',
                        'options' => array(
                            'encoding' => 'UTF-8',
                        ),
                    ),
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
            'subjectParent' => array(
                'required' => true,
            ),
            'subjectDescription' => array(
                'required' => true,
                'filters' => array(
                    array('name' => 'StringTrim'),
                    array('name' => 'StripTags'),
                ),
                'validators' => array(
                    array(
                        'name' => 'StringLength',
                        'options' => array(
                            'min' => 3,
                            'max' => 200,
                            'inclusive' => true,
                        ),
                    ),
                ),
            ),
        );
    }

}
