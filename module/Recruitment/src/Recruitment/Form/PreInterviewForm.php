<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Recruitment\Form;

use Doctrine\Common\Persistence\ObjectManager;
use DoctrineModule\Stdlib\Hydrator\DoctrineObject as DoctrineHydrator;
use Recruitment\Form\Fieldset\StudentRegistrationFieldset;
use Zend\Form\Form;
use Zend\InputFilter\InputFilterProviderInterface;

/**
 * Formulário de pré-entrevista.
 *
 * @author Márcio Dias <marciojr91@gmail.com>
 */
class PreInterviewForm extends Form implements InputFilterProviderInterface
{

    public function __construct(ObjectManager $obj, $options = null)
    {
        parent::__construct('pre-interview');
        $this->setHydrator(new DoctrineHydrator($obj));

        // define o fieldset base
        $registrationFieldset = new StudentRegistrationFieldset($obj, $options);
        $registrationFieldset->setUseAsBaseFieldset(true);
        $this->add($registrationFieldset);

        $this->add([
            'name' => 'preInterviewConsent',
            'type' => 'checkbox',
            'options' => [
                'label' => 'Declaro, para fins de direito, sob as penas da lei, e em atendimento ao edital, que
                    as informações constantes dos documentos que apresento para a entrevista no processo seletivo de 
                    alunos são verdadeiras, (ou são fieis à verdade e condizentes com a realidade dos fatos à época).
                    Fico ciente através desse documento que a falsidade dessa declaração configura crime previsto no
                    Código Penal Brasileiro, Lei 2848/40. Art. 299. Nada mais a declarar, e ciente das responsabilidades
                    pelas declarações prestadas, confirmo as informações.',
                'checked_value' => true,
                'unchecked_value' => false,
            ]
        ]);

        $this->add(array(
            'name' => 'submit',
            'type' => 'submit',
            'attributes' => array(
                'class' => 'btn btn-primary btn-block',
                'value' => 'Concluir',
            )
        ));
    }

    public function getInputFilterSpecification()
    {
        return [
            'preInterviewConsent' => [
                'required' => true,
            ]
        ];
    }

}
