<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Recruitment\Form;

use Recruitment\Entity\Person;
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

        $this->add(array(
                'name' => 'person_firstname',
                'type' => 'text',
                'attributes' => array(
                    'placeholder' => 'Primeiro Nome',
                ),
                'options' => array(
                    'label' => 'Nome*',
                ),
            ))
            ->add(array(
                'name' => 'person_lastname',
                'type' => 'text',
                'attributes' => array(
                    'placeholder' => 'Sobrenome',
                ),
                'options' => array(
                    'label' => 'Sobrenome*',
                ),
            ))
            ->add(array(
                'name' => 'person_gender',
                'type' => 'radio',
                'options' => array(
                    'label' => 'Sexo*',
                    'value_options' => array(
                        Person::GENDER_M => 'Masculino',
                        Person::GENDER_F => 'Feminino',
                    ),
                ),
            ))
            ->add(array(
                'name' => 'person_birthday',
                'type' => 'text',
                'attributes' => array(
                    'class' => 'datepicker',
                ),
                'options' => array(
                    'label' => 'Nascimento*',
                    'add-on-prepend' => '<i class="glyphicon glyphicon-calendar"></i>',
                ),
            ))
            ->add(array(
                'name' => 'person_cpf',
                'type' => 'text',
                'attributes' => array(
                    'placeholder' => 'XXX.XXX.XXX-XX',
                ),
                'options' => array(
                    'label' => 'CPF*',
                ),
            ))
            ->add(array(
                'name' => 'person_rg',
                'type' => 'text',
                'attributes' => array(
                    'placeholder' => 'Ex: MG-99.999.999',
                ),
                'options' => array(
                    'label' => 'RG*',
                ),
            ))
            ->add(array(
                'name' => 'person_phone',
                'type' => 'text',
                'attributes' => array(
                    'placeholder' => 'Ex: (35)99999-9999',
                ),
                'options' => array(
                    'label' => 'Telefone ou celular*',
                ),
            ))
            ->add(array(
                'name' => 'person_email',
                'type' => 'email',
                'attributes' => array(
                    'placeholder' => 'email@exemplo.com',
                ),
                'options' => array(
                    'label' => 'Endereço de Email*',
                )
            ))
            ->add(array(
                'name' => 'person_confirm_email',
                'type' => 'email',
                'attributes' => array(
                    'placeholder' => 'email@exemplo.com',
                ),
                'options' => array(
                    'label' => 'Reinsira o endereço de email*',
                )
            ))
            ->add(array(
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
