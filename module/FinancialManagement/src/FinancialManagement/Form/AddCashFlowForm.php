<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace FinancialManagement\Form;

use Doctrine\Common\Persistence\ObjectManager;
use DoctrineModule\Stdlib\Hydrator\DoctrineObject as DoctrineHydrator;
use FinancialManagement\Form\Fieldset\AddCashFlowFieldset;
use Zend\Form\Form;

/**
 * Description of AddCashFlowForm
 *
 * @author Gabriel Pereira <rickardch@gmail.com>
 */
class AddCashFlowForm extends Form
{

    public function __construct(ObjectManager $obj)
    {
        parent::__construct('add_cash_flow');
        
        $this->setHydrator(new DoctrineHydrator($obj));
        $addCashFlow = new AddCashFlowFieldset($obj);
        $addCashFlow->setUseAsBaseFieldset(true);
        $this->add($addCashFlow);

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
