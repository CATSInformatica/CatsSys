<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Recruitment\Form;

use Recruitment\Entity\Person;
use Zend\Captcha\Image;
use Zend\Form\Form;

/**
 * Description of RegistrationForm
 *
 * @author marcio
 */
class RegistrationForm extends Form
{

    const FONT_DIR = './data/fonts/';
    const CAPTCHA_DIR = './data/captcha/';

    public function __construct($imgUrl, $name = null)
    {
        parent::__construct($name);

        $captchaImg = new Image(array(
            'font' => self::FONT_DIR . 'Arial.ttf',
            'width' => '250',
            'height' => '100',
            'dotNoiseLevel' => '40',
            'lineNoiseLevel' => 3,
            'expiration' => '360',
        ));

        $captchaImg->setImgDir(self::CAPTCHA_DIR);
        $captchaImg->setImgUrl($imgUrl);

        $this->add(array(
                    'name' => 'person_firstname',
                    'type' => 'text',
                    'attributes' => array(
                        'placeholder' => 'Primeiro Nome',
                        'class' => 'form-control',
                    ),
                ))
                ->add(array(
                    'name' => 'person_lastname',
                    'type' => 'text',
                    'attributes' => array(
                        'placeholder' => 'Sobrenome',
                        'class' => 'form-control',
                    ),
                ))
                ->add(array(
                    'name' => 'person_gender',
                    'type' => 'radio',
                    'options' => array(
                        'value_options' => array(
                            Person::GENDER_M => Person::GENDER_M,
                            Person::GENDER_F => Person::GENDER_F,
                        ),
                    ),
                ))
                ->add(array(
                    'name' => 'person_birthday',
                    'type' => 'text',
                    'attributes' => array(
                        'class' => 'form-control datepicker',
                    ),
                ))
                ->add(array(
                    'name' => 'person_cpf',
                    'type' => 'text',
                    'attributes' => array(
                        'placeholder' => 'XXX.XXX.XXX-XX',
                        'class' => 'form-control',
                    )
                ))
                ->add(array(
                    'name' => 'person_rg',
                    'type' => 'text',
                    'attributes' => array(
                        'placeholder' => 'Ex: MG-99.999.999',
                        'class' => 'form-control',
                    )
                ))
                ->add(array(
                    'name' => 'person_phone',
                    'type' => 'text',
                    'attributes' => array(
                        'placeholder' => 'Ex: (35)99999-9999',
                        'class' => 'form-control',
                    )
                ))
                ->add(array(
                    'name' => 'person_email',
                    'type' => 'email',
                    'attributes' => array(
                        'placeholder' => 'email@exemplo.com',
                        'class' => 'form-control',
                    ),
                ))
                ->add(array(
                    'name' => 'person_confirm_email',
                    'type' => 'email',
                    'attributes' => array(
                        'placeholder' => 'email@exemplo.com',
                        'class' => 'form-control',
                    )
                ))
                ->add(array(
                    'name' => 'registration_consent',
                    'type' => 'checkbox',
                    'options' => array(
                        'use_hidden_element' => false,
                        'checked_value' => true,
                        'unchecked_value' => false
                    )
                ))
                ->add(array(
                    'name' => 'registration_captcha',
                    'type' => 'Zend\Form\Element\Captcha',
                    'options' => array(
                        'captcha' => $captchaImg,
                    ),
                    'attributes' => array(
                        'id' => 'captcha_input',
                        'class' => 'form-control',
                    )
                ))
                ->add(array(
                    'name' => 'registation_confirm',
                    'type' => 'button',
                    'attributes' => array(
                        'class' => 'btn btn-success',
                        'value' => 'Concluir',
                    ),
        ));
    }

}
