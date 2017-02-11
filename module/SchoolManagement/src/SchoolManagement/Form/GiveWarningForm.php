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

namespace SchoolManagement\Form;

use Doctrine\Common\Persistence\ObjectManager;
use DoctrineModule\Stdlib\Hydrator\DoctrineObject as DoctrineHydrator;
use SchoolManagement\Form\Fieldset\GiveWarningFieldset;
use Zend\Form\Form;
use Zend\InputFilter\InputFilterProviderInterface;

/**
 * Description of GiveWarningForm
 *
 * @author Gabriel Pereira <rickardch@gmail.com>
 */
class GiveWarningForm extends Form implements InputFilterProviderInterface
{

    public function __construct(ObjectManager $obj, 
            $options = [
                'class_names' => [],
                'warning_type_names' => []
            ])
    {
        parent::__construct('give-warning-form');
        
        $this->setHydrator(new DoctrineHydrator($obj));
        
        $giveWarningFieldset = new GiveWarningFieldset($obj, $options['warning_type_names']);
        $giveWarningFieldset->setUseAsBaseFieldset(true);
        
        $this
            ->add(array(
                'name' => 'class_id',
                'type' => 'Zend\Form\Element\Select',
                'options' => array(
                    'label' => 'Turma',
                    'value_options' => $options['class_names'],
                ),
                'attributes' => array(
                    'type' => 'select',
                    'id' => 'class-input'
                )
            ))
            ->add($giveWarningFieldset)
            ->add(array(
                'name' => 'Submit',
                'attributes' => array(
                    'type' => 'submit',
                    'class' => 'btn btn-primary btn-block',
                    'value' => 'Salvar',
                )
        ));
    }

    public function getInputFilterSpecification()
    {
        return array(
            'class_id' => array(
                'required' => true
            )
        );
    }
    
}
