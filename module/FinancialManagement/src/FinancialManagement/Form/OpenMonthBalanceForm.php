<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace FinancialManagement\Form;

use Doctrine\Common\Persistence\ObjectManager;
use DoctrineModule\Stdlib\Hydrator\DoctrineObject as DoctrineHydrator;
use FinancialManagement\Form\Fieldset\OpenMonthBalanceFieldset;
use Zend\Form\Form;

/**
 * Description of OpenMonthBalanceForm
 *
 * @author Gabriel Pereira <rickardch@gmail.com>
 */
class OpenMonthBalanceForm extends Form
{

    public function __construct(ObjectManager $obj, $options = null)
    {
        parent::__construct('open_month_balance_form');
        
        $this->setHydrator(new DoctrineHydrator($obj));
        $monthBalance = new OpenMonthBalanceFieldset($obj);
        $monthBalance->setUseAsBaseFieldset(true);
        $this->add($monthBalance);

        $this->add(array(
            'name' => 'submit',
            'type' => 'submit',
            'attributes' => array(
                'class' => 'btn btn-primary btn-block',
                'value' => 'Abrir mês',
            )
        ));
    }

}
