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
use Recruitment\Form\Fieldset\RecruitmentFieldset;
use Zend\Form\Form;

/**
 * Modela os campos da entidade Recruitment\Entity\Recruitment
 *
 * @author Márcio Dias <marciojr91@gmail.com>
 */
class RecruitmentForm extends Form
{
    
    private $name = 'recruitment';
    
    //put your code here
    public function __construct(ObjectManager $obj)
    {
        parent::__construct($this->name);
        $this->setAttribute('method', 'post');
        $this->setAttribute('enctype', 'multipart/form-data');
        
        $this->setHydrator(new DoctrineHydrator($obj));

        $recruitmentFieldset = new RecruitmentFieldset($obj, $this->name);
        $recruitmentFieldset->setUseAsBaseFieldset(true);
        
        $this->add($recruitmentFieldset);

        $this->add([
            'name' => 'Submit',
            'type' => 'submit',
            'attributes' => [
                'class' => 'btn btn-primary btn-block',
                'value' => 'Salvar',
            ]
        ]);
    }
}
