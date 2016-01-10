<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Recruitment\Entity;

use Doctrine\ORM\Mapping as ORM;
use Recruitment\Entity\Recruitment;
use Recruitment\Entity\Person;

/**
 * Description of Registration
 *
 * @author marcio
 * @ORM\Table(name="registration", 
 *      uniqueConstraints={@ORM\UniqueConstraint(name="person_recruitment_idx", columns={"recruitment_id", "person_id"})},
 * )
 * @ORM\Entity(repositoryClass="Recruitment\Entity\Repository\Registration")
 */
class Registration
{

    /**
     * $registrationKnowAbout possible values
     */
    const FAMILY = 'Familiares';
    const UNIVERSIRTY_STUDENTS = 'Alunos da UNIFEI';
    const STUDENTS = 'Alunos do CATS';
    const FRIENDS = 'Amigos';
    const INTERNET = 'Internet';
    const COMMON_COMMUNICATION_CHANNELS = 'Rádio, Televisão ou Jornais';
    const SCHOOL = 'Divulgação em sua escola';
    const VOLUNTEERS = 'Voluntários do CATS';
    const REGISTRATION_PAD_LENGTH = 8;

    /**
     *
     * @var integer
     * @ORM\Column(name="registration_id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $registrationId;

    /**
     *
     * @var \DateTime
     * @ORM\Column(name="registration_date", type="datetime", nullable=false)
     */
    private $registrationDate;

    /**
     *
     * @var \DateTime
     * @ORM\Column(name="registration_confirmation_date", type="datetime", nullable=true)
     */
    private $registrationConfirmationDate;

    /**
     *
     * @var \DateTime
     * @ORM\Column(name="registration_convocation_date", type="datetime", nullable=true)
     */
    private $registrationConvocationDate;

    /**
     *
     * @var \DateTime
     * @ORM\Column(name="registration_acceptance_date", type="datetime", nullable=true)
     */
    private $registrationAcceptanceDate;

    /**
     *
     * @var Recruitment
     * @ORM\ManyToOne(targetEntity="\Recruitment\Entity\Recruitment", inversedBy="registrations", fetch="EAGER")
     * @ORM\JoinColumn(name="recruitment_id", referencedColumnName="recruitment_id", nullable=false)
     */
    private $recruitment;

    /**
     *
     * @var Recruitment\Entity\PreInterview
     * @ORM\OneToOne(targetEntity="Recruitment\Entity\PreInterview", mappedBy="registration")
     * @ORM\JoinColumn(name="pre_inteview_id", referencedColumnName="pre_interview_id")
     */
    private $preInterview;

    /**
     * @var Person
     * @ORM\ManyToOne(targetEntity="\Recruitment\Entity\Person", inversedBy="registrations", fetch="EAGER")
     * @ORM\JoinColumn(name="person_id", referencedColumnName="person_id", nullable=false)
     */
    private $person;

    /**
     *
     * @var string
     * @ORM\Column(name="registration_know_about", type="string", length=500, nullable=false)
     */
    private $registrationKnowAbout;

    public function __construct()
    {
        $this->registrationDate = new \DateTime('now');
    }

    /**
     * 
     * @return integer
     */
    public function getRegistrationId()
    {
        return $this->registrationId;
    }

    /**
     * 
     * @return \DateTime
     */
    public function getRegistrationDate()
    {
        return $this->registrationDate;
    }

    /**
     * 
     * @return mixed \DateTime | null
     */
    public function getRegistrationConfirmationDate()
    {
        return $this->registrationConfirmationDate;
    }

    /**
     * 
     * @param mixed $registrationConfirmationDate \DateTime | null
     * @return Registration
     */
    public function setRegistrationConfirmationDate($registrationConfirmationDate)
    {
        $this->registrationConfirmationDate = $registrationConfirmationDate;
        return $this;
    }

    /**
     * 
     * @return mixed \DateTime | null
     */
    public function getRegistrationConvocationDate()
    {
        return $this->registrationConvocationDate;
    }

    /**
     * 
     * @param mixed $registrationConvocationDate \DateTime | null
     * @return Registration
     */
    public function setRegistrationConvocationDate($registrationConvocationDate)
    {
        $this->registrationConvocationDate = $registrationConvocationDate;
        return $this;
    }

    /**
     * 
     * @return Recruitment
     */
    public function getRecruitment()
    {
        return $this->recruitment;
    }

    /**
     * 
     * @param Recruitment $recruitment
     */
    public function setRecruitment(Recruitment $recruitment)
    {
        $recruitment->addRegistration($this);
        $this->recruitment = $recruitment;
        return $this;
    }

    /**
     * 
     * @return Person
     */
    public function getPerson()
    {
        return $this->person;
    }

    /**
     * @param Person $person
     * @return Registration
     */
    function setPerson(Person $person)
    {
        $person->addRegistration($this);
        $this->person = $person;
        return $this;
    }

    /**
     * 
     * @return string
     */
    public function getRegistrationKnowAbout()
    {
        return $this->registrationKnowAbout;
    }

    /**
     * 
     * @param string $registrationKnowAbout
     * @return Registration
     * @throws \InvalidArgumentException
     */
    public function addRegistrationKnowAbout($registrationKnowAbout)
    {
        if (in_array($registrationKnowAbout,
                array(
                self::FAMILY,
                self::UNIVERSIRTY_STUDENTS,
                self::STUDENTS,
                self::FRIENDS,
                self::INTERNET,
                self::COMMON_COMMUNICATION_CHANNELS,
                self::SCHOOL,
                self::VOLUNTEERS,
            ))) {

            if ($this->registrationKnowAbout != null) {
                $this->registrationKnowAbout .= ';' . $registrationKnowAbout;
            } else {
                $this->registrationKnowAbout = $registrationKnowAbout;
            }

            return $this;
        }
        throw new \InvalidArgumentException('invalid registration know about.');
    }

    /**
     * 
     * @return mixed \DateTime | null
     */
    public function getRegistrationAcceptanceDate()
    {
        return $this->registrationAcceptanceDate;
    }

    /**
     * 
     * @param mixed $registrationAcceptanceDate \DateTime | null
     * @return \Recruitment\Entity\Registration
     */
    public function setRegistrationAcceptanceDate($registrationAcceptanceDate)
    {
        $this->registrationAcceptanceDate = $registrationAcceptanceDate;
        return $this;
    }

    /**
     * 
     * @return string
     */
    public function getRegistrationNumber()
    {
        $regNum = $this->recruitment->getRecruitmentYear() .
            $this->recruitment->getRecruitmentNumber() .
            str_pad($this->registrationId, self::REGISTRATION_PAD_LENGTH, '0', STR_PAD_LEFT
        );

        return $regNum;
    }

    /**
     * 
     * @return Recruitment\Entity\PreInterview
     */
    public function getPreInterview()
    {
        return $this->preInterview;
    }

    /**
     * 
     * @param Recruitment\Entity\PreInterview $preInterview
     * @return Recruitment\Entity\Registration
     */
    public function setPreInterview(PreInterview $preInterview)
    {
        $this->preInterview = $preInterview;
        return $this;
    }

}
