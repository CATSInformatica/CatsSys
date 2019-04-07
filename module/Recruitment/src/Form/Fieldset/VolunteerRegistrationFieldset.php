<?php

namespace Recruitment\Form\Fieldset;

use Doctrine\Common\Persistence\ObjectManager;
use DoctrineModule\Stdlib\Hydrator\DoctrineObject as DoctrineHydrator;
use Recruitment\Entity\Registration;
use Recruitment\Entity\Recruitment;
use Zend\InputFilter\InputFilterProviderInterface;

/**
 * Description of VolunteerRegistrationFieldset
 *
 * @author Márcio Dias <marciojr91@gmail.com>
 */
final class VolunteerRegistrationFieldset extends RegistrationFieldset implements InputFilterProviderInterface
{

    /**
     * No formulário de entrevista, diversos campos são ocultados
     *
     * @var bool - formulário da entrevista
     */
    private $interviewForm = false;

    public function __construct(ObjectManager $obj, $options = null)
    {
        $this->setHydrator(new DoctrineHydrator($obj))
            ->setObject(new Registration());

        if (is_array($options)) {
            if (!array_key_exists('interview', $options)) {
                throw new \InvalidArgumentException('The `options` array must contain the key `interview`');
            }
            if (!array_key_exists('recruitment', $options)) {
                throw new \InvalidArgumentException('The `options` array must contain the key `recruitment`');
            }
        }

        parent::__construct($obj, $options);

        if (isset($options['interviewForm'])) {
            $this->interviewForm = $options['interviewForm'];
        }

        $openJobsOptions = $this->getOpenJobsOptions($options['recruitment']);

        $this
            ->add(array(
                'name' => 'desiredJob',
                'type' => 'DoctrineModule\Form\Element\ObjectSelect',
                'options' => array(
                    'label' => 'Cargo desejado',
                    'value_options' => $openJobsOptions,
                    'object_manager' => $obj,
                    'target_class' => 'AdministrativeStructure\Entity\Job',
                    'property' => 'jobName',
                ),
            ))
            ->add(array(
                'name' => 'desiredJobs',
                'type' => 'DoctrineModule\Form\Element\ObjectSelect',
                'attributes' => array(
                    'multiple' => 'multiple',
                    'size' => count($openJobsOptions),
                    'class' => 'allow-multiple-clicks',
                ),
                'options' => array(
                    'label' => 'Outro(s) cargo(s) de interesse (opcional)',
                    'value_options' => $openJobsOptions,
                    'object_manager' => $obj,
                    'target_class' => 'AdministrativeStructure\Entity\Job',
                    'property' => 'jobName',
                ),
            ))
            ->add(array(
                'name' => 'occupation',
                'type' => 'textarea',
                'attributes' => [
                    'rows' => 6,
                    'class' => 'col-xs-12 form-control',
                ],
                'options' => array(
                    'label' => 'Ocupação (acadêmica e/ou profissional)*',
                ),
            ))
            ->add(array(
                'name' => 'education',
                'type' => 'textarea',
                'attributes' => [
                    'rows' => 6,
                    'class' => 'col-xs-12 form-control',
                ],
                'options' => array(
                    'label' => 'Fez algum curso (técnico, linguas, etc) ? Qual?*',
                ),
            ));

        $this
            ->add(array(
                'name' => 'howAndWhenKnowUs',
                'type' => 'textarea',
                'attributes' => [
                    'rows' => 6,
                    'class' => 'col-xs-12 form-control',
                ],
                'options' => array(
                    'label' => 'Como e quando conheceu o CATS?*',
                ),
            ))
            ->add(array(
                'name' => 'volunteerWork',
                'type' => 'textarea',
                'attributes' => [
                    'rows' => 6,
                    'class' => 'col-xs-12 form-control',
                ],
                'options' => array(
                    'label' => 'O que pensa sobre trabalho voluntário? Já fez? Descreva*',
                ),
            ))
            ->add(array(
                'name' => 'extensionProjects',
                'type' => 'textarea',
                'attributes' => [
                    'rows' => 6,
                    'class' => 'col-xs-12 form-control',
                ],
                'options' => array(
                    'label' => 'Participa de outro projeto de extensão?*',
                ),
            ))
            ->add(array(
                'name' => 'whyWorkWithUs',
                'type' => 'textarea',
                'attributes' => [
                    'rows' => 6,
                    'class' => 'col-xs-12 form-control',
                ],
                'options' => array(
                    'label' => 'Por que escolheu se inscrever no CATS? Tentou outros projetos?*',
                ),
            ))
            ->add(array(
                'name' => 'volunteerWithUs',
                'type' => 'textarea',
                'attributes' => [
                    'rows' => 6,
                    'class' => 'col-xs-12 form-control',
                ],
                'options' => array(
                    'label' => 'O que espera do trabalho voluntário no CATS?*',
                ),
            ))
        ;

        if ($options['interview']) {
            $this->add(new VolunteerInterviewFieldset($obj));
        }
    }

