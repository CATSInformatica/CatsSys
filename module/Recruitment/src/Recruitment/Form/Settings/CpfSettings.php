<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Recruitment\Form\Settings;

/**
 * Description of CpfSettings
 *
 * @author Márcio Dias <marciojr91@gmail.com>
 */
class CpfSettings
{

    public static function createCpfElement($suffix = '')
    {
        return array(
            'name' => 'person_cpf' . $suffix,
            'type' => 'text',
            'attributes' => array(
                'placeholder' => '999.999.999-99',
            ),
            'options' => array(
                'label' => 'Número do Cpf*',
            ),
        );
    }

    public static function createCpfFilter($suffix = '')
    {
        return array(
            'name' => 'person_cpf' . $suffix,
            'required' => true,
            'filters' => array(
                array('name' => 'StripTags')
            ),
            'validators' => array(
                array(
                    'name' => 'Recruitment\Validator\Cpf',
                ),
            ),
        );
    }

}
