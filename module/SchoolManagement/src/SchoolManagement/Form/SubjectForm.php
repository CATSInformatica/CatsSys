<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace SchoolManagement\Form;

use Doctrine\Common\Persistence\ObjectManager;
use DoctrineModule\Stdlib\Hydrator\DoctrineObject as DoctrineHydrator;
use SchoolManagement\Form\Fieldset\SubjectFieldset;
use Zend\Form\Form;

/**
 * Description of SubjectForm
 *
 * @author Gabriel Pereira <rickardch@gmail.com>
 */
class SubjectForm extends Form
{
    
    public function __construct(ObjectManager $obj)
    {
        parent::__construct('subject');
        
        $this->setHydrator(new DoctrineHydrator($obj));
        $subjectFieldset = new SubjectFieldset($obj);
        $subjectFieldset->setUseAsBaseFieldset(true);
        $this->add($subjectFieldset);

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
