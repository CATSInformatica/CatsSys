<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Recruitment\Form\Fieldset;

use Doctrine\Common\Persistence\ObjectManager;
use DoctrineModule\Stdlib\Hydrator\DoctrineObject as DoctrineHydrator;
use Recruitment\Entity\Registration;
use Recruitment\Form\Fieldset\PersonFieldset;
use Zend\Form\Fieldset;
use Zend\InputFilter\InputFilterProviderInterface;

/**
 * Description of RegistrationFieldset
 *
 * @author Márcio Dias <marciojr91@gmail.com>
 */
class RegistrationFieldset extends Fieldset implements InputFilterProviderInterface
{

    public function __construct(ObjectManager $obj, $options = null)
    {
        if (is_array($options) && !array_key_exists('person', $options)) {
            throw new \InvalidArgumentException('`options` array must contain the key `person`');
        }

        parent::__construct('registration');

        $this->setHydrator(new DoctrineHydrator($obj))
            ->setObject(new Registration());

        $this->add(
            
            new PersonFieldset($obj, $options['person'])
        );

        $this->add(array(
            'name' => 'recruitmentKnowAbout',
            'type' => 'DoctrineModule\Form\Element\ObjectMultiCheckbox',
            'options' => array(
                'label' => 'Por qual(is) meio(s) você soube do processo seletivo de alunos?*',
                'object_manager' => $obj,
                'target_class' => 'Recruitment\Entity\RecruitmentKnowAbout',
                'property' => 'recruitmentKnowAboutDescription',
            ),
        ));
    }

    public function getInputFilterSpecification()
    {
        return array(
            'recruitmentKnowAbout' => array(
                'required' => true,
            ),
        );
    }

}
