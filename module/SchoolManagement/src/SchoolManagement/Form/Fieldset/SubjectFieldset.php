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
        parent::__construct('subject');

        $this->setHydrator(new DoctrineHydrator($obj))
            ->setObject(new Subject());

        $this
            ->add(array(
                'name' => 'subjectName',
                'type' => 'text',
                'attributes' => array(
                    'placeholder' => 'Ex: História do Brasil',
                ),
                'options' => array(
                    'label' => 'Nome da disciplina',
                ),
                'attributes' => array(
                    'id' => 'subject-name',
                ),
            ))
            ->add(array(
                'name' => 'subjectParent',
                'type' => 'select',
                'options' => array(
                    'label' => 'Disciplina a qual pertence',
                    'value_options' => $this->getSubjects($obj),
                ),
                'attributes' => array(
                    'id' => 'subject-parent',
                ),
            ))
            ->add(array(
                'name' => 'subjectDescription',
                'type' => 'textarea',
                'attributes' => array(
                    'rows' => 4,
                ),
                'options' => array(
                    'label' => 'Descrição da disciplina',
                ),
            ))
        ;
    }

    protected function getSubjects($obj)
    {
        $subjects = $obj->getRepository('SchoolManagement\Entity\Subject')->findAll();
        $subjectNames = [];
        $subjectNames[0] = '---';
        foreach ($subjects as $s) {
            $subjectNames[$s->getSubjectId()] = $s->getSubjectName();
        }
        return $subjectNames;
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
