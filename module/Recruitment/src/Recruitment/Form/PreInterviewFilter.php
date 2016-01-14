<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Recruitment\Form;

use Recruitment\Form\Settings\AddressSettings;
use Recruitment\Form\Settings\PersonSettings;
use Recruitment\Form\Settings\RelativeSettings;
use Zend\InputFilter\InputFilter;

/**
 * Description of PreInterviewFilter
 *
 * @author Márcio Dias <marciojr91@gmail.com>
 */
class PreInterviewFilter extends InputFilter
{

    public function __construct($isUnderage = false)
    {

        $addressFilters = AddressSettings::createAddressFilters();
        $this->add($addressFilters['postal_code']);
        $this->add($addressFilters['state']);
        $this->add($addressFilters['city']);
        $this->add($addressFilters['neighborhood']);
        $this->add($addressFilters['street']);
        $this->add($addressFilters['number']);
        $this->add($addressFilters['complement']);

        if ($isUnderage) {
            $relativeSuffix = '_relative';
            $personFilters = PersonSettings::createPersonFilters($relativeSuffix);
            $this->add($personFilters['person_firstname']);
            $this->add($personFilters['person_lastname']);
            $this->add($personFilters['person_gender']);
            $this->add($personFilters['person_birthday']);
            $this->add($personFilters['person_cpf']);
            $this->add($personFilters['person_rg']);
            $this->add($personFilters['person_phone']);
            $this->add($personFilters['person_email']);
            $this->add($personFilters['person_confirm_email']);

            $relativeFilters = RelativeSettings::createRelativeFilters();
            $this->add($relativeFilters['relative_relationship']);

            $addressFilters = AddressSettings::createAddressFilters($relativeSuffix);
            $this->add($addressFilters['postal_code']);
            $this->add($addressFilters['state']);
            $this->add($addressFilters['city']);
            $this->add($addressFilters['neighborhood']);
            $this->add($addressFilters['street']);
            $this->add($addressFilters['number']);
            $this->add($addressFilters['complement']);
        }

        $this
            ->add(array(
                'name' => 'elementary_school_type',
                'required' => true,
            ))
            ->add(array(
                'name' => 'high_school_type',
                'required' => true,
            ))
            ->add(array(
                'name' => 'high_school',
                'required' => true,
                'filters' => array(
                    array('name' => 'StringToUpper'),
                ),
                'validators' => array(
                    array(
                        'name' => 'Zend\Validator\StringLength',
                        'options' => array(
                            'min' => 5,
                            'max' => 150,
                        ),
                    ),
                ),
            ))
            ->add(array(
                'name' => 'hs_conclusion_year',
                'required' => true,
            ))
            ->add(array(
                'name' => 'preparation_school',
                'required' => true,
            ))
            ->add(array(
                'name' => 'language_course',
                'required' => true,
            ))
            ->add(array(
                'name' => 'current_study',
                'required' => true,
            ))
            ->add(array(
                'name' => 'live_with_number',
                'required' => true,
            ))
            ->add(array(
                'name' => 'live_with_you',
                'required' => true,
            ))
            ->add(array(
                'name' => 'number_of_rooms',
                'required' => true,
            ))
            ->add(array(
                'name' => 'means_of_transport',
                'required' => true,
            ))
            ->add(array(
                'name' => 'monthly_income',
                'required' => true,
            ))
            ->add(array(
                'name' => 'father_education_grade',
                'required' => true,
            ))
            ->add(array(
                'name' => 'mother_education_grade',
                'required' => true,
            ))
            ->add(array(
                'name' => 'expect_from_us',
                'required' => true,
                'filters' => array(
                    array('name' => 'StringTrim'),
                    array('name' => 'StripTags'),
                ),
                'validators' => array(
                    array(
                        'name' => 'Zend\Validator\StringLength',
                        'options' => array(
                            'min' => 20,
                            'max' => 200,
                        )
                    )
                )
        ));
    }

}
