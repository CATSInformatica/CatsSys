<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace FinancialManagement\Form\Fieldset;

use Doctrine\Common\Persistence\ObjectManager;
use DoctrineModule\Stdlib\Hydrator\DoctrineObject as DoctrineHydrator;
use FinancialManagement\Entity\Expense;
use Zend\Form\Fieldset;
use Zend\InputFilter\InputFilterProviderInterface;

/**
 * Description of AddExpenseFieldset
 *
 * @author gabriel
 */
class AddExpenseFieldset extends Fieldset implements InputFilterProviderInterface
{

    public function __construct(ObjectManager $obj)
    {
        parent::__construct('addExpenseForm');

        $this->setHydrator(new DoctrineHydrator($obj))
                ->setObject(new Expense());

        $this
                ->add(array(
                    'name' => 'decription',
                    'type' => 'textarea',
                    'options' => array(
                        'label' => 'Descrição da despesa',
                    ),
                    'attributes' => array(
                        'rows' => 6,
                    ),
                ))
                ->add(array(
                    'name' => 'isFixed',
                    'type' => 'radio',
                    'options' => array(
                        'label' => 'A despesa é fixa (mensal)?',
                        'value_options' => array(
                            'Sim',
                            'Não',
                        ),
                    ),
                ))
                ->add(array(
                    'name' => 'expenseAmount',
                    'type' => 'text',
                    'attributes' => array(
                        'placeholder' => 'Ex: 200',
                    ),
                    'options' => array(
                        'label' => 'Valor',
                    ),
                ))
                ->add(array(
                    'name' => 'expenseDate',
                    'type' => 'Date',
                    'attributes' => array(
                        'type' => 'text',
                        'class' => 'datepicker',
                    ),
                    'options' => array(
                        'label' => 'Data da despesa',
                        'add-on-prepend' => '<i class="glyphicon glyphicon-calendar"></i>',
                    ),
                ))
        ;
    }

    public function getInputFilterSpecification()
    {
        return array(
            'description' => array(
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
                            'max' => 500,
                        ),
                    ),
                ),
            ),
            'isFixed' => array(
                'required' => true,
            ),
            'expenseAmount' => array(
                'required' => true,
                'filters' => array(
                    array('name' => 'StripTags')
                ),
            ),
            'expenseDate' => array(
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
        );
    }

}
