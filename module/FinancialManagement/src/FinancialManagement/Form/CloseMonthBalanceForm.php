<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace FinancialManagement\Form;

use Doctrine\Common\Persistence\ObjectManager;
use DoctrineModule\Stdlib\Hydrator\DoctrineObject as DoctrineHydrator;
use FinancialManagement\Form\Fieldset\CloseMonthBalanceFieldset;
use Zend\Form\Form;

/**
 * Description of CloseMonthBalanceForm
 *
 * @author Gabriel Pereira <rickardch@gmail.com>
 */
class CloseMonthBalanceForm extends Form
{

    public function __construct(ObjectManager $obj)
    {
        parent::__construct('close_month_balance_form');
        
        $this->setHydrator(new DoctrineHydrator($obj));
        $monthBalance = new CloseMonthBalanceFieldset($obj);
        $monthBalance->setUseAsBaseFieldset(true);
        $this->add($monthBalance);

        $this->add(array(
            'name' => 'submit',
            'type' => 'submit',
            'attributes' => array(
                'class' => 'btn btn-primary btn-block',
                'value' => 'Fechar mês',
            )
        ));
    }

}
