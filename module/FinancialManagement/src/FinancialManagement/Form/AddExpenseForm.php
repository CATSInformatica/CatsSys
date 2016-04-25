<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace FinancialManagement\Form;

use Doctrine\Common\Persistence\ObjectManager;
use DoctrineModule\Stdlib\Hydrator\DoctrineObject as DoctrineHydrator;
use FinancialManagement\Entity\Expense;
use FinancialManagement\Form\Fieldset\AddExpenseFieldset;
use Zend\Form\Form;

/**
 * Description of AddExpenseForm
 *
 * @author Gabriel Pereira <rickardch@gmail.com>
 */
class AddExpenseForm extends Form
{

    public function __construct(ObjectManager $obj, $options = null)
    {
        parent::__construct('volunteer_interview');
        $this->setHydrator(new DoctrineHydrator($obj));

        $addExpense = new AddExpenseFieldset($obj, $options);
        $addExpense->setUseAsBaseFieldset(true);
        $this->add($addExpense);

        $this->add(array(
            'name' => 'submit',
            'type' => 'submit',
            'attributes' => array(
                'class' => 'btn btn-primary btn-block',
                'value' => 'Adicionar',
            )
        ));
    }

}
