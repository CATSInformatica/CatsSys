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
 * @ORM\Entity
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
     * @var Recruitment
     * @ORM\ManyToOne(targetEntity="\Recruitment\Entity\Recruitment", inversedBy="registrations")
     * @ORM\JoinColumn(name="recruitment_id", referencedColumnName="recruitment_id")
     */
    private $recruitment;

    /**
     * @var Person
     * @ORM\ManyToOne(targetEntity="\Recruitment\Entity\Person", inversedBy="registrations")
     * @ORM\JoinColumn(name="person_id", referencedColumnName="person_id")
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
    public function getRegistrationConfirmationDate()
    {
        return $this->registrationConfirmationDate;
    }

    /**
     * 
     * @param \DateTime $registrationConfirmationDate
     * @return Registration
     */
    public function setRegistrationConfirmationDate(\DateTime $registrationConfirmationDate)
    {
        $this->registrationConfirmationDate = $registrationConfirmationDate;
        return $this;
    }

    /**
     * 
     * @return \DateTime
     */
    public function getRegistrationConvocationDate()
    {
        return $this->registrationConvocationDate;
    }

    /**
     * 
     * @param \DateTime $registrationConvocationDate
     * @return Registration
     */
    public function setRegistrationConvocationDate(\DateTime $registrationConvocationDate)
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
        if (in_array($registrationKnowAbout, array(
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

}
