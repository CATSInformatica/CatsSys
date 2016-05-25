<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Recruitment\Form\Fieldset;

use Doctrine\Common\Persistence\ObjectManager;
use Zend\Form\Fieldset;

/**
 * Description of RegistrationFieldset
 *
 * @author Márcio Dias <marciojr91@gmail.com>
 */
abstract class RegistrationFieldset extends Fieldset
{

    public function __construct(ObjectManager $obj, $options = null)
    {
        if (is_array($options) && !array_key_exists('person', $options)) {
            throw new \InvalidArgumentException('`options` array must contain the key `person`');
        }

        parent::__construct('registration');

        if ($options === null) {
            $person = new PersonFieldset($obj);
        } else {
            $person = new PersonFieldset($obj, $options['person']);
        }

        $this->add($person);
    }

}
