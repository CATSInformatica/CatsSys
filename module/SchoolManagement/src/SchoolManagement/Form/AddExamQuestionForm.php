<?php

namespace SchoolManagement\Form;

use Doctrine\Common\Persistence\ObjectManager;
use DoctrineModule\Stdlib\Hydrator\DoctrineObject as DoctrineHydrator;
use SchoolManagement\Entity\ExamQuestion;
use SchoolManagement\Form\Fieldset\AddExamQuestionFieldset;
use Zend\Form\Form;

/**
 * Description of AddExamQuestionForm
 *
 * @author Gabriel Pereira <rickardch@gmail.com>
 */
class AddExamQuestionForm extends Form
{

    public function __construct(ObjectManager $obj, $typeOfQuestion = ExamQuestion::QUESTION_TYPE_CLOSED,
        $numberOfAnswers = AddExamQuestionFieldset::DEFAULT_NUMBER_OF_ANSWERS)
    {
        parent::__construct('add-exam-question');
        $this->setHydrator(new DoctrineHydrator($obj));
        $examQuestionFieldset = new AddExamQuestionFieldset($obj, $typeOfQuestion, $numberOfAnswers);
        $examQuestionFieldset->setUseAsBaseFieldset(true);
        $this->add($examQuestionFieldset);

        $this
            ->add(array(
                'name' => 'addAlternative',
                'type' => 'button',
                'attributes' => array(
                    'class' => 'btn-success btn-flat',
                    'id' => 'add-alternative-btn',
                ),
                'options' => array(
                    'fontAwesome' => 'plus',
                ),
            ))
            ->add(array(
                'name' => 'removeAlternative',
                'type' => 'button',
                'attributes' => array(
                    'class' => 'btn-danger btn-flat',
                    'id' => 'remove-alternative-btn',
                ),
                'options' => array(
                    'fontAwesome' => 'minus',
                ),
            ))
            ->add(array(
                'name' => 'submit',
                'type' => 'button',
                'attributes' => array(
                    'class' => 'btn btn-primary btn-block',
                    'id' => 'add-question-btn',
                    'value' => 'Adicionar Questão',
                ),
                'options' => [
                    'label' => 'Salvar',
                ],
            ))
        ;
    }

}
