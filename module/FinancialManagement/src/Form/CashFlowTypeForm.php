<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace FinancialManagement\Form;

use Doctrine\Common\Persistence\ObjectManager;
use DoctrineModule\Stdlib\Hydrator\DoctrineObject as DoctrineHydrator;
use FinancialManagement\Form\Fieldset\CashFlowTypeFieldset;
use Zend\Form\Form;

/**
 * Description of CashFlowTypeForm
 *
 * @author Gabriel Pereira <rickardch@gmail.com>
 */
class CashFlowTypeForm extends Form
{

    public function __construct(ObjectManager $obj)
    {
        parent::__construct('add_cash_flow_type');

        $this->setHydrator(new DoctrineHydrator($obj));
        $cashFlowType = new CashFlowTypeFieldset($obj);
        $cashFlowType->setUseAsBaseFieldset(true);
        $this->add($cashFlowType);

        $this->add(array(
            'name' => 'submit',
            'type' => 'submit',
            'attributes' => array(
                'class' => 'btn btn-primary btn-block',
                'value' => 'Criar',
            )
        ));
    }

}
