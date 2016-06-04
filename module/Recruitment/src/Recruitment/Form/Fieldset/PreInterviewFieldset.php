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


        $numberedOptions = [
            '0' => 'Nenhum',
            '1' => '1',
            '2' => '2',
            '3' => '3',
            '4' => '4',
            '5' => '5',
            '6 ou mais' => '6 ou mais',
        ];

        // VULNERABILIDADE
        $this->add(array(
                'name' => 'elementarySchoolType',
                'type' => 'radio',
                'options' => array(
                    'label' => 'A Instituição de ensino na qual cursou o ensino fundamental é?',
                    'value_options' => PreInterview::getElementarySchoolTypeArray(),
                ),
                'inline' => false,
            ))
            ->add(array(
                'name' => 'highSchoolType',
                'type' => 'radio',
                'options' => array(
                    'label' => 'Você cursou/cursa o ensino médio em escola(s):',
                    'value_options' => PreInterview::getHighSchoolTypeArray(),
                )
            ))
            ->add([
                'name' => 'highSchoolAdmissionYear',
                'type' => 'number',
                'options' => [
                    'label' => 'Ano de ingresso no ensino médio?',
                ],
            ])
            ->add([
                'name' => 'highSchoolConclusionYear',
                'type' => 'number',
                'options' => [
                    'label' => 'Ano de conclusão/previsão de conclusão do ensino médio?',
                ],
            ])
            ->add([
                'name' => 'siblingsUndergraduate',
                'type' => 'textarea',
                'options' => [
                    'label' => 'Tem irmãos que cursaram/cursam o ensino superior?',
                ],
                'attributes' => [
                    'rows' => 3,
                ],
            ])
            ->add([
                'name' => 'otherLanguages',
                'type' => 'textarea',
                'options' => [
                    'label' => 'Fala algum idioma estrangeiro? Se sim, como estudou? (Cursos preparatórios, por conta '
                    . 'própria, com amigos, ...)',
                ],
                'attributes' => [
                    'rows' => 3,
                ],
            ])
            ->add([
                'name' => 'homeStatus',
                'type' => 'radio',
                'options' => [
                    'label' => 'Imovel em que reside é?',
                    'value_options' => PreInterview::getHomeStatusArray(),
                ],
            ])
            ->add([
                'name' => 'homeDescription',
                'type' => 'radio',
                'options' => [
                    'label' => 'Marque a característica que melhor descreve a sua casa?',
                    'value_options' => PreInterview::getHomeDescriptionArray(),
                ],
            ])
            ->add([
                'name' => 'transport',
                'type' => 'radio',
                'options' => [
                    'label' => 'Transporte utilizado para comparecer às aulas:',
                    'value_options' => PreInterview::getTransportArray(),
                ],
            ])
            // PERFIL DE ESTUDANTE
            ->add([
                'name' => 'extraCourses',
                'type' => 'textarea',
                'options' => [
                    'label' => 'Fez algum curso extraclasse? Se sim, qual(is) curso(s)?',
                ],
                'attributes' => [
                    'rows' => 3,
                ],
            ])
            ->add([
                'name' => 'preparationCourse',
                'type' => 'textarea',
                'options' => [
                    'label' => 'já fez curso pré-vestibular? Se sim, qual(is) curso(s) pré-vestibular(es)?',
                ],
                'attributes' => [
                    'rows' => 3,
                ],
            ])
            ->add([
                'name' => 'entranceExam',
                'type' => 'textarea',
                'options' => [
                    'label' => 'Já prestou algum vestibular ou concurso? Se sim, qual(is) vestibular(es)?',
                ],
                'attributes' => [
                    'rows' => 3,
                ],
            ])
            ->add([
                'name' => 'undergraduateCourse',
                'type' => 'textarea',
                'options' => [
                    'label' => 'Já ingressou no ensino superior? Se sim, ainda cursa?',
                ],
                'attributes' => [
                    'rows' => 3,
                ],
            ])
            ->add([
                'name' => 'waitingForUs',
                'type' => 'textarea',
                'options' => [
                    'label' => 'O que espera de nós e o que pretende alcançar caso seja aprovado?',
                ],
                'attributes' => [
                    'rows' => 3,
                ],
            ])
            //SOCIOECONOMICO
            ->add(array(
                'name' => 'live',
                'type' => 'radio',
                'options' => array(
                    'label' => 'Você mora:',
                    'value_options' => PreInterview::getLiveArray(),
                )
            ))
            ->add(array(
                'name' => 'responsibleFinancial',
                'type' => 'radio',
                'options' => array(
                    'label' => 'Quem é(são) o(os) responsável(is) pela manutenção financeira do grupo familiar?',
                    'value_options' => PreInterview::getResponsibleFinancialArray(),
                )
            ))
            ->add(array(
                'name' => 'infrastructureElements',
                'type' => 'DoctrineModule\Form\Element\ObjectMultiCheckbox',
                'options' => array(
                    'label' => 'A casa onde mora têm:',
                    'object_manager' => $obj,
                    'target_class' => 'Recruitment\Entity\InfrastructureElement',
                    'property' => 'infrastructureElementDescription',
                ),
            ))
            ->add(array(
                'name' => 'liveArea',
                'type' => 'radio',
                'options' => array(
                    'label' => 'Você reside em:',
                    'value_options' => PreInterview::getLiveAreaArray(),
                )
            ))
            ->add(array(
                'name' => 'itemTv',
                'type' => 'radio',
                'options' => array(
                    'label' => 'Tv',
                    'value_options' => $numberedOptions,
                    'inline' => true,
                )
            ))
            ->add(array(
                'name' => 'itemBathroom',
                'type' => 'radio',
                'options' => array(
                    'label' => 'Banheiro',
                    'value_options' => $numberedOptions,
                    'inline' => true,
                )
            ))
            ->add(array(
                'name' => 'itemSalariedHousekeeper',
                'type' => 'radio',
                'options' => array(
                    'label' => 'empregada mensalista',
                    'value_options' => $numberedOptions,
                    'inline' => true,
                )
            ))
            ->add(array(
                'name' => 'itemDailyHousekeeper',
                'type' => 'radio',
                'options' => array(
                    'label' => 'empregada diarista',
                    'value_options' => $numberedOptions,
                    'inline' => true,
                )
            ))
            ->add(array(
                'name' => 'itemWashingMachine',
                'type' => 'radio',
                'options' => array(
                    'label' => 'máquina de lavar roupa',
                    'value_options' => $numberedOptions,
                    'inline' => true,
                )
            ))
            ->add(array(
                'name' => 'itemRefrigerator',
                'type' => 'radio',
                'options' => array(
                    'label' => 'geladeira',
                    'value_options' => $numberedOptions,
                    'inline' => true,
                )
            ))
            ->add(array(
                'name' => 'itemCableTv',
                'type' => 'radio',
                'options' => array(
                    'label' => 'TV a cabo',
                    'value_options' => $numberedOptions,
                    'inline' => true,
                )
            ))
            ->add(array(
                'name' => 'itemComputer',
                'type' => 'radio',
                'options' => array(
                    'label' => 'computador',
                    'value_options' => $numberedOptions,
                    'inline' => true,
                )
            ))
            ->add(array(
                'name' => 'smartphone',
                'type' => 'radio',
                'options' => array(
                    'label' => 'smartphones',
                    'value_options' => $numberedOptions,
                    'inline' => true,
                )
            ))
            ->add(array(
                'name' => 'bedroom',
                'type' => 'radio',
                'options' => array(
                    'label' => 'quartos',
                    'value_options' => $numberedOptions,
                    'inline' => true,
                )
            ))
        ;
    }

    public function getInputFilterSpecification()
    {
        return array(
            'elementarySchoolType' => array(
                'required' => true,
            ),
            'highSchoolType' => array(
                'required' => true,
            ),
//            'preInterviewHighSchool' => array(
//                'required' => true,
//                'filters' => array(
//                    array(
//                        'name' => 'StringToUpper',
//                        'options' => array(
//                            'encoding' => 'UTF-8',
//                        ),
//                    ),
//                ),
//                'validators' => array(
//                    array(
//                        'name' => 'Zend\Validator\StringLength',
//                        'options' => array(
//                            'min' => 5,
//                            'max' => 150,
//                        ),
//                    ),
//                ),
//            ),
//            'preInterviewHSConclusionYear' => array(
//                'required' => true,
//            ),
//            'preInterviewPreparationSchool' => array(
//                'required' => true,
//            ),
//            'preInterviewLanguageCourse' => array(
//                'required' => true,
//            ),
//            'preInterviewCurrentStudy' => array(
//                'required' => true,
//            ),
//            'preInterviewLiveWithNumber' => array(
//                'required' => true,
//            ),
//            'preInterviewLiveWithYou' => array(
//                'required' => true,
//            ),
//            'preInterviewNumberOfRooms' => array(
//                'required' => true,
//            ),
//            'preInterviewMeansOfTransport' => array(
//                'required' => true,
//            ),
//            'preInterviewMonthlyIncome' => array(
//                'required' => true,
//            ),
//            'preInterviewFatherEducationGrade' => array(
//                'required' => true,
//            ),
//            'preInterviewMotherEducationGrade' => array(
//                'required' => true,
//            ),
//            'preInterviewExpectFromUs' => array(
//                'required' => true,
//                'filters' => array(
//                    array('name' => 'StringTrim'),
//                    array('name' => 'StripTags'),
//                ),
//                'validators' => array(
//                    array(
//                        'name' => 'Zend\Validator\StringLength',
//                        'options' => array(
//                            'min' => 20,
//                            'max' => 200,
//                        ),
//                    ),
//                ),
//            ),
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
