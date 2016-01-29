<?php

namespace Recruitment\Form;

use Recruitment\Entity\RecruitmentStatus;
use Zend\Form\Form;
use Zend\InputFilter\InputFilterProviderInterface;
use Doctrine\Common\Persistence\ObjectManager;

/**
 * Description of RegistrationSearchForm
 *
 * @author Márcio Dias <marciojr91@gmail.com>
 */
class SearchRegistrationsForm extends Form implements InputFilterProviderInterface
{

    public function __construct(ObjectManager $obj, $rtype = null, $name = null, $options = array())
    {
        parent::__construct($name, $options);

        if ($rtype == null) {
            $recruitments = $obj->getRepository('Recruitment\Entity\Recruitment')->findBy(
                [], array('recruitmentId' => 'DESC')
            );
        } else {
            $recruitments = $obj->getRepository('Recruitment\Entity\Recruitment')->findBy(
                array('recruitmentType' => $rtype), array('recruitmentId' => 'DESC')
            );
        }


        $this
            ->add(array(
                'name' => 'recruitment',
                'type' => 'select',
                'options' => array(
                    'label' => 'Processo seletivo',
                    'value_options' => $this->getRecruitments($recruitments),
                ),
            ))
            ->add(array(
                'name' => 'registrationStatus',
                'type' => 'select',
                'options' => array(
                    'label' => 'Situação',
                    'value_options' => array(
                        RecruitmentStatus::STATUSTYPE_REGISTERED
                        => RecruitmentStatus::STATUSTYPEDESC_REGISTERED,
                        RecruitmentStatus::STATUSTYPE_CALLEDFOR_INTERVIEW
                        => RecruitmentStatus::STATUSTYPEDESC_CALLEDFOR_INTERVIEW,
                        RecruitmentStatus::STATUSTYPE_CANCELED_REGISTRATION
                        => RecruitmentStatus::STATUSTYPEDESC_CANCELED_REGISTRATION,
                        RecruitmentStatus::STATUSTYPE_INTERVIEWED
                        => RecruitmentStatus::STATUSTYPEDESC_INTERVIEWED,
                        RecruitmentStatus::STATUSTYPE_INTERVIEW_WAITINGLIST
                        => RecruitmentStatus::STATUSTYPEDESC_INTERVIEW_WAITINGLIST,
                        RecruitmentStatus::STATUSTYPE_INTERVIEW_APPROVED
                        => RecruitmentStatus::STATUSTYPEDESC_INTERVIEW_APPROVED,
                        RecruitmentStatus::STATUSTYPE_INTERVIEW_DISAPPROVED
                        => RecruitmentStatus::STATUSTYPEDESC_INTERVIEW_DISAPPROVED,
                        RecruitmentStatus::STATUSTYPE_VOLUNTEER
                        => RecruitmentStatus::STATUSTYPEDESC_VOLUNTEER,
                        RecruitmentStatus::STATUSTYPE_CALLEDFOR_TESTCLASS
                        => RecruitmentStatus::STATUSTYPEDESC_CALLEDFOR_TESTCLASS,
                        RecruitmentStatus::STATUSTYPE_TESTCLASS_COMPLETE
                        => RecruitmentStatus::STATUSTYPEDESC_TESTCLASS_COMPLETE,
                        RecruitmentStatus::STATUSTYPE_TESTCLASS_WAITINGLIST
                        => RecruitmentStatus::STATUSTYPEDESC_TESTCLASS_WAITINGLIST,
                        RecruitmentStatus::STATUSTYPE_CONFIRMED
                        => RecruitmentStatus::STATUSTYPEDESC_CONFIRMED,
                        RecruitmentStatus::STATUSTYPE_CALLEDFOR_PREINTERVIEW
                        => RecruitmentStatus::STATUSTYPEDESC_CALLEDFOR_PREINTERVIEW,
                        RecruitmentStatus::STATUSTYPE_PREINTERVIEW_COMPLETE
                        => RecruitmentStatus::STATUSTYPEDESC_PREINTERVIEW_COMPLETE,
                    ),
                ),
            ))
            ->add(array(
                'name' => 'submit',
                'type' => 'button',
                'attributes' => array(
                    'value' => 'Buscar',
                    'class' => 'btn-primary btn-block',
                ),
                'options' => array(
                    'label' => 'Buscar',
                    'glyphicon' => 'search',
                ),
            ))
        ;
    }

    protected function getRecruitments($recruitments)
    {
        $rArr = [];

        foreach ($recruitments as $r) {
            $rArr[$r->getRecruitmentId()] = $r->getRecruitmentNumber() . 'º Processo Seletivo de '
                . $r->getRecruitmentYear();
        }

        return $rArr;
    }

    public function getInputFilterSpecification()
    {
        return array(
            'recruitment' => array(
                'required' => true,
            ),
            'registrationStatus' => array(
                'required' => true,
            ),
        );
    }

//put your code here
}
