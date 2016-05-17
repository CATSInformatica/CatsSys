<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace FinancialManagement\Form;

use Doctrine\Common\Persistence\ObjectManager;
use DoctrineModule\Stdlib\Hydrator\DoctrineObject as DoctrineHydrator;
use FinancialManagement\Form\Fieldset\CashFlowFieldset;
use Zend\Form\Form;

/**
 * Description of CashFlowForm
 *
 * @author Gabriel Pereira <rickardch@gmail.com>
 */
class CashFlowForm extends Form
{

    public function __construct(ObjectManager $obj)
    {
        parent::__construct('cash_flow');
        
        $this->setHydrator(new DoctrineHydrator($obj));
        $cashFlow = new CashFlowFieldset($obj);
        $cashFlow->setUseAsBaseFieldset(true);
        $this->add($cashFlow);

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
