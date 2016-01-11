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
        $this->setAttribute('method', 'post');
        $this->setAttribute('enctype', 'multipart/form-data');

        $date = new \DateTime('now');

        $this->add(array(
                'name' => 'recruitment_number',
                'type' => 'Zend\Form\Element\Select',
                'options' => array(
                    'label' => 'Número do processo seletivo',
                    'empty_option' => '',
                    'value_options' => array(
                        1 => '1º',
                        2 => '2º',
                        3 => '3º',
                        4 => '4º',
                        5 => '5º',
                        6 => '6º',
                        7 => '7º',
                        8 => '8º',
                        9 => '9º',
                    ),
                ),
            ))
            ->add(array(
                'name' => 'recruitment_year',
                'type' => 'Zend\Form\Element\Select',
                'options' => array(
                    'label' => 'Ano do processo seletivo',
                    'empty_option' => 'Ano do processo seletivo',
                    'value_options' => $this->getYears(),
                ),
                'attributes' => array(
                    'class' => 'text-center',
                ),
            ))
            ->add(array(
                'name' => 'recruitment_begindate',
                'attributes' => array(
                    'type' => 'text',
                    'class' => 'form-control datepicker text-center',
                    'value' => $date->format('d/m/Y'),
                ),
                'options' => array(
                    'label' => 'Data de início',
                    'add-on-prepend' => '<i class="glyphicon glyphicon-calendar"></i>',
                ),
            ))
            ->add(array(
                'name' => 'recruitment_enddate',
                'attributes' => array(
                    'type' => 'text',
                    'class' => 'form-control datepicker text-center',
                    'value' => $date->format('d/m/Y')
                ),
                'options' => array(
                    'label' => 'Data de término',
                    'add-on-prepend' => '<i class="glyphicon glyphicon-calendar"></i>',
                ),
            ))
            ->add(array(
                'name' => 'recruitment_public_notice',
                'attributes' => array(
                    'type' => 'file',
                ),
                'options' => array(
                    'label' => 'Arquivo do edital',
                ),
            ))
            ->add(array(
                'name' => 'recruitment_type',
                'type' => 'Zend\Form\Element\Select',
                'options' => array(
                    'label' => 'Tipo de processo seletivo',
                    'empty_option' => '',
                    'value_options' => array(
                        Recruitment::STUDENT_RECRUITMENT_TYPE => 'Processo Seletivo de Alunos',
                        Recruitment::VOLUNTEER_RECRUITMENT_TYPE => 'Processo Seletivo de Voluntários',
                    ),
                ),
                'attributes' => array(
                    'class' => 'text-center',
                ),
            ))
            ->add(array(
                'name' => 'Submit',
                'type' => 'submit',
                'attributes' => array(
                    'class' => 'btn btn-primary btn-block',
                    'value' => 'Criar',
                )
        ));
    }

    protected function getYears()
    {
        $year = (new \DateTime('now'))->format('Y');
        return array(
            $year => $year,
            ++$year => $year,
        );
    }

}
