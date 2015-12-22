<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Recruitment\Form;

use Zend\Form\Form;
use Recruitment\Entity\Recruitment;

/**
 * Description of RecruitmentForm
 *
 * @author marcio
 */
class RecruitmentForm extends Form
{

    //put your code here
    public function __construct()
    {
        parent::__construct('Recruitment');

        $recruitmentYears = $this->generateRecruitmentYears();

        $date = new \DateTime('now');

        $this->add(array(
                    'name' => 'recruitment_begindate',
                    'attributes' => array(
                        'type' => 'text',
                        'class' => 'form-control datepicker text-center',
                        'value' => $date->format('d/m/Y'),
                    ),
                ))
                ->add(array(
                    'name' => 'recruitment_enddate',
                    'attributes' => array(
                        'type' => 'text',
                        'class' => 'form-control datepicker text-center',
                        'value' => $date->format('d/m/Y')
                    ),
                ))
                ->add(array(
                    'name' => 'recruitment_number',
                    'type' => 'Zend\Form\Element\Select',
                    'options' => array(
                        'empty_option' => 'Nº do processo seletivo',
                        'value_options' => array(
                            '1º' => 1,
                            '2º' => 2,
                            '3º' => 3,
                            '4º' => 4,
                            '5º' => 5,
                            '6º' => 6,
                            '7º' => 7,
                            '8º' => 8,
                            '9º' => 9,
                        ),
                    ),
                    'attributes' => array(
                        'class' => 'form-control',
                    ),
                ))
                ->add(array(
                    'name' => 'recruitment_year',
                    'type' => 'Zend\Form\Element\Select',
                    'options' => array(
                        'empty_option' => 'Ano do processo seletivo',
                        'value_options' => $recruitmentYears,
                    ),
                    'attributes' => array(
                        'class' => 'form-control text-center',
                    ),
                ))
                ->add(array(
                    'name' => 'recruitment_public_notice',
                    'attributes' => array(
                        'type' => 'text',
                        'class' => 'form-control',
                    )
                ))
                ->add(array(
                    'name' => 'recruitment_type',
                    'type' => 'Zend\Form\Element\Select',
                    'options' => array(
                        'empty_option' => 'Tipo de processo seletivo',
                        'value_options' => array(
                            Recruitment::STUDENT_RECRUITMENT_TYPE => 'Processo Seletivo de Alunos',
                            Recruitment::VOLUNTEER_RECRUITMENT_TYPE => 'Processo Seletivo de Voluntários',
                        ),
                    ),
                    'attributes' => array(
                        'class' => 'form-control text-center',
                    ),
                ))
                ->add(array(
                    'name' => 'Submit',
                    'attributes' => array(
                        'type' => 'button',
                        'class' => 'btn btn-primary',
                        'value' => 'Criar',
                    )
        ));
    }

    /**
     * Generate recruitment year interval [currentYear, currentYear + 1]
     * @return array
     */
    public function generateRecruitmentYears()
    {
        $year = (new \DateTime('now'))->format('Y');

        return array(
            $year => $year,
            ++$year => $year,
        );
    }

}
