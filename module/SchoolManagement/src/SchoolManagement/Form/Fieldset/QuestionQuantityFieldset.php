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

namespace SchoolManagement\Form\Fieldset;

use Zend\Form\Fieldset;
use Zend\InputFilter\InputFilterProviderInterface;

/**
 * Description of QuestionQuantityFieldset
 *
 * @author Gabriel Pereira <rickardch@gmail.com>
 */
class QuestionQuantityFieldset extends Fieldset implements InputFilterProviderInterface
{
    public function __construct()
    {
        parent::__construct('question-quantity-fieldset');

        $this->add(array(
            'name' => 'quantity',
            'type' => 'number',
            'attributes' => array(
                'class' => 'col-md-5 col-xs-12 pull-right amount-input',
                'data-old-value' => '',
                'min' => 0,
            ),
        ));
    }

    public function getInputFilterSpecification()
    {
        return array(
            'quantity' => array(
                'required' => true,
            ),
        );
    }
    
}
