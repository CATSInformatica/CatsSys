<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Recruitment\Form;

use Doctrine\Common\Persistence\ObjectManager;
use DoctrineModule\Stdlib\Hydrator\DoctrineObject as DoctrineHydrator;
use Recruitment\Model\CaptchaImage;
use Zend\Form\Form;
use Zend\InputFilter\InputFilterProviderInterface;

/**
 * Description of RegistrationForm
 *
 * @author Márcio Dias <marciojr91@gmail.com>
 */
class RegistrationForm extends Form implements InputFilterProviderInterface
{

    const TYPE_STUDENT = 0;
    const TYPE_VOLUNTEER = 1;

    public function __construct(ObjectManager $obj, $type = self::TYPE_STUDENT, $options = null)
    {
        parent::__construct('registration');
        $this->setHydrator(new DoctrineHydrator($obj));

        // Add the fieldset, and set it as the base fieldset

        switch ($type) {
            case self::TYPE_STUDENT:
                $registrationFieldset = new Fieldset\StudentRegistrationFieldset($obj, $options);
                break;
            case self::TYPE_VOLUNTEER:
                throw new \RuntimeException('type not implemented yet');
//                $registrationFieldset = new VolunteerRegistrationFieldset($obj, $options);
//                break;
            default:
                throw new \InvalidArgumentException('the type of registration form must be either `TYPE_STUDENT` or '
                . '`TYPE_VOLUNTEER`');
        }

        $registrationFieldset->setUseAsBaseFieldset(true);
        $this->add($registrationFieldset);
        $this->add(array(
                'name' => 'registrationConsent',
                'type' => 'checkbox',
                'options' => array(
                    'label' => 'Declaro ter lido o edital do processo seletivo e estar ciente de todas as etapas e'
                    . ' documentos exigidos neste processo seletivo.*',
                    'checked_value' => true,
                    'unchecked_value' => false,
                ),
            ))
            ->add(array(
                'name' => 'registrationCaptcha',
                'type' => 'Zend\Form\Element\Captcha',
                'options' => array(
                    'label' => 'Insira o código da imagem*',
                    'captcha' => new CaptchaImage(),
                ),
                'attributes' => array(
                    'id' => 'captcha_input',
                    'class' => 'text-center',
                )
            ))
            ->add(array(
                'name' => 'submit',
                'type' => 'submit',
                'attributes' => array(
                    'class' => 'btn btn-primary btn-block',
                    'value' => 'Criar',
                )
        ));
    }

    public function getInputFilterSpecification()
    {
        return array(
            'registrationConsent' => array(
                'required' => true,
            ),
            'registrationCaptcha' => array(
                'required' => true,
            ),
        );
    }

}
