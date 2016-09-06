<?php

/*
 * Copyright (C) 2016 Gabriel Pereira <rickardch@gmail.com>
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

namespace Documents\Form;

use Zend\InputFilter\InputFilterProviderInterface;

/**
 * Description of StudentIdCardForm
 *
 * @author Gabriel Pereira <rickardch@gmail.com>
 */
class StudentIdCardForm extends StudentsBoardForm implements InputFilterProviderInterface
{
        
    public function __construct($configIds = [], $classNames = [])
    {
        parent::__construct($configIds, $classNames);
        $nextyear=date("Y")+1;
        
        $this->add(array(
                'name' => 'expiry_date',
                'attributes' => array(
                    'type' => 'text',
                    'class' => 'datepicker text-center',
                    'value' => '01/03/'.$nextyear, 
                ),
                'options' => array(
                    'label' => 'Data de expiração',
                    'add-on-prepend' => '<i class="glyphicon glyphicon-calendar"></i>',
                ),
                    
        ));
    }  

    public function getInputFilterSpecification()
    {
        return array(
            'expiry_date' => array(
                'required' => true,
                'filters' => array(
                    array('name' => 'StripTags'),
                    array('name' => 'StringTrim'),
                    array(
                        'name' => 'Recruitment\Filter\DateToFormat',
                        'options' => array(
                            'inputFormat' => 'd/m/Y',
                            'outputFormat' => 'Y-m-d'
                        ),
                    ),
                ),
                'validators' => array(
                    array(
                        'name' => 'Date',
                        'options' => array(
                            'format' => 'Y-m-d',
                        ),
                    ),
                ),
            ),
        );
    }
    
}
