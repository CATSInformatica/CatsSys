<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Recruitment\Form\Fieldset;

use Doctrine\Common\Persistence\ObjectManager;
use DoctrineModule\Stdlib\Hydrator\DoctrineObject as DoctrineHydrator;
use Recruitment\Entity\Person;
use Zend\Form\Fieldset;
use Zend\InputFilter\InputFilterProviderInterface;

/**
 * Description of PersonFieldset
 *
 * @author Márcio Dias <marciojr91@gmail.com>
 */
class PersonFieldset extends Fieldset implements InputFilterProviderInterface
{

    /**
     * 
     * @param ObjectManager $obj
     * @param array $options array com as chaves 'relative', mostrar/ocultar parentes 
     * e 'address' mostrar/ocultar endereço
     * @throws \InvalidArgumentException
     */
    public function __construct(ObjectManager $obj,
        $options = [
        'relative' => false,
        'address' => true,
        'social_media' => false,
    ], $name = 'person')
    {

        if (is_array($options) &&
            (!array_key_exists('relative', $options) ||
            !array_key_exists('address', $options) ||
            !array_key_exists('social_media', $options))) {
            throw new \InvalidArgumentException('`options` array must contain the keys `relative`, `address`'
            . ' and `social_media`');
        }

        parent::__construct($name);

        $this->setHydrator(new DoctrineHydrator($obj))
            ->setObject(new Person());

        if ($options['relative']) {
            $relativeFieldset = new RelativeFieldset($obj);

            $this->add(array(
                'type' => 'Zend\Form\Element\Collection',
                'name' => 'relatives',
                'options' => array(
                    'count' => 1,
                    'target_element' => $relativeFieldset,
                ),
            ));
        }

        if ($options['address']) {
            $addressFieldset = new AddressFieldset($obj);
            $this->add(array(
                'type' => 'Zend\Form\Element\Collection',
                'name' => 'addresses',
                'options' => array(
                    'count' => 1,
                    'target_element' => $addressFieldset,
                ),
            ));
        }

        $this->add(array(
            'name' => 'personFirstName',
            'type' => 'text',
            'attributes' => array(
                'placeholder' => 'Primeiro Nome',
            ),
            'options' => array(
                'label' => 'Nome*',
            ),
        ));

        $this->add(array(
            'name' => 'personLastName',
            'type' => 'text',
            'attributes' => array(
                'placeholder' => 'Sobrenome',
            ),
            'options' => array(
                'label' => 'Sobrenome*',
            ),
        ));

        $this->add(array(
            'name' => 'personGender',
            'type' => 'radio',
            'options' => array(
                'label' => 'Sexo*',
                'value_options' => array(
                    Person::GENDER_M => 'Masculino',
                    Person::GENDER_F => 'Feminino',
                ),
            ),
        ));
        $this->add(array(
            'name' => 'personBirthday',
            'type' => 'Date',
            'attributes' => array(
                'type' => 'text',
                'class' => 'datepicker',
            ),
            'options' => array(
                'label' => 'Nascimento*',
                'add-on-prepend' => '<i class="glyphicon glyphicon-calendar"></i>',
            ),
        ));

        $this->add(array(
            'name' => 'personCpf',
            'type' => 'text',
            'attributes' => array(
                'placeholder' => '999.999.999-99',
            ),
            'options' => array(
                'label' => 'Número do Cpf*',
            ),
        ));

        $this->add(array(
            'name' => 'personRg',
            'type' => 'text',
            'attributes' => array(
                'placeholder' => 'Ex: MG-99.999.999',
            ),
            'options' => array(
                'label' => 'RG*',
            ),
        ));

        $this->add(array(
            'name' => 'personPhone',
            'type' => 'text',
            'attributes' => array(
                'placeholder' => 'Ex: (35)99999-9999',
            ),
            'options' => array(
                'label' => 'Telefone ou celular*',
            ),
        ));

        $this->add(array(
            'name' => 'personEmail',
            'type' => 'email',
            'attributes' => array(
                'placeholder' => 'email@exemplo.com',
            ),
            'options' => array(
                'label' => 'Endereço de Email*',
            ),
        ));

        $this->add(array(
            'name' => 'personEmailConfirm',
            'type' => 'email',
            'attributes' => array(
                'placeholder' => 'email@exemplo.com',
            ),
            'options' => array(
                'label' => 'Reinsira o endereço de email*',
            ),
        ));

        if ($options['social_media']) {
            $this->add(array(
                'name' => 'personSocialMedia',
                'type' => 'text',
                'attributes' => array(
                    'placeholder' => 'https://www.facebook.com/cats.familia',
                ),
                'options' => array(
                    'label' => 'Link para seu perfil no Facebook',
                    'add-on-prepend' => '<a href="https://www.facebook.com" target="_blank">'
                    . '<i class="fa fa-facebook"></i></a>',
                ),
            ));
        }
    }

