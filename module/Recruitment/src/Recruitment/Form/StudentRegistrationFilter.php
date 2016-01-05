<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Recruitment\Form;

/**
 * Description of StudentRegistrationFilter
 *
 * @author marcio
 */
class StudentRegistrationFilter extends RegistrationFilter
{

    public function __construct()
    {
        parent::__construct();

        $this->add(array(
            'name' => 'registration_know_about',
            'required' => true,
        ));
    }

}
