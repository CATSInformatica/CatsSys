<?php

/*
 * Copyright (C) 2016 Márcio Dias <marciojr91@gmail.com>
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

namespace AdministrativeStructure\Form;

use AdministrativeStructure\Form\Fieldset\JobFieldset;
use Doctrine\Common\Persistence\ObjectManager;
use Zend\Form\Form;
use Zend\InputFilter\InputFilterProviderInterface;

/**
 * Description of JobForm
 *
 * @author Márcio Dias <marciojr91@gmail.com>
 */
class JobForm extends Form implements InputFilterProviderInterface
{

    public function __construct(ObjectManager $obj, $name = null, $options = array())
    {
        parent::__construct($name, $options);

        $jobFieldset = new JobFieldset($obj, 'job');
        $jobFieldset->setUseAsBaseFieldset(true);

        $this->add($jobFieldset);

        $this
            ->add(array(
                'name' => 'submit',
                'type' => 'submit',
                'attributes' => array(
                    'value' => 'Salvar',
                    'class' => 'btn-flat btn-primary btn-block',
                ),
            ))
            ->add([
                'name' => 'roles',
                'type' => 'DoctrineModule\Form\Element\ObjectMultiCheckbox',
                'options' => [
                    'label' => 'Funções',
                    'object_manager' => $obj,
                    'target_class' => 'Authorization\Entity\Role',
                    'property' => 'roleName',
                    'find_method' => [
                        'name' => 'findNonNumericalRoles',
                    ],
                ],
            ])
        ;
    }

    public function getInputFilterSpecification()
    {
        return [
            'roles' => [
                'required' => false,
            ]
        ];
    }

}
