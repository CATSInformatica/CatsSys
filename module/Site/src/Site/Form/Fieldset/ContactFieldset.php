<?php

/*
 * Copyright (C) 2017 Gabriel Pereira <rickardch@gmail.com>
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

namespace Site\Form\Fieldset;

use Doctrine\Common\Persistence\ObjectManager;
use DoctrineModule\Stdlib\Hydrator\DoctrineObject as DoctrineHydrator;
use Zend\Form\Fieldset;
use Zend\InputFilter\InputFilterProviderInterface;
use Site\Entity\Contact;


/**
 * Description of ContactFieldset
 *
 * @author Gabriel Pereira <rickardch@gmail.com>
 */
class ContactFieldset extends Fieldset implements InputFilterProviderInterface
{

    public function __construct(ObjectManager $obj)
    {
        parent::__construct('contact-fieldset');

        $this->setHydrator(new DoctrineHydrator($obj))
            ->setObject(new Contact());

        $this->add(array(
                    'name' => 'name',
                    'type' => 'text',
                    'options' => array(
                        'label' => 'Nome (Opcional)',
                        'add-on-prepend' => '<i class="fa fa-user"></i>'
                    ),
                ))
                ->add(array(
                    'name' => 'email',
                    'type' => 'text',
                    'options' => array(
                        'label' => 'Email (Opcional)',
                        'add-on-prepend' => '<i class="fa fa-envelope-o"></i>'
                    ),
                ))
                ->add(array(
                    'name' => 'position',
                    'type' => 'Zend\Form\Element\Select',
                    'options' => array(
                        'label' => 'Qual a sua relação com o CATS?',
                        'empty_option' => 'Selecione sua relação',
                        'value_options' => Contact::POSITIONS_DESCRIPTION,
                        'add-on-prepend' => '<span class="glyphicon glyphicon-pawn"></span>'
                    ),
                ))
                ->add(array(
                    'name' => 'subject',
                    'type' => 'text',
                    'options' => array(
                        'label' => 'Assunto',
                        'add-on-prepend' => '<span class="glyphicon glyphicon-tag"></span>'
                    ),
                ))
                ->add(array(
                    'name' => 'message',
                    'type' => 'textarea',
                    'attributes' => array(
                        'rows' => 6,
                    ),
                    'options' => array(
                        'label' => 'Mensagem',
                        'add-on-prepend' => '<i class="fa fa-text-width"></i>',
                    ),
        ));
    }

    public function getInputFilterSpecification()
    {   
        return array(
            'name' => array(
                'required' => false,
                'validators' => array(
                    array(
                        'name' => 'Zend\Validator\StringLength',
                        'options' => array(
                            'min' => 3,
                            'max' => 100,
                        ),
                    ),
                ),
                'filters' => array(
                    array('name' => 'StringTrim'),
                    array('name' => 'StripTags'),
                    array(
                        'name' => 'StringToUpper',
                        'options' => array(
                            'encoding' => 'UTF-8',
                        ),
                    ),
                ),
            ),
            'email' => array(
                'required' => false,
                'validators' => array(
                    array(
                        'name' => 'Zend\Validator\StringLength',
                        'options' => array(
                            'min' => 9,
                            'max' => 50,
                        ),
                    ),
                    array(
                        'name' => 'Zend\Validator\EmailAddress',
                    ),
                )
            ),
            'position' => array(
                'required' => true,
            ),
            'subject' => array(
                'required' => true,
                'filters' => array(
                    array('name' => 'StringTrim'),
                    array('name' => 'StripTags'),
                    array(
                        'name' => 'StringToUpper',
                        'options' => [
                            'encoding' => 'UTF-8',
                        ],
                    ),
                ),
                'validators' => array(
                    array(
                        'name' => 'Zend\Validator\StringLength',
                        'options' => array(
                            'min' => 1,
                            'max' => 100,
                        )
                    )
                )
            ),
            'message' => array(
                'required' => true,
                'filters' => array(
                    array('name' => 'StringTrim'),
                    array('name' => 'StripTags'),
                ),
                'validators' => array(
                    array(
                        'name' => 'Zend\Validator\StringLength',
                        'options' => array(
                            'min' => 10,
                            'max' => 500,
                        )
                    )
                )
            ),
        );
    }
    
}
