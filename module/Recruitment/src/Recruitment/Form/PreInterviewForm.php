<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Recruitment\Form;

use Doctrine\Common\Persistence\ObjectManager;
use DoctrineModule\Stdlib\Hydrator\DoctrineObject as DoctrineHydrator;
use Recruitment\Form\Fieldset\RegistrationFieldset;
use Zend\Form\Form;

/**
 * Description of InterviewForm
 *
 * @author Márcio Dias <marciojr91@gmail.com>
 */
class PreInterviewForm extends Form
{

    public function __construct(ObjectManager $obj, $options = null)
    {
        parent::__construct('pre-interview');
        $this->setHydrator(new DoctrineHydrator($obj));
        
        // Add the user fieldset, and set it as the base fieldset
        $registrationFieldset = new RegistrationFieldset($obj, $options);
        $registrationFieldset->setUseAsBaseFieldset(true);
        $this->add($registrationFieldset);
        
        $this->add(array(
            'name' => 'submit',
            'type' => 'submit',
            'attributes' => array(
                'class' => 'btn btn-primary btn-block',
                'value' => 'Concluir',
            )
        ));
    }

}
