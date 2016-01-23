<?php

namespace Recruitment\Form;

use Doctrine\Common\Persistence\ObjectManager;
use DoctrineModule\Stdlib\Hydrator\DoctrineObject as DoctrineHydrator;
use Recruitment\Form\Fieldset\VolunteerRegistrationFieldset;
use Zend\Form\Form;

/**
 * Description of VolunteerInterviewForm
 *
 * @author Márcio Dias <marciojr91@gmail.com>
 */
class VolunteerInterviewForm extends Form
{

    public function __construct(ObjectManager $obj, $options = null)
    {
        parent::__construct('volunteer_interview');
        $this->setHydrator(new DoctrineHydrator($obj));

        // Add the user fieldset, and set it as the base fieldset
        $registrationFieldset = new VolunteerRegistrationFieldset($obj, $options);
        $registrationFieldset->setUseAsBaseFieldset(true);
        $this->add($registrationFieldset);

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
