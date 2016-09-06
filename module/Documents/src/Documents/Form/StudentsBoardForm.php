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
    
    public function __construct($configIds = [], $classNames = [])
    {
        parent::__construct('student_board_form');
                
        $this->add(array(
                    'name' => 'config_id',
                    'type' => 'select',
                    'options' => array(
                        'value_options' => $configIds,
                        'label' => 'Configuração de Fundo',
                    ),
                ))
                ->add(array(
                    'name' => 'class_id',
                    'type' => 'select',
                    'options' => array(
                        'value_options' => $classNames,
                        'label' => 'Turma',
                    ),
                ))
                ->add(array(
                'name' => 'Submit',
                'attributes' => array(
                    'type' => 'submit',
                    'class' => 'btn btn-primary btn-block',
                    'value' => 'Gerar',
                )
        ));
    }

    public function getInputFilterSpecification()
    {
        return array(
            'config_id' => array(
                'required' => true,
            ), 
            'class_id' => array(
                'required' => true,
            ),
        );
    }
    
}
