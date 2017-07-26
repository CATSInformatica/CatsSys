<?php

namespace Recruitment\Form\Fieldset;

use Doctrine\Common\Persistence\ObjectManager;
use DoctrineModule\Stdlib\Hydrator\DoctrineObject as DoctrineHydrator;
use Recruitment\Entity\Registration;
use Zend\InputFilter\InputFilterProviderInterface;

/**
 * Description of VolunteerRegistrationFieldset
 *
 * @author Márcio Dias <marciojr91@gmail.com>
 */
final class VolunteerRegistrationFieldset extends RegistrationFieldset implements InputFilterProviderInterface
{

    public function __construct(ObjectManager $obj, $options = null)
    {
        $this->setHydrator(new DoctrineHydrator($obj))
            ->setObject(new Registration());

        if (is_array($options) && !array_key_exists('interview', $options)) {
            throw new \InvalidArgumentException('The `options` array must contain the key `interview`');
        }

        parent::__construct($obj, $options);
        
        $this->get('person')->get('personFirstName')->setAttribute('placeholder', 'Nome');
        $this->get('person')->get('personFirstName')->setLabel('Nome (inclua todos, se mais de um)*');
        $this->get('person')->get('personLastName')->setLabel('Sobrenome (inclua todos, se mais de um)*');

        $this
            ->add(array(
                'name' => 'occupation',
                'type' => 'textarea',
                'attributes' => array(
                    'rows' => 5,
                ),
                'options' => array(
                    'label' => 'Ocupação (acadêmica e/ou profissional)*',
                ),
            ))
            ->add(array(
                'name' => 'education',
                'type' => 'textarea',
                'attributes' => array(
                    'rows' => 5,
                ),
                'options' => array(
                    'label' => 'Fez algum curso (técnico, linguas, etc) ? Qual?*',
                ),
            ))
            ->add(array(
                'name' => 'volunteerWork',
                'type' => 'textarea',
                'attributes' => array(
                    'rows' => 5,
                ),
                'options' => array(
                    'label' => 'O que pensa sobre trabalho voluntário? Já fez? Descreva*',
                ),
            ))
            ->add(array(
                'name' => 'howAndWhenKnowUs',
                'type' => 'textarea',
                'attributes' => array(
                    'rows' => 5,
                ),
                'options' => array(
                    'label' => 'Como e quando conheceu o CATS?*',
                ),
            ))
            ->add(array(
                'name' => 'extensionProjects',
                'type' => 'textarea',
                'attributes' => array(
                    'rows' => 5,
                ),
                'options' => array(
                    'label' => 'Participa de outro projeto de extensão?*',
                ),
            ))
            ->add(array(
                'name' => 'whyWorkWithUs',
                'type' => 'textarea',
                'attributes' => array(
                    'rows' => 5,
                ),
                'options' => array(
                    'label' => 'Por que escolheu se inscrever no CATS? Tentou outros projetos?*',
                ),
            ))
            ->add(array(
                'name' => 'volunteerWithUs',
                'type' => 'textarea',
                'attributes' => array(
                    'rows' => 5,
                ),
                'options' => array(
                    'label' => 'O que espera do trabalho voluntário no CATS?*',
                ),
            ))
            ->add(array(
                'name' => 'responsibility',
                'type' => 'radio',
                'options' => array(
                    'label' => 'Responsabilidade*',
                    'value_options' => array(
                        Registration::SELF_EVALUATION_LEVEL_1 => Registration::SELF_EVALUATION_LEVEL_1,
                        Registration::SELF_EVALUATION_LEVEL_2 => Registration::SELF_EVALUATION_LEVEL_2,
                        Registration::SELF_EVALUATION_LEVEL_3 => Registration::SELF_EVALUATION_LEVEL_3,
                        Registration::SELF_EVALUATION_LEVEL_4 => Registration::SELF_EVALUATION_LEVEL_4,
                        Registration::SELF_EVALUATION_LEVEL_5 => Registration::SELF_EVALUATION_LEVEL_5,
                    ),
                    'inline' => true,
                ),
            ))
            ->add(array(
                'name' => 'proactive',
                'type' => 'radio',
                'attributes' => array(
                ),
                'options' => array(
                    'label' => 'Proatividade*',
                    'value_options' => array(
                        Registration::SELF_EVALUATION_LEVEL_1 => Registration::SELF_EVALUATION_LEVEL_1,
                        Registration::SELF_EVALUATION_LEVEL_2 => Registration::SELF_EVALUATION_LEVEL_2,
                        Registration::SELF_EVALUATION_LEVEL_3 => Registration::SELF_EVALUATION_LEVEL_3,
                        Registration::SELF_EVALUATION_LEVEL_4 => Registration::SELF_EVALUATION_LEVEL_4,
                        Registration::SELF_EVALUATION_LEVEL_5 => Registration::SELF_EVALUATION_LEVEL_5,
                    ),
                    'inline' => true,
                ),
            ))
            ->add(array(
                'name' => 'volunteerSpirit',
                'type' => 'radio',
                'attributes' => array(
                ),
                'options' => array(
                    'label' => 'Espírito Voluntário',
                    'value_options' => array(
                        Registration::SELF_EVALUATION_LEVEL_1 => Registration::SELF_EVALUATION_LEVEL_1,
                        Registration::SELF_EVALUATION_LEVEL_2 => Registration::SELF_EVALUATION_LEVEL_2,
                        Registration::SELF_EVALUATION_LEVEL_3 => Registration::SELF_EVALUATION_LEVEL_3,
                        Registration::SELF_EVALUATION_LEVEL_4 => Registration::SELF_EVALUATION_LEVEL_4,
                        Registration::SELF_EVALUATION_LEVEL_5 => Registration::SELF_EVALUATION_LEVEL_5,
                    ),
                    'inline' => true,
                ),
            ))
            ->add(array(
                'name' => 'commitment',
                'type' => 'radio',
                'attributes' => array(
                ),
                'options' => array(
                    'label' => 'Comprometimento*',
                    'value_options' => array(
                        Registration::SELF_EVALUATION_LEVEL_1 => Registration::SELF_EVALUATION_LEVEL_1,
                        Registration::SELF_EVALUATION_LEVEL_2 => Registration::SELF_EVALUATION_LEVEL_2,
                        Registration::SELF_EVALUATION_LEVEL_3 => Registration::SELF_EVALUATION_LEVEL_3,
                        Registration::SELF_EVALUATION_LEVEL_4 => Registration::SELF_EVALUATION_LEVEL_4,
                        Registration::SELF_EVALUATION_LEVEL_5 => Registration::SELF_EVALUATION_LEVEL_5,
                    ),
                    'inline' => true,
                ),
            ))
            ->add(array(
                'name' => 'teamWork',
                'type' => 'radio',
                'attributes' => array(
                ),
                'options' => array(
                    'label' => 'Trabalho em Grupo*',
                    'value_options' => array(
                        Registration::SELF_EVALUATION_LEVEL_1 => Registration::SELF_EVALUATION_LEVEL_1,
                        Registration::SELF_EVALUATION_LEVEL_2 => Registration::SELF_EVALUATION_LEVEL_2,
                        Registration::SELF_EVALUATION_LEVEL_3 => Registration::SELF_EVALUATION_LEVEL_3,
                        Registration::SELF_EVALUATION_LEVEL_4 => Registration::SELF_EVALUATION_LEVEL_4,
                        Registration::SELF_EVALUATION_LEVEL_5 => Registration::SELF_EVALUATION_LEVEL_5,
                    ),
                    'inline' => true,
                ),
            ))
            ->add(array(
                'name' => 'efficiency',
                'type' => 'radio',
                'attributes' => array(
                ),
                'options' => array(
                    'label' => 'Eficiência*',
                    'value_options' => array(
                        Registration::SELF_EVALUATION_LEVEL_1 => Registration::SELF_EVALUATION_LEVEL_1,
                        Registration::SELF_EVALUATION_LEVEL_2 => Registration::SELF_EVALUATION_LEVEL_2,
                        Registration::SELF_EVALUATION_LEVEL_3 => Registration::SELF_EVALUATION_LEVEL_3,
                        Registration::SELF_EVALUATION_LEVEL_4 => Registration::SELF_EVALUATION_LEVEL_4,
                        Registration::SELF_EVALUATION_LEVEL_5 => Registration::SELF_EVALUATION_LEVEL_5,
                    ),
                    'inline' => true,
                ),
            ))
            ->add(array(
                'name' => 'courtesy',
                'type' => 'radio',
                'attributes' => array(
                ),
                'options' => array(
                    'label' => 'Cortesia*',
                    'value_options' => array(
                        Registration::SELF_EVALUATION_LEVEL_1 => Registration::SELF_EVALUATION_LEVEL_1,
                        Registration::SELF_EVALUATION_LEVEL_2 => Registration::SELF_EVALUATION_LEVEL_2,
                        Registration::SELF_EVALUATION_LEVEL_3 => Registration::SELF_EVALUATION_LEVEL_3,
                        Registration::SELF_EVALUATION_LEVEL_4 => Registration::SELF_EVALUATION_LEVEL_4,
                        Registration::SELF_EVALUATION_LEVEL_5 => Registration::SELF_EVALUATION_LEVEL_5,
                    ),
                    'inline' => true,
                ),
            ))
        ;

        if ($options['interview']) {
            $this->add(new VolunteerInterviewFieldset($obj));
        }
    }

