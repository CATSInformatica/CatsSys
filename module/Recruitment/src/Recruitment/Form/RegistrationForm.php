<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Recruitment\Form;

use Recruitment\Form\Settings\PersonSettings;
use Recruitment\Model\CaptchaImage;
use Zend\Form\Form;

/**
 * Description of RegistrationForm
 *
 * @author marcio
 */
abstract class RegistrationForm extends Form
{

    public function __construct($name = null)
    {
        parent::__construct($name);

        $captchaImg = new CaptchaImage(array(
            'width' => '350',
            'height' => '100',
            'dotNoiseLevel' => '60',
            'lineNoiseLevel' => 3,
            'expiration' => '360',
        ));


        $personElements = PersonSettings::createPersonElements();

        $this
            ->add($personElements['person_firstname'])
            ->add($personElements['person_lastname'])
            ->add($personElements['person_gender'])
            ->add($personElements['person_birthday'])
            ->add($personElements['person_cpf'])
            ->add($personElements['person_rg'])
            ->add($personElements['person_phone'])
            ->add($personElements['person_email'])
            ->add($personElements['person_confirm_email']);


        $this->add(array(
                'name' => 'registration_consent',
                'type' => 'checkbox',
                'options' => array(
                    'label' => 'Declaro ter lido o edital do processo seletivo de alunos do CATS '
                    . 'e estar ciente de todas as etapas e documentos exigidos neste processo seletivo.*',
                    'checked_value' => true,
                    'unchecked_value' => false,
                ),
            ))
            ->add(array(
                'name' => 'registration_captcha',
                'type' => 'Zend\Form\Element\Captcha',
                'options' => array(
                    'label' => 'Insira o código da imagem*',
                    'captcha' => $captchaImg,
                ),
                'attributes' => array(
                    'id' => 'captcha_input',
                    'class' => 'text-center',
                )
            ))
            ->add(array(
                'name' => 'registation_confirm',
                'type' => 'submit',
                'attributes' => array(
                    'class' => 'btn btn-success btn-block',
                    'value' => 'Concluir',
                ),
        ));
    }

}
