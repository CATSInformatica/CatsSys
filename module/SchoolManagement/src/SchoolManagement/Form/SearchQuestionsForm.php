<?php

namespace SchoolManagement\Form;

use SchoolManagement\Entity\ExamQuestion;
use Zend\Form\Form;
use Zend\InputFilter\InputFilterProviderInterface;
use Doctrine\Common\Persistence\ObjectManager;

/**
 * Description of SearchQuestionsForm
 *
 * @author Gabriel Pereira <rickardch@gmail.com>
 */
class SearchQuestionsForm extends Form implements InputFilterProviderInterface
{

    public function __construct(ObjectManager $obj, $name = null, $options = array())
    {
        parent::__construct($name, $options);
        $subjects = $obj->getRepository('SchoolManagement\Entity\Subject')->findAll();
        
        $this
            ->add(array(
                'name' => 'subject',
                'type' => 'select',
                'options' => array(
                    'label' => 'Disciplina',
                    'value_options' => $this->getSubjectNames($subjects),
                ),
            ))
            ->add(array(
                'name' => 'questionType',
                'type' => 'select',
                'options' => array(
                    'label' => 'Tipo',
                    'value_options' => array(
                        ExamQuestion::QUESTION_TYPE_CLOSED => "Questão Fechada", 
                        ExamQuestion::QUESTION_TYPE_OPEN => "Questão Aberta",
                        -1 => "Todas",
                    ),
                ),
            ))
            ->add(array(
                'name' => 'submit',
                'type' => 'button',
                'attributes' => array(
                    'value' => 'Buscar',
                    'class' => 'btn-primary btn-block',
                ),
                'options' => array(
                    'label' => 'Buscar',
                    'glyphicon' => 'search',
                ),
            ))
        ;
    }

    protected function getSubjectNames($subjects)
    {
        $subjectNames = [];
        foreach($subjects as $subject) {
            $subjectNames[$subject->getSubjectId()] = $subject->getSubjectName();
        }

        return $subjectNames;
    }

    public function getInputFilterSpecification()
    {
        return array(
            'subject' => array(
                'required' => true,
            ),
            'questionType' => array(
                'required' => true,
            ),
        );
    }

}
