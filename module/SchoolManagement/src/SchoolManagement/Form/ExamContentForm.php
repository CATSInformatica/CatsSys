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
use SchoolManagement\Form\Fieldset\QuestionQuantityFieldset;
use SchoolManagement\Form\Fieldset\ExamContentFieldset;
use Zend\Form\Form;

/**
 * Description of ExamContentForm
 *
 * @author Gabriel Pereira <rickardch@gmail.com>
 */
class ExamContentForm extends Form
{
    /**
     * 
     * @param ObjectManager $obj
     * @param array $numberOfQuantityFields - número de campos desejado
     */
    public function __construct(ObjectManager $obj, $numberOfQuantityFields)
    {
        parent::__construct('exam-content-form');
        $this->setHydrator(new DoctrineHydrator($obj));
        $examContentFieldset = new ExamContentFieldset($obj);
        $examContentFieldset->setUseAsBaseFieldset(true);
        $this->add($examContentFieldset);       
        
        $this  
            ->add(array(
                'name' => 'examQuestionQuantity',
                'type' => 'Zend\Form\Element\Collection',
                'options' => array(
                    'count' => $numberOfQuantityFields,
                    'should_create_template' => false,
                    'allow_add' => false,
                    'target_element' => new QuestionQuantityFieldset(),
                ),
            ))
            ->add(array(
                'name' => 'submit',
                'type' => 'submit',
                'attributes' => array(
                    'class' => 'btn btn-primary btn-block',
                    'value' => 'Criar Conteúdo',
                )
            ))  
        ;
    }
}
