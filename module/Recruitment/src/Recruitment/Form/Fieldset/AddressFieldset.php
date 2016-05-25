<?php

namespace Recruitment\Form\Fieldset;

use Doctrine\Common\Persistence\ObjectManager;
use Recruitment\Entity\Address;
use Zend\Form\Fieldset;
use Zend\InputFilter\InputFilterProviderInterface;
use DoctrineModule\Stdlib\Hydrator\DoctrineObject as DoctrineHydrator;

/**
 * Description of AddressFieldset
 *
 * @author Márcio Dias <marciojr91@gmail.com>
 */
class AddressFieldset extends Fieldset implements InputFilterProviderInterface
{

    public function __construct(ObjectManager $obj)
    {
        parent::__construct('address');

        $this->setHydrator(new DoctrineHydrator($obj))
            ->setObject(new Address());

        $this->add(array(
                'name' => 'addressPostalCode',
                'type' => 'text',
                'options' => array(
                    'label' => 'CEP',
                    'add-on-append' => '<i class="fa fa-search"></i>',
                ),
                'attributes' => array(
                    'id' => 'cep',
                    'class' => 'input-sm',
                ),
            ))
            ->add(array(
                'name' => 'addressState',
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
            ))
            ->add(array(
                'name' => 'addressCity',
                'type' => 'text',
                'options' => array(
                    'label' => 'Cidade',
                ),
                'attributes' => array(
                    'class' => 'input-sm',
                ),
            ))
            ->add(array(
                'name' => 'addressNeighborhood',
                'type' => 'text',
                'options' => array(
                    'label' => 'Bairro',
                ),
                'attributes' => array(
                    'class' => 'input-sm',
                ),
            ))
            ->add(array(
                'name' => 'addressStreet',
                'type' => 'text',
                'options' => array(
                    'label' => 'Rua',
                ),
                'attributes' => array(
                    'class' => 'input-sm',
                ),
            ))
            ->add(array(
                'name' => 'addressNumber',
                'type' => 'number',
                'options' => array(
                    'label' => 'Número',
                ),
                'attributes' => array(
                    'class' => 'input-sm',
                ),
            ))
            ->add(array(
                'name' => 'addressComplement',
                'type' => 'text',
                'options' => array(
                    'label' => 'Complemento',
                ),
                'attributes' => array(
                    'class' => 'input-sm',
                ),
        ));
    }

    public function getInputFilterSpecification()
    {
        return array(
            'addressPostalCode' => array(
                'name' => 'addressPostalCode',
                'required' => true,
                'validators' => array(
                    array(
                        'name' => 'Zend\Validator\Regex',
                        'options' => array(
                            'pattern' => '/^[0-9]{5}-[0-9]{3}$/',
                        ),
                    ),
                ),
            ),
            'addressState' => array(
                'name' => 'state',
                'required' => true,
            ),
            'addressCity' => array(
                'name' => 'city',
                'required' => true,
                'filters' => array(
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
                            'max' => 50,
                        ),
                    ),
                ),
            ),
            'addressNeighborhood' => array(
                'name' => 'neighborhood',
                'required' => true,
                'filters' => array(
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
                            'max' => 50,
                        ),
                    ),
                ),
            ),
            'addressStreet' => array(
                'name' => 'street',
                'required' => true,
                'filters' => array(
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
                            'min' => 1,
                            'max' => 100,
                        ),
                    ),
                ),
            ),
            'addressNumber' => array(
                'name' => 'number',
                'required' => false,
                'validators' => array(
                    array('name' => 'Zend\Validator\Digits'),
                    array(
                        'name' => 'LessThan',
                        'options' => array(
                            'max' => 100000,
                        ),
                    ),
                ),
            ),
            'addressComplement' => array(
                'name' => 'complement',
                'required' => false,
                'filters' => array(
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
                            'min' => 1,
                            'max' => 100,
                        ),
                    ),
                ),
            ),
        );
    }

}
