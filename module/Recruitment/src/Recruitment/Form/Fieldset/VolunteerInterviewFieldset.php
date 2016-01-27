<?php

namespace Recruitment\Form\Fieldset;

use Doctrine\Common\Persistence\ObjectManager;
use DoctrineModule\Stdlib\Hydrator\DoctrineObject as DoctrineHydrator;
use Recruitment\Entity\VolunteerInterview;
use Zend\Form\Fieldset;
use Zend\InputFilter\InputFilterProviderInterface;

/**
 * Description of PreInterviewFieldset
 *
 * @author Márcio Dias <marciojr91@gmail.com>
 */
class PreInterviewFieldset extends Fieldset implements InputFilterProviderInterface
{

    public function __construct(ObjectManager $obj)
    {
        parent::__construct('volunteerInterview');

        $this->setHydrator(new DoctrineHydrator($obj))
            ->setObject(new VolunteerInterview());

        $this
            ->add(array(
                'name' => 'proactivity',
                'type' => 'textarea',
                'options' => array(
                    'label' => '',
                ),
                'attributes' => array(
                    'rows' => 6,
                ),
            ))
            ->add(array(
                'name' => 'commitmentAndEfficiency',
                'type' => 'textarea',
                'options' => array(
                    'label' => '',
                ),
                'attributes' => array(
                    'rows' => 6,
                ),
            ))
            ->add(array(
                'name' => 'volunteerProfile',
                'type' => 'textarea',
                'options' => array(
                    'label' => '',
                ),
                'attributes' => array(
                    'rows' => 6,
                ),
            ))
            ->add(array(
                'name' => 'interest',
                'type' => 'textarea',
                'options' => array(
                    'label' => '',
                ),
                'attributes' => array(
                    'rows' => 6,
                ),
            ))
            ->add(array(
                'name' => 'interpersonalRelationship',
                'type' => 'textarea',
                'options' => array(
                    'label' => '',
                ),
                'attributes' => array(
                    'rows' => 6,
                ),
            ))
            ->add(array(
                'name' => 'personality',
                'type' => 'textarea',
                'options' => array(
                    'label' => '',
                ),
                'attributes' => array(
                    'rows' => 6,
                ),
            ))
            ->add(array(
                'name' => 'coherence',
                'type' => 'textarea',
                'options' => array(
                    'label' => '',
                ),
                'attributes' => array(
                    'rows' => 6,
                ),
            ))
            ->add(array(
                'name' => 'testClass',
                'type' => 'textarea',
                'options' => array(
                    'label' => '',
                ),
                'attributes' => array(
                    'rows' => 6,
                ),
            ))
            ->add(array(
                'name' => 'result',
                'type' => 'textarea',
                'options' => array(
                    'label' => '',
                ),
                'attributes' => array(
                    'rows' => 6,
                ),
            ))
        ;
    }

    public function getInputFilterSpecification()
    {
        return array(
            'proactivity' => array(
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
                            'max' => 500,
                        ),
                    ),
                ),
            ),
            'commitmentAndEfficiency' => array(
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
                            'max' => 500,
                        ),
                    ),
                ),
            ),
            'volunteerProfile' => array(
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
                            'max' => 500,
                        ),
                    ),
                ),
            ),
            'interest' => array(
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
                            'max' => 500,
                        ),
                    ),
                ),
            ),
            'interpersonalRelationship' => array(
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
                            'max' => 500,
                        ),
                    ),
                ),
            ),
            'personality' => array(
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
                            'max' => 500,
                        ),
                    ),
                ),
            ),
            'coherence' => array(
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
                            'max' => 500,
                        ),
                    ),
                ),
            ),
            'testClass' => array(
                'required' => false,
                'filters' => array(
                    array('name' => 'StringTrim'),
                    array('name' => 'StripTags'),
                ),
                'validators' => array(
                    array(
                        'name' => 'StringLength',
                        'options' => array(
                            'min' => 3,
                            'max' => 500,
                        ),
                    ),
                ),
            ),
            'result' => array(
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
                            'max' => 500,
                        ),
                    ),
                ),
            ),
        );
    }

}
