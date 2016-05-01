<?php

namespace SchoolManagement\Form\Fieldset;

use Doctrine\Common\Persistence\ObjectManager;
use DoctrineModule\Stdlib\Hydrator\DoctrineObject as DoctrineHydrator;
use SchoolManagement\Entity\ExamAnswer;
use Zend\Form\Fieldset;
use Zend\InputFilter\InputFilterProviderInterface;

/**
 * Description of AddExamAnswerFieldset
 *
 * @author Gabriel Pereira <rickardch@gmail.com>
 */
class ExamAnswerFieldset extends Fieldset implements InputFilterProviderInterface
{

    public function __construct(ObjectManager $obj, $name = null, $options = [])
    {
        parent::__construct($name, $options);

        $this->setHydrator(new DoctrineHydrator($obj))
            ->setObject(new ExamAnswer());

        $this->add(array(
            'name' => 'examAnswerDescription',
            'type' => 'textarea',
            'attributes' => array(
                'rows' => 15,
                'class' => 'textarea',
            ),
            'options' => array(
                'label' => 'Resposta',
            ),
        ));
    }

    public function getInputFilterSpecification()
    {
        return array(
            'examAnswerDescription' => array(
                'required' => true,
            ),
        );
    }

}
