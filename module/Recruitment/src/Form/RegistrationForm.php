<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Recruitment\Form;

use Doctrine\Common\Persistence\ObjectManager;
use DoctrineModule\Stdlib\Hydrator\DoctrineObject as DoctrineHydrator;
use InvalidArgumentException;
use Recruitment\Entity\Recruitment;
use Recruitment\Form\Fieldset\StudentRegistrationFieldset;
use Recruitment\Form\Fieldset\VolunteerRegistrationFieldset;
//use Recruitment\Model\CaptchaImage;
use Zend\Form\Form;
use Zend\InputFilter\InputFilterProviderInterface;

/**
 * Description of RegistrationForm
 *
 * @author Márcio Dias <marciojr91@gmail.com>
 */
class RegistrationForm extends Form implements InputFilterProviderInterface
{

    public function __construct(ObjectManager $obj, $type = Recruitment::STUDENT_RECRUITMENT_TYPE, $options = null)
    {
        parent::__construct('registration');
        $this->setHydrator(new DoctrineHydrator($obj));

        // Add the fieldset, and set it as the base fieldset

        switch ($type) {
            case Recruitment::STUDENT_RECRUITMENT_TYPE:
                $registrationFieldset = new StudentRegistrationFieldset($obj, $options);
                break;
            case Recruitment::VOLUNTEER_RECRUITMENT_TYPE:
                $registrationFieldset = new VolunteerRegistrationFieldset($obj, $options);
                break;
            default:
                throw new InvalidArgumentException('the type of registration form must be either'
                . ' `STUDENT_RECRUITMENT_TYPE` or `VOLUNTEER_RECRUITMENT_TYPE`');
        }

        $registrationFieldset->setUseAsBaseFieldset(true);
        $this->add($registrationFieldset);
        $this->add(array(
                'name' => 'registrationConsent',
                'type' => 'checkbox',
                'options' => array(
                    'label' => 'Declaro ter lido o edital do processo seletivo e estar ciente de todas as etapas e'
                    . ' documentos exigidos*',
                    'checked_value' => true,
                    'unchecked_value' => false,
                ),
            ))
            ->add(array(
                'type' => 'Zend\Form\Element\Csrf',
                'name' => 'registrationCsrf',
                'csrf_options' => array(
                    'timeout' => 1200,
                ),
            ))
            ->add(array(
                'name' => 'submit',
                'type' => 'submit',
                'attributes' => array(
                    'class' => 'btn btn-primary btn-block',
                    'value' => 'Inscrever',
                )
        ));
    }

    public function getInputFilterSpecification()
    {
        return array(
            'registrationConsent' => array(
                'required' => true,
            ),
            'registrationCsrf' => array(
                'required' => true,
            ),
        );
    }

}
