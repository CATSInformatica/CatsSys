<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Authentication\Form;

use Zend\InputFilter\InputFilter;

/**
 * Description of LoginFilter
 *
 * @author marcio
 */
class LoginFilter extends InputFilter
{
    public function __construct($sm)
    {   
        $this->add(array(
            'name' => 'username', //usr_name
            'required' => true,
            'filters'=> array(
                array(
                    'name' => 'StripTags',
                ),
                array(
                    'name' => 'StringTrim',
                ),
            ),
            'validators' => array(
                array(
                    'name' => 'StringLength',
                    'options' => array(
                        'encoding' => 'UTF-8',
                        'min' => 1,
                        'max' => 100,
                    ),
                ),
//                array(
//                    'name' => 'DoctrineModule\Validator\ObjectExists',
//                    'options' => array(
//                        'object_repository' => 
//                            $sm->get('Doctrine\ORM\EntityManager')
//                                ->getRepository('Database\Entity\User'),
//                        'fields' => 'usrName',
//                    ),
//                ),
            ),
        ))
        ->add(array(
            'name'=> 'password', // usr_password
            'required' => true,
            'filters'  => array(
                array('name' => 'StripTags'),
                array('name' => 'StringTrim'),
            ),
            'validators' => array(
                array(
                    'name'    => 'StringLength',
                    'options' => array(
                        'encoding' => 'UTF-8',
                            'min'      => 3,
                            'max'      => 30,
                        ),
                    ),
                ),
            )
        );
    }
}