    public function getInputFilterSpecification()
    {
        return array(
            'personFirstName' => array(
                'required' => true,
                'filters' => array(
                    array('name' => 'StripTags'),
                    array('name' => 'StringTrim'),
                    [
                        'name' => 'StringToUpper',
                        'options' => [
                            'encoding' => 'UTF-8',
                        ]
                    ],
                ),
                'validators' => array(
                    array(
                        'name' => 'Zend\Validator\StringLength',
                        'options' => array(
                            'min' => 3,
                            'max' => 80,
                        )
                    ),
                ),
            ),
            'personLastName' => array(
                'required' => true,
                'filters' => array(
                    array('name' => 'StripTags'),
                    array('name' => 'StringTrim'),
                    [
                        'name' => 'StringToUpper',
                        'options' => [
                            'encoding' => 'UTF-8',
                        ]
                    ],
                ),
                'validators' => array(
                    array(
                        'name' => 'Zend\Validator\StringLength',
                        'options' => array(
                            'min' => 3,
                            'max' => 200,
                        ),
                    ),
                ),
            ),
            'personGender' => array(
                'required' => true,
            ),
            'personBirthday' => array(
                'required' => true,
                'filters' => array(
                    array(
                        'name' => 'Recruitment\Filter\DateToFormat',
                        'options' => array(
                            'inputFormat' => 'd/m/Y',
                            'outputFormat' => 'Y-m-d'
                        ),
                    ),
                ),
                'validators' => array(
                    array(
                        'name' => 'Zend\Validator\Date',
                        'options' => array(
                            'format' => 'Y-m-d',
                        ),
                    ),
                ),
            ),
            'personCpf' => array(
                'required' => true,
                'filters' => array(
                    array('name' => 'StripTags')
                ),
                'validators' => array(
                    array(
                        'name' => 'Recruitment\Validator\Cpf',
                    ),
                ),
            ),
            'personRg' => array(
                'required' => true,
                'filters' => array(
                    array('name' => 'StringTrim'),
                    array(
                        'name' => 'StringToUpper',
                        'options' => [
                            'encoding' => 'UTF-8',
                        ],
                    ),
                ),
                'validadors' => array(
                    array(
                        'name' => 'Zend\Validator\StringLength',
                        'options' => array(
                            'min' => 6,
                            'max' => 25,
                        ),
                    ),
                ),
            ),
            'personPhone' => array(
                'required' => true,
                'filters' => array(
                    array('name' => 'StringTrim')
                ),
                'validators' => array(
                    array(
                        'name' => 'Zend\Validator\StringLength',
                        'options' => array(
                            'min' => 6,
                            'max' => 20,
                        ),
                    ),
                ),
            ),
            'personEmail' => array(
                'required' => true,
                'filters' => [
                    ['name' => 'StringToLower'],
                ],
                'validators' => array(
                    array(
                        'name' => 'Zend\Validator\StringLength',
                        'options' => array(
                            'min' => 9,
                            'max' => 50,
                        ),
                    ),
                    array(
                        'name' => 'Zend\Validator\EmailAddress',
                    ),
                ),
            ),
            'personEmailConfirm' => array(
                'required' => true,
                'validators' => array(
                    array(
                        'name' => 'Zend\Validator\Identical',
                        'options' => array(
                            'token' => 'personEmail',
                        ),
                    ),
                ),
            ),
            'personSocialMedia' => array(
                'required' => false,
            ),
        );
    }

}
