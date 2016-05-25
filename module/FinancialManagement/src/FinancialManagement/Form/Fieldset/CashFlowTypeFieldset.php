<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace FinancialManagement\Form\Fieldset;

use Doctrine\Common\Persistence\ObjectManager;
use DoctrineModule\Stdlib\Hydrator\DoctrineObject as DoctrineHydrator;
use FinancialManagement\Entity\CashFlowType;
use Zend\Form\Fieldset;
use Zend\InputFilter\InputFilterProviderInterface;

/**
 * Description of CashFlowTypeFieldset
 *
 * @author Gabriel Pereira <rickardch@gmail.com>
 */
class CashFlowTypeFieldset extends Fieldset implements InputFilterProviderInterface
{

    public function __construct(ObjectManager $obj)
    {
        parent::__construct('cash_flow_type_fieldset');

        $this->setHydrator(new DoctrineHydrator($obj))
                ->setObject(new CashFlowType());

        $this
                ->add(array(
                    'name' => 'cashFlowTypeDirection',
                    'type' => 'select',
                    'options' => array(
                        'label' => 'Tipo',
                        'value_options' => array(
                            CashFlowType::CASH_FLOW_DIRECTION_OUTFLOW
                            => CashFlowType::CASH_FLOW_DIRECTION_OUTFLOW_DESCRIPTION,
                            CashFlowType::CASH_FLOW_DIRECTION_INFLOW
                            => CashFlowType::CASH_FLOW_DIRECTION_INFLOW_DESCRIPTION
                        ),
                    ),
                ))
                ->add(array(
                    'name' => 'cashFlowTypeName',
                    'type' => 'text',
                    'options' => array(
                        'label' => 'Nome',
                    ),
                ))
                ->add(array(
                    'name' => 'cashFlowTypeDescription',
                    'type' => 'textarea',
                    'options' => array(
                        'label' => 'Descrição',
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
            'cashFlowTypeDirection' => array(
                'required' => true,
            ),
            'cashFlowTypeName' => array(
                'required' => true,
                'filters' => array(
                    array('name' => 'StripTags'),
                    array('name' => 'StringTrim'),
                    array(
                        'name' => 'StringToUpper',
                        'options' => array(
                            'encoding' => 'UTF-8',
                        ),
                    ),
                ),
                'validators' => array(
                    array(
                        'name' => 'Zend\Validator\StringLength',
                        'options' => array(
                            'min' => 3,
                            'max' => 90,
                        ),
                    ),
                ),
            ),
            'cashFlowTypeDescription' => array(
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
                            'max' => 1000,
                        ),
                    ),
                ),
            ),
        );
    }

}
