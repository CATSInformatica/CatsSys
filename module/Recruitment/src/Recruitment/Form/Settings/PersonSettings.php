<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Recruitment\Form\Settings;

use Recruitment\Entity\Person;

/**
 * Description of PersonSettings
 *
 * @author Márcio Dias <marciojr91@gmail.com>
 */
class PersonSettings
{

    public static function createPersonElements($suffix = '')
    {
        $elements = [];

        $elements['person_firstname'] = array(
            'name' => 'person_firstname' . $suffix,
            'type' => 'text',
            'attributes' => array(
                'placeholder' => 'Primeiro Nome',
            ),
            'options' => array(
                'label' => 'Nome*',
            ),
        );

        $elements['person_lastname'] = array(
            'name' => 'person_lastname' . $suffix,
            'type' => 'text',
            'attributes' => array(
                'placeholder' => 'Sobrenome',
            ),
            'options' => array(
                'label' => 'Sobrenome*',
            ),
        );

        $elements['person_gender'] = array(
            'name' => 'person_gender' . $suffix,
            'type' => 'radio',
            'options' => array(
                'label' => 'Sexo*',
                'value_options' => array(
                    Person::GENDER_M => 'Masculino',
                    Person::GENDER_F => 'Feminino',
                ),
            ),
        );

        $elements['person_birthday'] = array(
            'name' => 'person_birthday' . $suffix,
            'type' => 'text',
            'attributes' => array(
                'class' => 'datepicker',
            ),
            'options' => array(
                'label' => 'Nascimento*',
                'add-on-prepend' => '<i class="glyphicon glyphicon-calendar"></i>',
            ),
        );

        $elements['person_cpf'] = CpfSettings::createCpfElement($suffix);

        $elements['person_rg'] = array(
            'name' => 'person_rg' . $suffix,
            'type' => 'text',
            'attributes' => array(
                'placeholder' => 'Ex: MG-99.999.999',
            ),
            'options' => array(
                'label' => 'RG*',
            ),
        );

        $elements['person_phone'] = array(
            'name' => 'person_phone' . $suffix,
            'type' => 'text',
            'attributes' => array(
                'placeholder' => 'Ex: (35)99999-9999',
            ),
            'options' => array(
                'label' => 'Telefone ou celular*',
            ),
        );

        $elements['person_email'] = array(
            'name' => 'person_email' . $suffix,
            'type' => 'email',
            'attributes' => array(
                'placeholder' => 'email@exemplo.com',
            ),
            'options' => array(
                'label' => 'Endereço de Email*',
            ),
        );

        $elements['person_confirm_email'] = array(
            'name' => 'person_confirm_email' . $suffix,
            'type' => 'email',
            'attributes' => array(
                'placeholder' => 'email@exemplo.com',
            ),
            'options' => array(
                'label' => 'Reinsira o endereço de email*',
            ),
        );

        return $elements;
    }

    public static function createPersonFilters($suffix = '')
    {
        $filters = [];

        $filters['person_firstname'] = array(
            'name' => 'person_firstname' . $suffix,
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
                        'max' => 80,
                    )
                ),
            ),
        );
        $filters['person_lastname'] = array(
            'name' => 'person_lastname' . $suffix,
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
                        'max' => 200,
                    ),
                ),
            ),
        );

        $filters['person_gender'] = array(
            'name' => 'person_gender' . $suffix,
            'required' => true,
        );

        $filters['person_birthday'] = array(
            'name' => 'person_birthday' . $suffix,
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
        );

        $filters['person_cpf'] = CpfSettings::createCpfFilter($suffix);

        $filters['person_rg'] = array(
            'name' => 'person_rg' . $suffix,
            'required' => true,
            'filters' => array(
                array('name' => 'StringTrim'),
                array('name' => 'StringToUpper'),
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
        );

        $filters['person_phone'] = array(
            'name' => 'person_phone' . $suffix,
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
        );
        $filters['person_email'] = array(
            'name' => 'person_email' . $suffix,
            'required' => true,
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
            )
        );
        $filters['person_confirm_email'] = array(
            'name' => 'person_confirm_email' . $suffix,
            'required' => true,
            'validators' => array(
                array(
                    'name' => 'Zend\Validator\Identical',
                    'options' => array(
                        'token' => 'person_email' . $suffix,
                    ),
                ),
            ),
        );

        return $filters;
    }

}
