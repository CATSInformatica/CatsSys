<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Recruitment\Form\Settings;

use Recruitment\Entity\Address;

/**
 * Description of AddressElementsSettings
 * Cria as configurações de campos e filtros para endereço
 *
 * @author Márcio Dias <marciojr91@gmail.com>
 */
class AddressSettings
{

    /**
     * @param string $suffix sufixo a ser utilizado para compor o nome do campo
     * @return array contendo os elementos postal_code, state, city, neighborhood, street, number e complement
     */
    public static function createAddressElements($suffix = '')
    {

        $elements = [];

        $elements['postal_code'] = array(
            'name' => 'postal_code' . $suffix,
            'type' => 'text',
            'options' => array(
                'label' => 'CEP',
                'add-on-append' => '<i class="fa fa-search"></i>',
            ),
            'attributes' => array(
                'id' => 'cep',
                'class' => 'input-sm',
            ),
        );

        $elements['state'] = array(
            'name' => 'state' . $suffix,
            'type' => 'select',
            'options' => array(
                'label' => 'UF',
                'empty_option' => '',
                'value_options' => array(
                    Address::STATE_AC => Address::STATE_AC,
                    Address::STATE_AL => Address::STATE_AL,
                    Address::STATE_AM => Address::STATE_AM,
                    Address::STATE_AP => Address::STATE_AP,
                    Address::STATE_BA => Address::STATE_BA,
                    Address::STATE_CE => Address::STATE_CE,
                    Address::STATE_DF => Address::STATE_DF,
                    Address::STATE_ES => Address::STATE_ES,
                    Address::STATE_GO => Address::STATE_GO,
                    Address::STATE_MA => Address::STATE_MA,
                    Address::STATE_MG => Address::STATE_MG,
                    Address::STATE_MS => Address::STATE_MS,
                    Address::STATE_MT => Address::STATE_MT,
                    Address::STATE_PA => Address::STATE_PA,
                    Address::STATE_PB => Address::STATE_PB,
                    Address::STATE_PE => Address::STATE_PE,
                    Address::STATE_PI => Address::STATE_PI,
                    Address::STATE_PR => Address::STATE_PR,
                    Address::STATE_RJ => Address::STATE_RJ,
                    Address::STATE_RN => Address::STATE_RN,
                    Address::STATE_RO => Address::STATE_RO,
                    Address::STATE_RR => Address::STATE_RR,
                    Address::STATE_RS => Address::STATE_RS,
                    Address::STATE_SC => Address::STATE_SC,
                    Address::STATE_SE => Address::STATE_SE,
                    Address::STATE_SP => Address::STATE_SP,
                    Address::STATE_TO => Address::STATE_TO,
                ),
            ),
            'attributes' => array(
                'class' => 'input-sm',
            ),
        );

        $elements['city'] = array(
            'name' => 'city' . $suffix,
            'type' => 'text',
            'options' => array(
                'label' => 'Cidade',
            ),
            'attributes' => array(
                'class' => 'input-sm',
            ),
        );

        $elements['neighborhood'] = array(
            'name' => 'neighborhood' . $suffix,
            'type' => 'text',
            'options' => array(
                'label' => 'Bairro',
            ),
            'attributes' => array(
                'class' => 'input-sm',
            ),
        );

        $elements['street'] = array(
            'name' => 'street' . $suffix,
            'type' => 'text',
            'options' => array(
                'label' => 'Rua',
            ),
            'attributes' => array(
                'class' => 'input-sm',
            ),
        );

        $elements['number'] = array(
            'name' => 'number' . $suffix,
            'type' => 'text',
            'options' => array(
                'label' => 'Número',
            ),
            'attributes' => array(
                'class' => 'input-sm',
            ),
        );

        $elements['complement'] = array(
            'name' => 'complement' . $suffix,
            'type' => 'text',
            'options' => array(
                'label' => 'Complemento',
            ),
            'attributes' => array(
                'class' => 'input-sm',
            ),
        );

        return $elements;
    }

    /**
     * @param string $suffix sufixo a ser utilizado para compor o nome do filtro
     * @return array contendo os elementos postal_code, state, city, neighborhood, street, number e complement
     */
    public static function createAddressFilters($suffix = '')
    {
        $filters = [];

        $filters['postal_code'] = array(
            'name' => 'postal_code' . $suffix,
            'required' => true,
            'validators' => array(
                array(
                    'name' => 'Zend\Validator\Regex',
                    'options' => array(
                        'pattern' => '/^[0-9]{5}-[0-9]{3}$/',
                    ),
                ),
            ),
        );

        $filters['state'] = array(
            'name' => 'state' . $suffix,
            'required' => true,
        );

        $filters['city'] = array(
            'name' => 'city' . $suffix,
            'required' => true,
            'filters' => array(
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
        );

        $filters['neighborhood'] = array(
            'name' => 'neighborhood' . $suffix,
            'required' => true,
            'filters' => array(
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
        );

        $filters['street'] = array(
            'name' => 'street' . $suffix,
            'required' => true,
            'filters' => array(
                array('name' => 'StringToUpper'),
            ),
            'validators' => array(
                array(
                    'name' => 'Zend\Validator\StringLength',
                    'options' => array(
                        'min' => 1,
                        'max' => 100,
                    ),
                ),
            ),
        );

        $filters['number'] = array(
            'name' => 'number' . $suffix,
            'required' => false,
            'validators' => array(
                array('name' => 'Zend\Validator\Digits'),
            ),
        );

        $filters['complement'] = array(
            'name' => 'complement' . $suffix,
            'required' => false,
            'filters' => array(
                array('name' => 'StringToUpper'),
            ),
            'validators' => array(
                array(
                    'name' => 'Zend\Validator\StringLength',
                    'options' => array(
                        'min' => 1,
                        'max' => 100,
                    ),
                ),
            ),
        );

        return $filters;
    }

}
