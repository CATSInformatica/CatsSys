<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Authentication\Form;

/**
 * Description of UserForm
 *
 * @author marcio
 */

use Doctrine\Common\Persistence\ObjectManager;
use DoctrineModule\Stdlib\Hydrator\DoctrineObject as DoctrineHydrator;
use Zend\Form\Form;

class UserForm extends Form
{

    public function __construct(ObjectManager $obj)
    {
        parent::__construct('user');
        $this->setHydrator(new DoctrineHydrator($obj));
        
        $userFieldset = new Fieldset\UserFieldset($obj);
        $userFieldset->setUseAsBaseFieldset(true);
        $this->add($userFieldset);
        
        $this->add(array(
            'name' => 'submit',
            'type' => 'Submit',
            'attributes' => array(
                'value' => 'Salvar',
                'id' => 'submitbutton',
                'class' => 'btn btn-primary btn-block'
            ),
        ));
    }
}
