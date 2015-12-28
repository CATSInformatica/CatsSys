<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Recruitment\Form;

use Zend\InputFilter\InputFilter;

/**
 * Description of RecruitmentFilter
 *
 * @author marcio
 */
class RecruitmentFilter extends InputFilter
{

    public function __construct()
    {

        $this->add(array(
            'name' => 'recruitment_number',
            'required' => true,
        ));

        $this->add(array(
            'name' => 'recruitment_year',
            'required' => true,
        ));

        $this->add(array(
            'name' => 'recruitment_begindate',
            'required' => true,
            'filters' => array(
                array('name' => 'StripTags'),
                array('name' => 'StringTrim'),
                array(
                    'name' => 'Recruitment\Filter\DateToFormat',
                    'options' => array(
                        'inputFormat' => 'd/m/Y',
                        'outputFormat' => 'Y-m-d'
                    ),
                ),
            ),
            'validators' => array(
                array(
                    'name' => 'Date',
                    'options' => array(
                        'format' => 'Y-m-d',
                    )
                ),
                array(
                    'name' => 'Recruitment\Validator\DateGratherThan',
                    'options' => array(
                        'format' => 'Y-m-d',
                        'inclusive' => true,
                    ),
                ),
            ),
        ));
        $this->add(array(
            'name' => 'recruitment_enddate',
            'require' => true,
            'filters' => array(
                array('name' => 'StripTags'),
                array('name' => 'StringTrim'),
                array(
                    'name' => 'Recruitment\Filter\DateToFormat',
                    'options' => array(
                        'inputFormat' => 'd/m/Y',
                        'outputFormat' => 'Y-m-d'
                    ),
                ),
            ),
            'validators' => array(
                array(
                    'name' => 'Date',
                    'options' => array(
                        'format' => 'Y-m-d',
                    )
                ),
                array(
                    'name' => 'Recruitment\Validator\DateGratherThan',
                    'options' => array(
                        'format' => 'Y-m-d',
                        'compareWith' => array(
                            'name' => 'recruitment_begindate',
                            'format' => 'd/m/Y',
                        ),
                    ),
                ),
            ),
        ));
        $this->add(array(
            'name' => 'recruitment_public_notice',
            'required' => true,
            'validators' => array(
                array(
                    'name' => 'Zend\Validator\File\Extension',
                    'options' => array(
                        'extension' => array(
                            'pdf',
                        ),
                    ),
                ),
                array(
                    'name' => 'Zend\Validator\File\Size',
                    'options' => array(
                        'min' => '1000',
                        'max' => '5000000',
                    )
                )
            )
        ));

        $this->add(array(
            'name' => 'recruitment_type',
            'required' => true,
        ));
    }

}
