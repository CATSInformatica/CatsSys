<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Site\Form;

use Zend\Form\Form;
use Zend\InputFilter\InputFilterProviderInterface;
use Doctrine\Common\Persistence\ObjectManager;
use DoctrineModule\Stdlib\Hydrator\DoctrineObject as DoctrineHydrator;
use Recruitment\Model\CaptchaImage;
use Site\Form\Fieldset\ContactFieldset;

/**
 * Description of ContactForm
 *
 * @author Gabriel Pereira <rickardch@gmail.com>
 */
class ContactForm extends Form implements InputFilterProviderInterface
{

    public function __construct(ObjectManager $obj)
    {

        parent::__construct('formulario-contato');
        
        $this->setHydrator(new DoctrineHydrator($obj));
        
        $contactFieldset = new ContactFieldset($obj);
        $contactFieldset->setUseAsBaseFieldset(true);
        $this->add($contactFieldset);
        
        $this->add(array(
                    'name' => 'contactCaptcha',
                    'type' => 'Zend\Form\Element\Captcha',
                    'options' => array(
                        'label' => 'Insira o código da imagem',
                        'captcha' => new CaptchaImage(),
                    ),
                    'attributes' => array(
                        'id' => 'captcha-input',
                        'class' => 'text-center',
                    )
                ))
                ->add(array(
                    'name' => 'contactCsrf',
                    'type' => 'Zend\Form\Element\Csrf',
                ))
                ->add(array(
                    'name' => 'Submit',
                    'type' => 'submit',
                    'attributes' => array(
                        'class' => 'btn btn-primary btn-block',
                        'value' => 'Enviar',
                    ),
        ));
    }

    public function getInputFilterSpecification()
    {   
        return array(
            'contactCsrf' => array(
                'required' => true,
            ),
            'contactCaptcha' => array(
                'required' => true,
            ),
        );
    }
    
}
