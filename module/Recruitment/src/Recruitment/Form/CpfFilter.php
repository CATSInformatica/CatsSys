<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Recruitment\Form;

use Recruitment\Form\Settings\CpfSettings;
use Zend\InputFilter\InputFilter;

/**
 * Description of CpfFormFilter
 *
 * @author Márcio Dias <marciojr91@gmail.com>
 */
class CpfFilter extends InputFilter
{

    public function __construct()
    {
        $this->add(CpfSettings::createCpfFilter());
    }

}
