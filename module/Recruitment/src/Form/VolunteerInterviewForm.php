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

use Zend\Form\Form;
use Doctrine\Common\Persistence\ObjectManager;
use DoctrineModule\Stdlib\Hydrator\DoctrineObject as DoctrineHydrator;
use Recruitment\Form\Fieldset\VolunteerInterviewFieldset;

/**
 * Description of VolunteerInterviewForm
 *
 * @author Márcio Dias <marciojr91@gmail.com>
 */
class VolunteerInterviewForm extends Form
{

    public function __construct(ObjectManager $obj, $options = null)
    {
        parent::__construct('volunteerInterviewForm');
        $this->setHydrator(new DoctrineHydrator($obj));

        $volunteerInterviewFieldset = new VolunteerInterviewFieldset($obj, $options);
        $volunteerInterviewFieldset->setUseAsBaseFieldset(true);
        $this->add($volunteerInterviewFieldset);

        $this->add(array(
            'name' => 'submit',
            'type' => 'submit',
            'attributes' => array(
                'class' => 'btn btn-primary btn-block',
                'value' => 'Salvar',
            )
        ));
    }

}
