<?php

namespace SchoolManagement\Form;

use Doctrine\Common\Persistence\ObjectManager;
use DoctrineModule\Stdlib\Hydrator\DoctrineObject as DoctrineHydrator;
use SchoolManagement\Form\Fieldset\AddExamQuestionFieldset;
use Zend\Form\Form;

/**
 * Description of AddExamQuestionForm
 *
 * @author Gabriel Pereira <rickardch@gmail.com>
 */
class AddExamQuestionForm extends Form
{

    public function __construct(ObjectManager $obj)
    {
        parent::__construct('add-exam-question');

        $this->setHydrator(new DoctrineHydrator($obj));
        
        $examQuestionFieldset = new AddExamQuestionFieldset($obj);
        $examQuestionFieldset->setUseAsBaseFieldset(true);
        $this->add($examQuestionFieldset);

        $this
            ->add(array(
                'name' => 'submit',
                'type' => 'submit',
                'attributes' => array(
                    'class' => 'btn btn-primary btn-block',
                    'id' => 'add-question-btn',
                    'value' => 'Adicionar Questão',
                )
            ))
        ;
    }

}
