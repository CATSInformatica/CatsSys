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
use Doctrine\Common\Persistence\ObjectManager;
use DoctrineModule\Stdlib\Hydrator\DoctrineObject as DoctrineHydrator;
use Documents\Form\Fieldset\StudentBgConfigFieldset;

/**
 * Description of StudentBgConfigForm
 *
 * @author Gabriel Pereira <rickardch@gmail.com>
 */
class StudentBgConfigForm extends Form implements InputFilterProviderInterface 
{
    
    private $imgRequired;
    
    /**
     * 
     * @param ObjectManager $obj - entity manager
     * @param bool $imgRequired - define se o upload de uma imagem é obrigatório
     */
    public function __construct(ObjectManager $obj, $imgRequired = true) 
    {
        parent::__construct('student_bg_config_form');
        $this->imgRequired = $imgRequired;
        
        $this->setAttribute('method', 'post');
        
        $this->setHydrator(new DoctrineHydrator($obj));
        $studentBgConfigFieldset = new StudentBgConfigFieldset($obj);
        $studentBgConfigFieldset->setUseAsBaseFieldset(true);
        $this->add($studentBgConfigFieldset);
        
        $this
                ->add(array(
                    'name' => 'bg_img',
                    'type' => 'file',
                    'attributes' => array(
                        'id' => 'bg_img',
                    ),
                    'options' => array(
                        'label' => 'Imagem de fundo (.png)',
                    ),
                ))
                ->add(array(
                    'name' => 'submit',
                    'type' => 'submit',
                    'attributes' => array(
                        'class' => 'btn btn-primary btn-block',
                        'value' => 'Criar configuração de fundo',
                    ),
        ));
    }    

    public function getInputFilterSpecification()
    {
        return array(
            'bg_img' => array(
                'required' => $this->imgRequired,
                'validators' => array(
                    array(
                        'name' => 'Zend\Validator\File\Extension',
                        'options' => array(
                            'extension' => array(
                                'png',
                            ),
                        ),
                    ),
                    array(
                        'name' => 'Zend\Validator\File\Size',
                        'options' => array(
                            'min' => '1000',
                            'max' => '5000000',
                        )
                    )
                ),
            )
        );
    }

}
