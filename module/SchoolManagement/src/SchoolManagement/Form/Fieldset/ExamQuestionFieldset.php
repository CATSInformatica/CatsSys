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
class ExamQuestionFieldset extends Fieldset implements InputFilterProviderInterface
{

    const DEFAULT_NUMBER_OF_ANSWERS = 5;
    private $questionType;

    public function __construct(ObjectManager $obj, $questionType = ExamQuestion::QUESTION_TYPE_CLOSED,
        $numberOfAnswers = self::DEFAULT_NUMBER_OF_ANSWERS, $name = null, $options = []
    )
    {
        parent::__construct($name, $options);
        
        $this->questionType = $questionType;
        $this->setHydrator(new DoctrineHydrator($obj))
            ->setObject(new ExamQuestion());

        $this
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
                'name' => 'examQuestionType',
                'type' => 'select',
                'options' => array(
                    'label' => 'Tipo de questão',
                    'value_options' => array(
                        [
                            'value' => ExamQuestion::QUESTION_TYPE_CLOSED,
                            'label' => ExamQuestion::QUESTION_TYPE_CLOSED_DESC,
                            'selected' => $questionType === ExamQuestion::QUESTION_TYPE_CLOSED,
                        ],
                        [
                            'value' => ExamQuestion::QUESTION_TYPE_OPEN,
                            'label' => ExamQuestion::QUESTION_TYPE_OPEN_DESC,
                            'selected' => $questionType === ExamQuestion::QUESTION_TYPE_OPEN,
                        ]
                    ),
                ),
                'attributes' => array(
                    'id' => 'question-type',
                ),
            ))
            ->add(array(
                'name' => 'subject',
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
                'name' => 'answerOptions',
                'type' => 'Zend\Form\Element\Collection',
                'options' => array(
                    'count' => $numberOfAnswers,
                    'should_create_template' => true,
                    'allow_add' => true,
                    'allow_remove' => true,
                    'template_placeholder' => '__placeholder__',
                    'target_element' => new ExamAnswerFieldset($obj, 'exam-answer'),
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
        ));
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
        $correctAnswerIsRequired = $this->questionType == ExamQuestion::QUESTION_TYPE_CLOSED ? true : false;
        return array(
            'subject' => array(
                'required' => true,
            ),
            'examQuestionType' => array(
                'required' => true,
            ),
            'examQuestionEnunciation' => array(
                'required' => true,
            ),
            'correctAnswer' => array(
                'required' => $correctAnswerIsRequired,
            ),
        );
    }

}
