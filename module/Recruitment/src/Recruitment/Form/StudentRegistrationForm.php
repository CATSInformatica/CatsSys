<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Recruitment\Form;

use Recruitment\Entity\Registration;

/**
 * Description of RegistrationForm
 *
 * @author marcio
 */
class StudentRegistrationForm extends RegistrationForm
{

    public function __construct($imgUrl, $name = null)
    {
        parent::__construct($imgUrl, $name);

        $this->add(array(
            'name' => 'registration_know_about',
            'type' => 'Zend\Form\Element\MultiCheckbox',
            'options' => array(
                'use_hidden_element' => false,
                'value_options' => array(
                    Registration::FAMILY => array(
                        'label' => Registration::FAMILY,
                        'value' => Registration::FAMILY,
                        'attributes' => array(
                            'class' => 'checkbox',
                        ),
                    ),
                    Registration::UNIVERSIRTY_STUDENTS => array(
                        'label' => Registration::UNIVERSIRTY_STUDENTS,
                        'value' => Registration::UNIVERSIRTY_STUDENTS,
                        'attributes' => array(
                            'class' => 'checkbox',
                        ),
                    ),
                    Registration::STUDENTS => array(
                        'label' => Registration::STUDENTS,
                        'value' => Registration::STUDENTS,
                        'attributes' => array(
                            'class' => 'checkbox',
                        ),
                    ),
                    Registration::FRIENDS => array(
                        'label' => Registration::FRIENDS,
                        'value' => Registration::FRIENDS,
                        'attributes' => array(
                            'class' => 'checkbox',
                        ),
                    ),
                    Registration::INTERNET => array(
                        'label' => Registration::INTERNET,
                        'value' => Registration::INTERNET,
                        'attributes' => array(
                            'class' => 'checkbox',
                        ),
                    ),
                    Registration::COMMON_COMMUNICATION_CHANNELS => array(
                        'label' => Registration::COMMON_COMMUNICATION_CHANNELS,
                        'value' => Registration::COMMON_COMMUNICATION_CHANNELS,
                        'attributes' => array(
                            'class' => 'checkbox',
                        ),
                    ),
                    Registration::SCHOOL => array(
                        'label' => Registration::SCHOOL,
                        'value' => Registration::SCHOOL,
                        'attributes' => array(
                        ),
                    ),
                    Registration::VOLUNTEERS => array(
                        'label' => Registration::VOLUNTEERS,
                        'value' => Registration::VOLUNTEERS,
                        'attributes' => array(
                        ),
                    ),
                ),
            ),
        ));
    }

}
