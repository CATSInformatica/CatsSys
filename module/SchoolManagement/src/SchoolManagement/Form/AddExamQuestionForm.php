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
                'name' => 'addAlternative',
                'type' => 'button',
                'attributes' => array(
                    'class' => 'btn-success',
                    'id' => 'add-alternative-btn',
                ),
                'options' => array(
                    'label' => 'Adicionar Alternativa',
                ),
            ))
            ->add(array(
                'name' => 'removeAlternative',
                'type' => 'button',
                'attributes' => array(
                    'class' => 'btn-danger',
                    'id' => 'remove-alternative-btn',
                ),
                'options' => array(
                    'label' => 'Remover Alternativa',
                ),
            ))
            ->add(array(
                'name' => 'submit',
                'type' => 'button',
                'attributes' => array(
                    'class' => 'btn btn-primary btn-block',
                    'id' => 'add-question-btn',
                    'value' => 'Adicionar Questão',
                )
            ))
        ;
    }

}
