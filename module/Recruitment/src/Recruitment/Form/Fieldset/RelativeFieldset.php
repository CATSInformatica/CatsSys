<?php

namespace Recruitment\Form\Fieldset;

use Doctrine\Common\Persistence\ObjectManager;
use DoctrineModule\Stdlib\Hydrator\DoctrineObject as DoctrineHydrator;
use Recruitment\Entity\Relative;
use Zend\Form\Fieldset;
use Zend\InputFilter\InputFilterProviderInterface;

/**
 * Description of RelativeFieldset
 *
 * @author Márcio Dias <marciojr91@gmail.com>
 */
class RelativeFieldset extends Fieldset implements InputFilterProviderInterface
{

    public function __construct(ObjectManager $obj, $address = false)
    {
        parent::__construct('relative');

        $this->setHydrator(new DoctrineHydrator($obj))
            ->setObject(new Relative());

        $personFieldset = new PersonFieldset($obj,
            array(
            'relative' => false,
            'address' => $address,
        ), 'relative');

        $this->add($personFieldset);

        $this->add(array(
            'name' => 'relativeRelationship',
            'type' => 'text',
            'attributes' => array(
                'placeholder' => 'Ex: Pai, Avó, ...'
            ),
            'options' => array(
                'label' => 'Grau de parentesco',
            ),
        ));
    }

    public function getInputFilterSpecification()
    {
        return array(
            'relativeRelationship' => array(
                'name' => 'relative_relationship',
                'required' => true,
                'filters' => array(
                    array('name' => 'StripTags'),
                    array('name' => 'StringToUpper'),
                ),
                'validators' => array(
                    array(
                        'name' => 'Zend\Validator\StringLength',
                        'options' => array(
                            'min' => 3,
                            'max' => 50,
                        ),
                    ),
                ),
            ),
        );
    }

}
