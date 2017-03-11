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

use Zend\Form\Form;
use Zend\InputFilter\InputFilterProviderInterface;

/**
 * Description of StudentBoardForm
 *
 * @author Gabriel Pereira <rickardch@gmail.com>
 */
class StudentsBoardForm extends Form implements InputFilterProviderInterface
{    
    
    public function __construct($name = 'student-board-form')
    {
        parent::__construct($name);
                
        $this
            ->add([
                'name' => 'studentIds',
            ])
            ->add([
                'name' => 'Submit',
                'attributes' => [
                    'type' => 'submit',
                    'class' => 'btn btn-primary btn-block',
                    'value' => 'Gerar'
                ]
        ]);
    }
    
    public function getInputFilterSpecification()
    {
        return [
            'studentIds' => [
                'required' => true,
                'validators' => [
                    [
                        'name' => 'Callback',
                        'options' => [
                            'callback' => function($studentIds) {
                                foreach ($studentIds as $studentId) {
                                    if (!is_numeric($studentId)) {
                                        return false;
                                    }
                                }
                                
                                return true;
                            },
                        ],
                    ],
                ],
            ]
        ];
    }
    
}
