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

namespace Recruitment\Form;

use Doctrine\Common\Persistence\ObjectManager;
use DoctrineModule\Stdlib\Hydrator\DoctrineObject as DoctrineHydrator;
use Recruitment\Form\Fieldset\StudentInterviewFieldset;
use Zend\Form\Form;

/**
 * Formulário de entrevista para candidatos de processos seletivos de alunos.
 *
 * @author Márcio Dias <marciojr91@gmail.com>
 */
class StudentInterviewForm extends Form
{

    public function __construct(ObjectManager $obj)
    {
        parent::__construct('student-interview');

        $this->setHydrator(new DoctrineHydrator($obj));

        $interviewFieldset = new StudentInterviewFieldset($obj);
        $interviewFieldset->setUseAsBaseFieldset(true);
        $this->add($interviewFieldset);

        $this->add([
            'name' => 'interviewSubmit',
            'type' => 'submit',
            'attributes' => [
                'class' => 'btn-flat btn-primary btn-block',
                'value' => 'Concluir',
            ]
        ]);
    }
}
