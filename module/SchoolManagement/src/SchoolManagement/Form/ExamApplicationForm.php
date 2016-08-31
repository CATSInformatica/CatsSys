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
use SchoolManagement\Form\Fieldset\ExamApplicationFieldset;
use Zend\Form\Form;
use Zend\InputFilter\InputFilterProviderInterface;

/**
 * Description of ExamApplicationForm
 *
 * @author Gabriel Pereira <rickardch@gmail.com>
 */
class ExamApplicationForm extends Form implements InputFilterProviderInterface
{

    /**
     * 
     * @param ObjectManager $obj
     */
    public function __construct(ObjectManager $obj)
    {
        parent::__construct('exam-application-form');
        $this->setHydrator(new DoctrineHydrator($obj));

        $examApplicationFieldset = new ExamApplicationFieldset($obj);
        $examApplicationFieldset->setUseAsBaseFieldset(true);
        $this->add($examApplicationFieldset);

        $this
            ->add(array(
                'name' => 'appExams',
                'type' => 'Collection',
                'options' => array(
                    'count' => 0,
                    'should_create_template' => true,
                    'allow_add' => true,
                    'target_element' => new \Zend\Form\Element\Hidden(),
                ),
            ))
            ->add(array(
                'name' => 'submit',
                'type' => 'button',
                'attributes' => array(
                    'id' => 'submit-button',
                    'class' => 'btn btn-primary btn-block',
                    'value' => 'Criar Aplicação de Prova',
                )
            ))
        ;
    }

    public function getInputFilterSpecification()
    {
        return [
            'appExams' => [
                'required' => true,
            ]
        ];
    }
}
