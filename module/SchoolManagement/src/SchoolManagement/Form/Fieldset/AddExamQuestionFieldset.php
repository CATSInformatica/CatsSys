<?php

namespace SchoolManagement\Form\Fieldset;

use Doctrine\Common\Persistence\ObjectManager;
use DoctrineModule\Stdlib\Hydrator\DoctrineObject as DoctrineHydrator;
use Zend\Form\Fieldset;
use SchoolManagement\Entity\ExamQuestion;
use Zend\InputFilter\InputFilterProviderInterface;

/**
 * Description of AddExamQuestionFieldset
 *
 * @author Gabriel Pereira <rickardch@gmail.com>
 */
class AddExamQuestionFieldset extends Fieldset implements InputFilterProviderInterface
{

    public function __construct(ObjectManager $obj)
    {
        parent::__construct('exam-question');

        $this->setHydrator(new DoctrineHydrator($obj))
            ->setObject(new ExamQuestion());

        $this
            ->add(array(
                'name' => 'subjectId',
                'type' => 'select',
                'options' => array(
                    'label' => 'Disciplina',
                    'value_options' => $this->getSubjects($obj),
                ),
                'attributes' => array(
                    'id' => 'subject',
                ),
            ))
            ->add(array(
                'name' => 'examQuestionType',
                'type' => 'select',
                'options' => array(
                    'label' => 'Tipo de questão',
                    'value_options' => array(
                        ExamQuestion::QUESTION_TYPE_CLOSED => ExamQuestion::QUESTION_TYPE_CLOSED_DESC,
                        ExamQuestion::QUESTION_TYPE_OPEN => ExamQuestion::QUESTION_TYPE_OPEN_DESC,
                    ),
                ),
                'attributes' => array(
                    'id' => 'question-type',
                ),
            ))
            ->add(array(
                'name' => 'examQuestionEnunciation',
                'type' => 'textarea',
                'attributes' => array(
                    'rows' => 5,
                ),
                'options' => array(
                    'label' => 'Enunciado',
                ),
                'attributes' => array(
                    'id' => 'question-enunciation',
                ),
            ))
            ->add(array(
                'type' => 'Zend\Form\Element\Radio',
                'name' => 'correctAnswer',
                'options' => array(
                    'label' => 'Alternativa Correta',
                    'value_options' => array(
                        '0' => 'Nenhum',
                        /* Inseridos dinamicamente */
                    ),
                    'disable_inarray_validator' => true,
                ),
            ))
            ->add(array(
                'name' => 'answerOptions',
                'type' => 'Zend\Form\Element\Collection',
                'options' => array(
                    'count' => 0,
                    'should_create_template' => true,
                    'template_placeholder' => '__placeholder__',
                    'target_element' => new AddExamAnswerFieldset($obj),
                ),
                'attributes' => array(
                    'id' => 'alternatives-fieldset',
                )
            ))
        ;
    }

    protected function getSubjects($obj)
    {
        $subjects = $obj->getRepository('SchoolManagement\Entity\Subject')->findAll();
        $subjectNames = [];
        foreach ($subjects as $s) {
            $subjectNames[$s->getSubjectId()] = $s->getSubjectName();
        }
        return $subjectNames;
    }

    public function getInputFilterSpecification()
    {
        return array(
            'subjectId' => array(
                'required' => true,
            ),
            'examQuestionType' => array(
                'required' => true,
            ),
            'examQuestionEnunciation' => array(
                'required' => true,
                'filters' => array(
                    array('name' => 'StringTrim'),
                    array('name' => 'StripTags'),
                ),
            ),
            'correctAnswer' => array(
                'required' => false,
            ),
        );
    }

}
