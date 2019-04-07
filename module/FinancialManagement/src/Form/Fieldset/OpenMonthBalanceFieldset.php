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
 * Description of OpenMonthBalanceFieldset
 *
 * @author Gabriel Pereira <rickardch@gmail.com>
 */
class OpenMonthBalanceFieldset extends Fieldset implements InputFilterProviderInterface
{

    public function __construct(ObjectManager $obj)
    {
        parent::__construct('open_month_balance_fieldset');

        $this->setHydrator(new DoctrineHydrator($obj))
                ->setObject(new MonthlyBalance());

        $this
                ->add(array(
                    'name' => 'monthlyBalanceOpen',
                    'type' => 'Date',
                    'attributes' => array(
                        'type' => 'text',
                        'class' => 'text-center datepicker',
                    ),
                    'options' => array(
                        'label' => 'Data',
                        'add-on-prepend' => '<i class="glyphicon glyphicon-calendar"></i>',
                    ),
                ))
                ->add(array(
                    'name' => 'monthlyBalanceProjectedRevenue',
                    'type' => 'number',
                    'options' => array(
                        'label' => 'Receita Prevista',
                    ),
                    'attributes' => array(
                        'step' => 'any',
                    ),
                ))
                ->add(array(
                    'name' => 'monthlyBalanceProjectedExpense',
                    'type' => 'number',
                    'options' => array(
                        'label' => 'Despesa Prevista',
                    ),
                    'attributes' => array(
                        'step' => 'any',
                    ),
                ))
        ;
    }

    public function getInputFilterSpecification()
    {
        return array(
            'monthlyBalanceOpen' => array(
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
            'monthlyBalanceProjectedRevenue' => array(
                'required' => true,
                'validators' => array(
                    array(
                        'name' => 'Zend\I18n\Validator\IsFloat',
                        'options' => array(
                            'min' => 0,
                        ),
                    ),
                ),
            ),
            'monthlyBalanceProjectedExpense' => array(
                'required' => true,
                'validators' => array(
                    array(
                        'name' => 'Zend\I18n\Validator\IsFloat',
                        'options' => array(
                            'min' => 0,
                        ),
                    ),
                ),
            ),
        );
    }

}