    public function getInputFilterSpecification()
    {
        return array(
            'occupation' => array(
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
                            'max' => 700,
                            'inclusive' => true,
                        ),
                    ),
                ),
            ),
            'education' => array(
                'required' => true,
                'filters' => array(
                    array('name' => 'StringTrim'),
                    array('name' => 'StripTags'),
                ),
                'validators' => array(
                    array(
                        'name' => 'StringLength',
                        'options' => array(
                            'min' => 0,
                            'max' => 700,
                            'inclusive' => true,
                        ),
                    ),
                ),
            ),
            'volunteerWork' => array(
                'required' => true,
                'filters' => array(
                    array('name' => 'StringTrim'),
                    array('name' => 'StripTags'),
                ),
                'validators' => array(
                    array(
                        'name' => 'StringLength',
                        'options' => array(
                            'min' => 0,
                            'max' => 700,
                            'inclusive' => true,
                        ),
                    ),
                ),
            ),
            'howAndWhenKnowUs' => array(
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
                            'max' => 700,
                            'inclusive' => true,
                        ),
                    ),
                ),
            ),
            'extensionProjects' => array(
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
                            'max' => 400,
                            'inclusive' => true,
                        ),
                    ),
                ),
            ),
            'whyWorkWithUs' => array(
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
                            'max' => 700,
                            'inclusive' => true,
                        ),
                    ),
                ),
            ),
            'volunteerWithUs' => array(
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
                            'max' => 700,
                            'inclusive' => true,
                        ),
                    ),
                ),
            ),
            'responsibility' => array(
                'required' => true,
            ),
            'proactive' => array(
                'required' => true,
            ),
            'volunteerSpirit' => array(
                'required' => true,
            ),
            'commitment' => array(
                'required' => true,
            ),
            'teamWork' => array(
                'required' => true,
            ),
            'efficiency' => array(
                'required' => true,
            ),
            'courtesy' => array(
                'required' => true,
            ),
        );
    }

}
