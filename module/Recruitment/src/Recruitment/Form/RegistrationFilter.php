<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Recruitment\Form;

use Recruitment\Form\Settings\PersonSettings;
use Zend\InputFilter\InputFilter;

/**
 * Description of RegistrationFilter
 *
 * @author marcio
 */
abstract class RegistrationFilter extends InputFilter
{

    public function __construct()
    {
        
        $personFilters = PersonSettings::createPersonFilters();
        
        $this->add($personFilters['person_firstname']);
        $this->add($personFilters['person_lastname']);
        $this->add($personFilters['person_gender']);
        $this->add($personFilters['person_birthday']);
        $this->add($personFilters['person_cpf']);
        $this->add($personFilters['person_rg']);
        $this->add($personFilters['person_phone']);
        $this->add($personFilters['person_email']);
        $this->add($personFilters['person_confirm_email']);
        
        $this->add(array(
            'name' => 'registration_consent',
            'required' => true,
        ))->add(array(
            'name' => 'registration_captcha',
            'required' => true,
        ));
    }

}