    /**
     * Retorna um array associativo com os ids e nomes dos cargos abertos do PSV.
     * O array tem a forma conforme a seguir:
     *  [
     *      <id> => <jobName>,
     *      .
     *      .
     *      .
     *  ]
     *
     * @param Recruitment $recruitment - PSV
     * @return array
     */
    private function getOpenJobsOptions(Recruitment $recruitment) {
        $jobs = $recruitment->getOpenJobs();
        $jobsNames = [];

        foreach ($jobs as $job) {
            $jobsNames[$job->getJobId()] = $job->getJobName();
        }

        return $jobsNames;
    }

    public function getInputFilterSpecification()
    {
        return array(
            'desiredJob' => array(
                'required' => !$this->interviewForm,
            ),
            'desiredJobs' => array(
                'required' => false,
            ),
            'occupation' => array(
                'required' => !$this->interviewForm,
                'filters' => array(
                    array('name' => 'StringTrim'),
                    array('name' => 'StripTags'),
                ),
                'validators' => array(
                    array(
                        'name' => 'StringLength',
                        'options' => array(
                            'min' => 3,
                            'max' => 700,
                            'inclusive' => true,
                        ),
                    ),
                ),
            ),
            'education' => array(
                'required' => !$this->interviewForm,
                'filters' => array(
                    array('name' => 'StringTrim'),
                    array('name' => 'StripTags'),
                ),
                'validators' => array(
                    array(
                        'name' => 'StringLength',
                        'options' => array(
                            'min' => 0,
                            'max' => 700,
                            'inclusive' => true,
                        ),
                    ),
                ),
            ),
            'volunteerWork' => array(
                'required' => $this->interviewForm,
                'filters' => array(
                    array('name' => 'StringTrim'),
                    array('name' => 'StripTags'),
                ),
                'validators' => array(
                    array(
                        'name' => 'StringLength',
                        'options' => array(
                            'min' => 0,
                            'max' => 700,
                            'inclusive' => true,
                        ),
                    ),
                ),
            ),
            'howAndWhenKnowUs' => array(
                'required' => $this->interviewForm,
                'filters' => array(
                    array('name' => 'StringTrim'),
                    array('name' => 'StripTags'),
                ),
                'validators' => array(
                    array(
                        'name' => 'StringLength',
                        'options' => array(
                            'min' => 3,
                            'max' => 700,
                            'inclusive' => true,
                        ),
                    ),
                ),
            ),
            'extensionProjects' => array(
                'required' => $this->interviewForm,
                'filters' => array(
                    array('name' => 'StringTrim'),
                    array('name' => 'StripTags'),
                ),
                'validators' => array(
                    array(
                        'name' => 'StringLength',
                        'options' => array(
                            'min' => 3,
                            'max' => 400,
                            'inclusive' => true,
                        ),
                    ),
                ),
            ),
            'whyWorkWithUs' => array(
                'required' => $this->interviewForm,
                'filters' => array(
                    array('name' => 'StringTrim'),
                    array('name' => 'StripTags'),
                ),
                'validators' => array(
                    array(
                        'name' => 'StringLength',
                        'options' => array(
                            'min' => 3,
                            'max' => 700,
                            'inclusive' => true,
                        ),
                    ),
                ),
            ),
            'volunteerWithUs' => array(
                'required' => $this->interviewForm,
                'filters' => array(
                    array('name' => 'StringTrim'),
                    array('name' => 'StripTags'),
                ),
                'validators' => array(
                    array(
                        'name' => 'StringLength',
                        'options' => array(
                            'min' => 3,
                            'max' => 700,
                            'inclusive' => true,
                        ),
                    ),
                ),
            ),
        );
    }

}
