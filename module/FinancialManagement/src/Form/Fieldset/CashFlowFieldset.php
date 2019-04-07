<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace FinancialManagement\Form\Fieldset;

use Doctrine\Common\Persistence\ObjectManager;
use DoctrineModule\Stdlib\Hydrator\DoctrineObject as DoctrineHydrator;
use FinancialManagement\Entity\CashFlow;
use FinancialManagement\Entity\CashFlowType;
use Zend\Form\Fieldset;
use Zend\InputFilter\InputFilterProviderInterface;

/**
 * Description of CashFlowFieldset
 *
 * @author Gabriel Pereira <rickardch@gmail.com>
 */
class CashFlowFieldset extends Fieldset implements InputFilterProviderInterface
{

    public function __construct(ObjectManager $obj)
    {
        parent::__construct('cash_flow_fieldset');

        $this->setHydrator(new DoctrineHydrator($obj))
                ->setObject(new CashFlow());

        $this
                ->add(array(
                    'name' => 'cashFlowType',
                    'type' => 'select',
                    'options' => array(
                        'label' => 'Tipo de fluxo',
                        'value_options' => $this->getCashFlowType($obj),
                    ),
                ))
                ->add(array(
                    'name' => 'cashFlowDate',
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
                    'name' => 'cashFlowAmount',
                    'type' => 'number',
                    'options' => array(
                        'label' => 'Valor',
                    ),
                    'attributes' => array(
                        'step' => 'any',
                    ),
                ))
                ->add(array(
                    'name' => 'cashFlowDescription',
                    'type' => 'textarea',
                    'options' => array(
                        'label' => 'Descrição',
                    ),
                    'attributes' => array(
                        'rows' => 6,
                    ),
                ))
                ->add(array(
                    'name' => 'cashFlowObservation',
                    'type' => 'textarea',
                    'options' => array(
                        'label' => 'Observação',
                    ),
                    'attributes' => array(
                        'rows' => 6,
                    ),
                ))
                ->add(array(
                    'name' => 'department',
                    'type' => 'select',
                    'options' => array(
                        'label' => 'Departamento',
                        'value_options' => $this->getDepartments($obj),
                    ),
                ))
                ->add(array(
                    'name' => 'monthlyBalance',
                    'type' => 'select',
                    'options' => array(
                        'label' => 'Mês correspondente',
                        'value_options' => $this->getMonthlyBalances($obj),
                    ),
                ))
        ;
    }

    private function getDepartments($obj)
    {
        $activeDepartments = $obj->getRepository('AdministrativeStructure\Entity\Department')
                ->findBy(array('isActive' => true));
        $activeDepartmentsNames = [];
        $activeDepartmentsNames[0] = 'SEM DEPARTAMENTO ESPECÍFICO';
        foreach ($activeDepartments as $department) {
            $activeDepartmentsNames[$department->getDepartmentId()] = $department->getDepartmentName();
        }
        return $activeDepartmentsNames;
    }

    private function getCashFlowType($obj)
    {
        $cashFlowTypes = $obj->getRepository('FinancialManagement\Entity\CashFlowType')
                ->findAll();
        $cashFlowTypeNames = [];
        foreach ($cashFlowTypes as $cashFlowType) {
            if ($cashFlowType->getCashFlowTypeId() !== CashFlowType::CASH_FLOW_TYPE_MONTHLY_PAYMENT) {
                $cashFlowTypeNames[$cashFlowType->getCashFlowTypeId()] = $cashFlowType->getCashFlowTypeName()
                        . ' [' . CashFlowType::getCashFlowTypeDirectionDescription($cashFlowType->getCashFlowTypeDirection()) . ']';
            }
        }
        return $cashFlowTypeNames;
    }

    private function getMonthlyBalances($obj)
    {
        $openMonthBalance = $obj->getRepository('FinancialManagement\Entity\MonthlyBalance')
                ->findBy(array('monthlyBalanceIsOpen' => true));
        return [$openMonthBalance[0]->getMonthlyBalanceOpen()->format("M \d\\e Y")];
    }

    public function getInputFilterSpecification()
    {
        return array(
            'cashFlowType' => array(
                'required' => true,
            ),
            'cashFlowDate' => array(
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
            'cashFlowAmount' => array(
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
            'cashFlowDescription' => array(
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
            'cashFlowObservation' => array(
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
            'monthlyBalance' => array(
                'required' => true,
            ),
        );
    }

}
