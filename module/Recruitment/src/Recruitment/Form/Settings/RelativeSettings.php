<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Recruitment\Form\Settings;

/**
 * Description of RelativeSettings
 *
 * @author Márcio Dias <marciojr91@gmail.com>
 */
class RelativeSettings
{

    public static function createRelativeElements($suffix = '')
    {
        $elements['relative_relationship'] = array(
            'name' => 'relative_relationship' . $suffix,
            'type' => 'text',
            'attributes' => array(
                'placeholder' => 'Ex: Pai, Avó, ...'
            ),
            'options' => array(
                'label' => 'Grau de parentesco',
            ),
        );

        return $elements;
    }

    public static function createRelativeFilters($suffix = '')
    {
        $elements['relative_relationship'] = array(
            'name' => 'relative_relationship' . $suffix,
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
                    )
                ),
            ),
        );

        return $elements;
    }

}
