<?php

namespace SchoolManagement\Form\Fieldset;

use Zend\Form\Fieldset;
use DoctrineModule\Stdlib\Hydrator\DoctrineObject as DoctrineHydrator;
use Zend\InputFilter\InputFilterProviderInterface;
use SchoolManagement\Entity\ExamAnswer;

/**
 * Description of AddExamAnswerFieldset
 *
 * @author Gabriel Pereira <rickardch@gmail.com>
 */
class AddExamAnswerFieldset extends Fieldset implements InputFilterProviderInterface
{

    public function __construct($obj)
    {
        parent::__construct('exam-answer');
        
        $this->setHydrator(new DoctrineHydrator($obj))
            ->setObject(new ExamAnswer());

        $this->add(array(
            'name' => 'examAnswerDescription',
            'type' => 'textarea',
            'attributes' => array(
                'rows' => 4,
            ),
            'options' => array(
                'label' => 'Alternativa 1',
            ),
        ));
    }

    public function getInputFilterSpecification()
    {
        return array(
            'examAnswerDescription' => array(
                'required' => true,
                'filters' => array(
                    array('name' => 'StringTrim'),
                    array('name' => 'StripTags'),
                ),
            ),
        );
    }

}
