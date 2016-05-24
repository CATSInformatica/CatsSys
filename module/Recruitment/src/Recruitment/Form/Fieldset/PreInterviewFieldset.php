<?php

namespace Recruitment\Form\Fieldset;

use Doctrine\Common\Persistence\ObjectManager;
use DoctrineModule\Stdlib\Hydrator\DoctrineObject as DoctrineHydrator;
use Recruitment\Entity\PreInterview;
use Zend\Form\Fieldset;
use Zend\InputFilter\InputFilterProviderInterface;

/**
 * Description of PreInterviewFieldset
 *
 * @author Márcio Dias <marciojr91@gmail.com>
 */
class PreInterviewFieldset extends Fieldset implements InputFilterProviderInterface
{

    public function __construct(ObjectManager $obj)
    {
        parent::__construct('preInterview');

        $this->setHydrator(new DoctrineHydrator($obj))
            ->setObject(new PreInterview());


        $this->add(array(
                'name' => 'preInterviewElementarySchoolType',
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
                'name' => 'preInterviewHighSchoolType',
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
                'name' => 'preInterviewHighSchool',
                'type' => 'text',
                'options' => array(
                    'label' => 'Escola onde cursou ou está cursando o ensino médio',
                ),
                'attributes' => array(
                    'placeholder' => 'Ex: Escola Estadual Major João Pereira',
                ),
            ))
            ->add(array(
                'name' => 'preInterviewHSConclusionYear',
                'type' => 'select',
                'options' => array(
                    'label' => 'Conclusão',
                    'empty_option' => 'Ano',
                    'value_options' => self::getYears(),
                ),
            ))
            ->add(array(
                'name' => 'preInterviewPreparationSchool',
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
                'name' => 'preInterviewLanguageCourse',
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
                'name' => 'preInterviewCurrentStudy',
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
                'name' => 'preInterviewLiveWithNumber',
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
                'name' => 'preInterviewLiveWithYou',
                'type' => 'DoctrineModule\Form\Element\ObjectMultiCheckbox',
                'options' => array(
                    'label' => 'Quem mora com você?*',
                    'object_manager' => $obj,
                    'target_class' => 'Recruitment\Entity\RecruitmentLiveWithYou',
                    'property' => 'recruitmentLiveWithYouDescription',
                ),
            ))
            ->add(array(
                'name' => 'preInterviewNumberOfRooms',
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
                'name' => 'preInterviewMeansOfTransport',
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
                'name' => 'preInterviewMonthlyIncome',
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
                'name' => 'preInterviewFatherEducationGrade',
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
                'name' => 'preInterviewMotherEducationGrade',
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
                'name' => 'preInterviewExpectFromUs',
                'attributes' => array(
                    'type' => 'textarea',
                    'rows' => 6,
                ),
                'options' => array(
                    'label' => 'O que você espera que o CATS te proporcione?',
                )
        ));
    }

    public function getInputFilterSpecification()
    {
        return array(
            'preInterviewElementarySchoolType' => array(
                'required' => true,
            ),
            'preInterviewHighSchoolType' => array(
                'required' => true,
            ),
            'preInterviewHighSchool' => array(
                'required' => true,
                'filters' => array(
                    array(
                        'name' => 'StringToUpper',
                        'options' => array(
                            'encoding' => 'UTF-8',
                        ),
                    ),
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
            ),
            'preInterviewHSConclusionYear' => array(
                'required' => true,
            ),
            'preInterviewPreparationSchool' => array(
                'required' => true,
            ),
            'preInterviewLanguageCourse' => array(
                'required' => true,
            ),
            'preInterviewCurrentStudy' => array(
                'required' => true,
            ),
            'preInterviewLiveWithNumber' => array(
                'required' => true,
            ),
            'preInterviewLiveWithYou' => array(
                'required' => true,
            ),
            'preInterviewNumberOfRooms' => array(
                'required' => true,
            ),
            'preInterviewMeansOfTransport' => array(
                'required' => true,
            ),
            'preInterviewMonthlyIncome' => array(
                'required' => true,
            ),
            'preInterviewFatherEducationGrade' => array(
                'required' => true,
            ),
            'preInterviewMotherEducationGrade' => array(
                'required' => true,
            ),
            'preInterviewExpectFromUs' => array(
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
                        ),
                    ),
                ),
            ),
        );
    }

    protected static function getYears()
    {
        $year = date('Y') + 2;
        $options = [];
        for ($i = 1; $i < 51; $i++) {
            $options[$year] = $year--;
        }
        return $options;
    }

}
