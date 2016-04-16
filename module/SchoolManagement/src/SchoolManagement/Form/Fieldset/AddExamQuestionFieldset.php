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

    const DEFAULT_NUMBER_OF_ANSWERS = 5;

    public function __construct(ObjectManager $obj, $questionType = ExamQuestion::QUESTION_TYPE_CLOSED,
        $numberOfAnswers = self::DEFAULT_NUMBER_OF_ANSWERS
    )
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
                    'value' => $questionType,
                ),
            ))
            ->add(array(
                'name' => 'examQuestionEnunciation',
                'type' => 'textarea',
                'attributes' => array(
                    'rows' => 15,
                    'id' => 'question-enunciation',
                ),
                'options' => array(
                    'label' => 'Enunciado',
                ),
            ))
            ->add(array(
                'type' => 'Zend\Form\Element\Radio',
                'name' => 'correctAnswer',
                'options' => array(
                    'label' => 'Resposta Correta',
                    'value_options' => self::generateAnswers($numberOfAnswers),
                    'disable_inarray_validator' => true,
                    'inline' => true,
                ),
            ))
            ->add(array(
                'name' => 'answerOptions',
                'type' => 'Zend\Form\Element\Collection',
                'options' => array(
                    'count' => $numberOfAnswers,
                    'should_create_template' => true,
                    'template_placeholder' => '__placeholder__',
                    'target_element' => new AddExamAnswerFieldset($obj),
                ),
            ))
        ;
    }

    /**
     * Gera um ventor no formato
     *  [
     *      0 => 'A',
     *      1 => 'B',
     *      .
     *      .
     *      .
     *      ($numberOfAnswers - 1) => ascii_character_of('A' + $numberOfAnswers - 1);
     *  ]
     * 
     * @param type $numberOfAnswers
     * @return type
     */
    protected function generateAnswers($numberOfAnswers)
    {
        $ansArr = [];
        for ($i = 0; $i < $numberOfAnswers; $i++) {
            $ansArr[] = chr(ord('A') + $i);
        }

        return $ansArr;
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
            ),
            'correctAnswer' => array(
                'required' => false,
            ),
        );
    }

}
