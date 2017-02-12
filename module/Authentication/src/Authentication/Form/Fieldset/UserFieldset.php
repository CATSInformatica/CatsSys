<?php
/*
 * Copyright (C) 2017 Márcio Dias <marciojr91@gmail.com>
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

namespace Authentication\Form\Fieldset;

use Authentication\Entity\User;
use Doctrine\Common\Persistence\ObjectManager;
use DoctrineModule\Stdlib\Hydrator\DoctrineObject as DoctrineHydrator;
use Zend\Form\Fieldset;
use Zend\InputFilter\InputFilterProviderInterface;
use Zend\Validator\Identical;

/**
 * Description of UserFieldset
 *
 * @author Márcio Dias <marciojr91@gmail.com>
 */
class UserFieldset extends Fieldset implements InputFilterProviderInterface
{

    public function __construct(ObjectManager $obj)
    {
        parent::__construct('user');
        
        $this->setHydrator(new DoctrineHydrator($obj))
            ->setObject(new User());

        $this
            ->add([
                'name' => 'userName',
                'type' => 'text',
                'options' => [
                    'label' => 'Nome de usuário',
                ],
            ])
            ->add([
                'name' => 'userPassword',
                'type' => 'password',
                'options' => [
                    'label' => 'Senha',
                ],
            ])
            ->add([
                'name' => 'userPasswordConfirm',
                'type' => 'password',
                'options' => [
                    'label' => 'Confirmação de senha',
                ],
            ])
            ->add([
                'name' => 'userActive',
                'type' => 'checkbox',
                'options' => [
                    'label' => 'Ativo',
                ]
            ])
        ;
    }

    public function getInputFilterSpecification()
    {
        return [
            'userName' => [
                'required' => true,
                'filters' => [
                    ['name' => 'StripTags'],
                    ['name' => 'StringTrim']
                ],
                'validators' => [
                    [
                        'name' => 'StringLength',
                        'options' => [
                            'encoding' => 'UTF-8',
                            'min' => 5,
                            'max' => 100,
                        ],
                    ],
                ],
            ],
            'userPassword' => [
                'required' => true,
                'filters' => [
                    ['name' => 'StripTags'],
                    ['name' => 'StringTrim'],
                ],
                'validators' => [
                    [
                        'name' => 'StringLength',
                        'options' => [
                            'encoding' => 'UTF-8',
                            'min' => 6,
                            'max' => 20,
                        ],
                    ],
                ],
            ],
            'userPasswordConfirm' => [
                'required' => true,
                'validators' => [
                    [
                        'name' => 'Identical',
                        'options' => [
                            'token' => 'userPassword',
                            'messages' => [
                                Identical::NOT_SAME => 'as senhas não são iguais'
                            ]
                        ]
                    ]
                ],
            ],
            'userActive' => [
                'required' => false,
            ]
        ];
    }
}
