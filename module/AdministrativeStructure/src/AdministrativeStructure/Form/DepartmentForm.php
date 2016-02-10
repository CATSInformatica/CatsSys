<?php

namespace AdministrativeStructure\Form;

use AdministrativeStructure\Form\Fieldset\DepartmentFieldset;
use Doctrine\Common\Persistence\ObjectManager;
use Zend\Form\Form;

/**
 * Description of DepartmentForm
 *
 * @author Márcio Dias <marciojr91@gmail.com>
 */
class DepartmentForm extends Form
{

    public function __construct(ObjectManager $obj, $name = null, $options = array())
    {
        parent::__construct($name, $options);

        $departmentFieldset = new DepartmentFieldset($obj, 'department');
        $departmentFieldset->setUseAsBaseFieldset(true);
        $this
            ->add($departmentFieldset)
            ->add(array(
                'name' => 'submit',
                'type' => 'submit',
                'attributes' => array(
                    'value' => 'Criar',
                    'class' => 'btn-primary btn-block',
                ),
            ))
        ;
    }

}
