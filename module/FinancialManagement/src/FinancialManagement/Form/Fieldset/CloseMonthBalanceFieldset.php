<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace FinancialManagement\Form\Fieldset;

use Doctrine\Common\Persistence\ObjectManager;
use DoctrineModule\Stdlib\Hydrator\DoctrineObject as DoctrineHydrator;
use FinancialManagement\Entity\MonthlyBalance;
use Zend\Form\Fieldset;
use Zend\InputFilter\InputFilterProviderInterface;

/**
 * Description of CloseMonthBalanceFieldset
 *
 * @author Gabriel Pereira <rickardch@gmail.com>
 */
class CloseMonthBalanceFieldset extends Fieldset implements InputFilterProviderInterface
{

    public function __construct(ObjectManager $obj)
    {
        parent::__construct('close_month_balance_fieldset');

        $this->setHydrator(new DoctrineHydrator($obj))
                ->setObject(new MonthlyBalance());

        $this
                ->add(array(
                    'name' => 'monthlyBalanceObservation',
                    'type' => 'textarea',
                    'options' => array(
                        'label' => 'Observação',
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
            'monthlyBalanceObservation' => array(
                'required' => false,
                'filters' => array(
                    array('name' => 'StringTrim'),
                    array('name' => 'StripTags'),
                ),
                'validators' => array(
                    array(
                        'name' => 'StringLength',
                        'options' => array(
                            'max' => 1000,
                        ),
                    ),
                ),
            ),
        );
    }

}
