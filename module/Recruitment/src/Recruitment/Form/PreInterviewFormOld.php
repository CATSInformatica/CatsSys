<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Recruitment\Form;

use Recruitment\Entity\PreInterview;
use Recruitment\Form\Settings\AddressSettings;
use Recruitment\Form\Settings\PersonSettings;
use Recruitment\Form\Settings\RelativeSettings;
use Zend\Form\Form;

/**
 * Description of PreInterviewForm
 *
 * @author Márcio Dias <marciojr91@gmail.com>
 */
class PreInterviewFormOld extends Form
{

    public function __construct($name = null, $options = array(), $isUnderage = false)
    {
        parent::__construct($name, $options);

        $addressElements = AddressSettings::createAddressElements();
        $this->add($addressElements['postal_code']);
        $this->add($addressElements['state']);
        $this->add($addressElements['city']);
        $this->add($addressElements['neighborhood']);
        $this->add($addressElements['street']);
        $this->add($addressElements['number']);
        $this->add($addressElements['complement']);

        if ($isUnderage) {
            $relativeSuffix = '_relative';
            $personElements = PersonSettings::createPersonElements($relativeSuffix);
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

            $relativeElements = RelativeSettings::createRelativeElements();
            $this->add($relativeElements['relative_relationship']);

            $addressElements = AddressSettings::createAddressElements($relativeSuffix);
            $this->add($addressElements['postal_code']);
            $this->add($addressElements['state']);
            $this->add($addressElements['city']);
            $this->add($addressElements['neighborhood']);
            $this->add($addressElements['street']);
            $this->add($addressElements['number']);
            $this->add($addressElements['complement']);
        }

        $this->add(array(
                'name' => 'elementary_school_type',
                'type' => 'radio',
                'options' => array(
                    'label' => 'Tipo de escola onde cursou ensino fundamental (4° a 8ª série)',
                    'value_options' => array(
                        PreInterview::SCHOOL_TYPE_ONLY_PUBLIC => 'Somente em escola pública.',
                        PreInterview::SCHOOL_TYPE_MOST_PUBLIC => 'Maior parte em escola pública.',
                        PreInterview::SCHOOL_TYPE_ONLY_PRIVATE => 'Somente em escola particular.',
                        PreInterview::SCHOOL_TYPE_MOST_PRIVATE => 'Maior parte em escola particular.',
                        PreInterview::SCHOOL_TYPE_NOT_ATTENDED_REGULAR_SCHOOL => 'Não frequentei escola regular.',
                    )
                )
            ))
            ->add(array(
                'name' => 'high_school_type',
                'type' => 'radio',
                'options' => array(
                    'label' => 'Em que tipo de escola você cursou ou está cursando o ensino médio (1° ao 3° ano)?',
                    'value_options' => array(
                        PreInterview::SCHOOL_TYPE_ONLY_PUBLIC => 'Somente em escola pública.',
                        PreInterview::SCHOOL_TYPE_MOST_PUBLIC => 'Maior parte em escola pública.',
                        PreInterview::SCHOOL_TYPE_ONLY_PRIVATE => 'Somente em escola particular.',
                        PreInterview::SCHOOL_TYPE_PRIVATE_SCHOLARSHIP => 'Escola particular com bolsa.',
                        PreInterview::SCHOOL_TYPE_MOST_PRIVATE => 'Maior parte em escola particular.',
                        PreInterview::SCHOOL_TYPE_NOT_ATTENDED_REGULAR_SCHOOL => 'Não frequentei escola regular.',
                    )
                )
            ))
            ->add(array(
                'name' => 'high_school',
                'type' => 'text',
                'options' => array(
                    'label' => 'Escola onde cursou ou está cursando o ensino médio',
                ),
                'attributes' => array(
                    'placeholder' => 'Ex: Escola Estadual Major João Pereira',
                ),
            ))
            ->add(array(
                'name' => 'hs_conclusion_year',
                'type' => 'select',
                'options' => array(
                    'label' => 'Conclusão',
                    'empty_option' => 'Ano',
                    'value_options' => $this->getYears(),
                ),
            ))
            ->add(array(
                'name' => 'preparation_school',
                'type' => 'radio',
                'options' => array(
                    'label' => 'Frequentou curso pré-vestibular?',
                    'value_options' => array(
                        PreInterview::PREP_SCHOOL_ASSISTANCE => 'Curso assistencial.',
                        PreInterview::PREP_SCHOOL_PRIVATE => 'Curso particular.',
                        PreInterview::PREP_SCHOOL_PRIVATE_SCHOLARSHIP => 'Curso particular com bolsa.',
                        PreInterview::PREP_SCHOOL_NOTHING => 'Não frequentei curso pré-vestibular.',
                    ),
                ),
            ))
            ->add(array(
                'name' => 'language_course',
                'type' => 'radio',
                'options' => array(
                    'label' => 'Você já fez ou faz algum curso de idioma?',
                    'value_options' => array(
                        PreInterview::LANGUAGE_COURSE_ENGLISH => 'Inglês.',
                        PreInterview::LANGUAGE_COURSE_SPANISH => 'Espanhol.',
                        PreInterview::LANGUAGE_COURSE_ENGLISH_AND_SPANISH => 'Inglês e espanhol.',
                        PreInterview::LANGUAGE_COURSE_OTHER => 'Outro(s).',
                        PreInterview::LANGUAGE_COURSE_NOTHING => 'Não fiz curso de idioma.',
                    ),
                ),
            ))
            ->add(array(
                'name' => 'current_study',
                'type' => 'radio',
                'options' => array(
                    'label' => 'Você estuda atualmente?',
                    'value_options' => array(
                        PreInterview::CURRENT_STUDY_HIGH_SCHOOL => 'Sim, faço Ensino Médio.',
                        PreInterview::CURRENT_STUDY_PREP_SCHOOL => 'Sim, faço outro curso pré-vestibular.',
                        PreInterview::CURRENT_STUDY_CERTIFICATE_PROGRAM => 'Sim, faço curso técnico'
                        . '/profissionalizante.',
                        PreInterview::CURRENT_STUDY_HIGHER_EDUCATION => 'Sim, faço curso superior.',
                        PreInterview::CURRENT_STUDY_NOTHING => 'Não estudo atualmente.',
                    ),
                ),
            ))
            ->add(array(
                'name' => 'live_with_number',
                'type' => 'radio',
                'options' => array(
                    'label' => 'Quantas pessoas moram em sua casa (incluindo você)?',
                    'value_options' => array(
                        PreInterview::ONE => 'Uma pessoa.',
                        PreInterview::TWO => 'Duas pessoas.',
                        PreInterview::THREE => 'Três pessoas.',
                        PreInterview::FOUR => 'Quatro pessoas.',
                        PreInterview::FIVE => 'Cinco pessoas.',
                        PreInterview::SIX => 'Seis pessoas.',
                        PreInterview::MORE_THAN_SIX => 'Mais de seis pessoas.',
                    ),
                ),
            ))
            ->add(array(
                'name' => 'live_with_you',
                'type' => 'MultiCheckbox',
                'options' => array(
                    'label' => 'Quem mora com você?',
                    'value_options' => array(
                        PreInterview::LIVE_WITH_YOU_ALONE => 'Moro sozinho.',
                        PreInterview::LIVE_WITH_YOU_CHILDREN => 'Filhos.',
                        PreInterview::LIVE_WITH_YOU_PARENTS => 'Moro com pai e/ou mãe.',
                        PreInterview::LIVE_WITH_YOU_SIBLINGS => 'Irmãos.',
                        PreInterview::LIVE_WITH_YOU_LIFE_PARTNER => 'Esposa, marido, companheiro(a).',
                        PreInterview::LIVE_WITH_YOU_OTHER => 'Outro.',
                    ),
                ),
            ))
            ->add(array(
                'name' => 'number_of_rooms',
                'type' => 'radio',
                'options' => array(
                    'label' => 'Quantos cômodos possui sua residência (banheiro não entra na contagem)?',
                    'value_options' => array(
                        PreInterview::ONE => 'Um cômodo.',
                        PreInterview::TWO => 'Dois cômodos.',
                        PreInterview::THREE => 'Três cômodos.',
                        PreInterview::FOUR => 'Quatro cômodos.',
                        PreInterview::FIVE => 'Cinco cômodos.',
                        PreInterview::SIX => 'Seis cômodos.',
                        PreInterview::MORE_THAN_SIX => 'Mais de seis cômodos.',
                    ),
                ),
            ))
            ->add(array(
                'name' => 'means_of_transport',
                'type' => 'radio',
                'options' => array(
                    'label' => 'Qual o principal meio de transporte você utilizará para chegar no CATS?',
                    'value_options' => array(
                        PreInterview::MEANS_OF_TRANSPORT_BYCICLE => 'Bicicleta, carona. ',
                        PreInterview::MEANS_OF_TRANSPORT_ON_FOOT => 'A pé, carona.',
                        PreInterview::MEANS_OF_TRANSPORT_SCHOLAR => 'Transporte escolar (gratuito).',
                        PreInterview::MEANS_OF_TRANSPORT_PRIVATE_COLLETIVE => 'Transporte coletivo (particular).',
                        PreInterview::MEANS_OF_TRANSPORT_PRIVATE => 'Transporte próprio (carro/moto).',
                        PreInterview::MEANS_OF_TRANSPORT_OTHER => 'Outro.',
                    ),
                ),
            ))
            ->add(array(
                'name' => 'monthly_income',
                'type' => 'radio',
                'options' => array(
                    'label' => 'Qual sua renda mensal individual?',
                    'value_options' => array(
                        PreInterview::MONTHLY_INCOME_NOTHING => 'Nenhuma.',
                        PreInterview::MONTHLY_INCOME_LESS_THAN_ONE_MINIMUM_WAGE => 'Menos de 01 '
                        . 'salário mínimo.',
                        PreInterview::MONTHLY_INCOME_BETWEEN_ONE_AND_TWO_MINIMUM_WAGES => 'Acima de 01 '
                        . 'até 02 salários mínimos.',
                        PreInterview::MONTHLY_INCOME_BETWEEN_TWO_AND_THREE_MINIMUM_WAGES => 'Acima de 02 '
                        . 'até 03 salários mínimos.',
                        PreInterview::MONTHLY_INCOME_BETWEEN_THREE_AND_FOUR_MINIMUM_WAGES => 'Acima de 03 '
                        . 'até 04 salários mínimos.',
                        PreInterview::MONTHLY_INCOME_MORE_THAN_FOUR_MINIMUM_WAGES => 'Mais de 04 '
                        . 'salários mínimos.',
                    ),
                ),
            ))->add(array(
                'name' => 'father_education_grade',
                'type' => 'radio',
                'options' => array(
                    'label' => 'Qual o grau de escolaridade de seu pai?',
                    'value_options' => array(
                        PreInterview::PARENT_SCHOOL_GRADE_INCOMPLETE_ELEMENTARY_SCHOOL => 'Primeiro'
                        . ' grau (ensino fundamental) incompleto.',
                        PreInterview::PARENT_SCHOOL_GRADE_COMPLETE_ELEMENTARY_SCHOOL => 'Primeiro '
                        . 'grau (ensino fundamental) completo.',
                        PreInterview::PARENT_SCHOOL_GRADE_INCOMPLETE_HIGH_SCHOOL => 'Segundo '
                        . 'grau (colegial) incompleto.',
                        PreInterview::PARENT_SCHOOL_GRADE_COMPLETE_HIGH_SCHOOL => 'Segundo '
                        . 'grau (colegial) completo.',
                        PreInterview::PARENT_SCHOOL_GRADE_INCOMPLETE_UNDERGRADUATE_COURSE => 'Ensino '
                        . 'superior (faculdade/universidade) incompleto.',
                        PreInterview::PARENT_SCHOOL_GRADE_COMPLETE_UNDERGRADUATE_COURSE => 'Ensino '
                        . 'superior (faculdade/universidade) completo.',
                        PreInterview::PARENT_SCHOOL_GRADE_GRADUATE_SPECIALIZATION => 'Pós-graduação '
                        . '(especialização) completa.',
                        PreInterview::PARENT_SCHOOL_GRADE_MASTER_DEGREE => 'Mestrado completo.',
                        PreInterview::PARENT_SCHOOL_GRADE_DOCTORATE_DEGREE => 'Doutorado completo.',
                    ),
                ),
            ))
            ->add(array(
                'name' => 'mother_education_grade',
                'type' => 'radio',
                'options' => array(
                    'label' => 'Qual o grau de escolaridade de sua mãe?',
                    'value_options' => array(
                        PreInterview::PARENT_SCHOOL_GRADE_INCOMPLETE_ELEMENTARY_SCHOOL => 'Primeiro'
                        . ' grau (ensino fundamental) incompleto.',
                        PreInterview::PARENT_SCHOOL_GRADE_COMPLETE_ELEMENTARY_SCHOOL => 'Primeiro '
                        . 'grau (ensino fundamental) completo.',
                        PreInterview::PARENT_SCHOOL_GRADE_INCOMPLETE_HIGH_SCHOOL => 'Segundo '
                        . 'grau (colegial) incompleto.',
                        PreInterview::PARENT_SCHOOL_GRADE_COMPLETE_HIGH_SCHOOL => 'Segundo '
                        . 'grau (colegial) completo.',
                        PreInterview::PARENT_SCHOOL_GRADE_INCOMPLETE_UNDERGRADUATE_COURSE => 'Ensino '
                        . 'superior (faculdade/universidade) incompleto.',
                        PreInterview::PARENT_SCHOOL_GRADE_COMPLETE_UNDERGRADUATE_COURSE => 'Ensino '
                        . 'superior (faculdade/universidade) completo.',
                        PreInterview::PARENT_SCHOOL_GRADE_GRADUATE_SPECIALIZATION => 'Pós-graduação '
                        . '(especialização) completa.',
                        PreInterview::PARENT_SCHOOL_GRADE_MASTER_DEGREE => 'Mestrado completo.',
                        PreInterview::PARENT_SCHOOL_GRADE_DOCTORATE_DEGREE => 'Doutorado completo.',
                    )
                )
            ))
            ->add(array(
                'name' => 'expect_from_us',
                'attributes' => array(
                    'type' => 'textarea',
                    'rows' => 6,
                ),
                'options' => array(
                    'label' => 'O que você espera que o CATS te proporcione?',
                )
            ))
            ->add(array(
                'name' => 'submit',
                'attributes' => array(
                    'type' => 'submit',
                    'value' => 'Concluir',
                    'class' => 'btn btn-success btn-block',
                )
        ));
    }

    protected function getYears()
    {
        $year = date('Y');
        $options = [];
        for ($i = 1; $i < 51; $i++) {
            $options[$year] = $year--;
        }

        return $options;
    }

}
