<?php
/*
 * Copyright (C) 2016 Márcio Dias <marciojr91@gmail.com>
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

namespace Recruitment\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Recruitment\Entity\Person;
use Recruitment\Entity\Recruitment;
use Doctrine\Common\Collections\Criteria;

/**
 * Contém informações de registro de voluntários e alunos.
 *
 * @author Márcio Dias <marciojr91@gmail.com>
 * 
 * @ORM\Table(name="registration", 
 *      uniqueConstraints={@ORM\UniqueConstraint(name="person_recruitment_idx", columns={"recruitment_id", "person_id"})},
 * )
 * @ORM\Entity(repositoryClass="Recruitment\Entity\Repository\RegistrationRepository")
 */
class Registration
{

    const REGISTRATION_PAD_LENGTH = 8;

    /**
     * COMMON ATTRIBUTES
     */

    /**
     *
     * @var integer
     * @ORM\Column(name="registration_id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    protected $registrationId;

    /**
     *
     * @var \DateTime
     * @ORM\Column(name="registration_date", type="datetime", nullable=false)
     */
    protected $registrationDate;

    /**
     *
     * @var Recruitment
     * @ORM\ManyToOne(targetEntity="\Recruitment\Entity\Recruitment", inversedBy="registrations")
     * @ORM\JoinColumn(name="recruitment_id", referencedColumnName="recruitment_id", nullable=false)
     */
    protected $recruitment;

    /**
     *
     * @var Recruitment\Entity\PreInterview
     * @ORM\OneToOne(targetEntity="Recruitment\Entity\PreInterview", mappedBy="registration",
     *  cascade={"persist", "merge"})
     * @ORM\JoinColumn(name="pre_inteview_id", referencedColumnName="pre_interview_id")
     */
    protected $preInterview;

    /**
     * @var Person
     * @ORM\ManyToOne(targetEntity="\Recruitment\Entity\Person", inversedBy="registrations", fetch="EAGER", 
     * cascade={"persist"})
     * @ORM\JoinColumn(name="person_id", referencedColumnName="person_id", nullable=false)
     */
    protected $person;

    /**
     * STUDENT SPECIFIC ATTRIBUTES
     */

    /**
     * 
     * @var Collection
     * @ORM\ManyToMany(targetEntity="RecruitmentKnowAbout", fetch="EAGER")
     * @ORM\JoinTable(name="registration_recruitment_know_about",
     *      joinColumns={@ORM\JoinColumn(name="registration_id", 
     *          referencedColumnName="registration_id")
     *      },
     *      inverseJoinColumns={@ORM\JoinColumn(name="recruitment_know_about_id", 
     *          referencedColumnName="recruitment_know_about_id")}
     * )
     */
    protected $recruitmentKnowAbout;

    /**
     * VOLUNTEER SPECIFIC ATTRIBUTES
     */

    /**
     * @var string
     * @ORM\Column(name="registration_occupation", type="string", length=700, nullable=true)
     */
    protected $occupation;

    /**
     * @var string
     * @ORM\Column(name="registration_education", type="string", length=700, nullable=true)
     */
    protected $education;

    /**
     *
     * @var string
     * @ORM\Column(name="registration_volunteer_work", type="string", length=700, nullable=true)
     */
    protected $volunteerWork;

    /**
     *
     * @var string
     * @ORM\Column(name="registration_howandwhen_knowus", type="string", length=700, nullable=true)
     */
    protected $howAndWhenKnowUs;

    /**
     *
     * @var string
     * @ORM\Column(name="registration_whywork_withus", type="string", length=700, nullable=true)
     */
    protected $whyWorkWithUs;

    /**
     *
     * @var string
     * @ORM\Column(name="registration_volunteer_workwithus", type="string", length=700, nullable=true)
     */
    protected $volunteerWithUs;

    /**
     * self-evaluation levels
     */
    const SELF_EVALUATION_LEVEL_1 = 1;
    const SELF_EVALUATION_LEVEL_2 = 2;
    const SELF_EVALUATION_LEVEL_3 = 3;
    const SELF_EVALUATION_LEVEL_4 = 4;
    const SELF_EVALUATION_LEVEL_5 = 5;

    /**
     *
     * @var integer
     * @ORM\Column(name="registration_responsibility", type="smallint", nullable=true)
     */
    protected $responsibility;

    /**
     *
     * @var integer
     * @ORM\Column(name="registration_proactive", type="smallint", nullable=true)
     */
    protected $proactive;

    /**
     *
     * @var integer
     * @ORM\Column(name="registration_volunteer_spirit", type="smallint", nullable=true)
     */
    protected $volunteerSpirit;

    /**
     *
     * @var integer
     * @ORM\Column(name="registration_commitment", type="smallint", nullable=true)
     */
    protected $commitment;

    /**
     *
     * @var integer
     * @ORM\Column(name="registration_team_work", type="smallint", nullable=true)
     */
    protected $teamWork;

    /**
     *
     * @var integer
     * @ORM\Column(name="registration_efficiency", type="smallint", nullable=true)
     */
    protected $efficiency;

    /**
     * @var integer
     * @ORM\Column(name="registration_courtesy", type="smallint", nullable=true)
     */
    protected $courtesy;

    /**
     * @var Collection
     * @ORM\OneToMany(targetEntity="RegistrationStatus", mappedBy="registration", cascade={"persist", "remove"},
     *      orphanRemoval=true)
     */
    protected $registrationStatus;

    /**
     *
     * @var VolunteerInterview
     * @ORM\OneToOne(targetEntity="VolunteerInterview", inversedBy="registration", cascade={"persist", "merge"})
     * @ORM\JoinColumn(name="volunteer_interview_id", referencedColumnName="volunteer_interview_id")
     */
    protected $volunteerInterview;

    /**
     *
     * @var StudentInterview
     * @ORM\OneToOne(targetEntity="StudentInterview", inversedBy="registration", cascade={"persist", "merge"})
     * @ORM\JoinColumn(name="student_interview_id", referencedColumnName="student_interview_id")
     */
    protected $studentInterview;

    public function __construct()
    {
        $this->registrationDate = new \DateTime('now');
        $this->recruitmentKnowAbout = new ArrayCollection();
        $this->registrationStatus = new ArrayCollection();
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
     * @param string $format
     * @return string
     */
    public function getRegistrationDate($format = 'd/m/Y \à\s H:i:s')
    {
        return $this->registrationDate->format($format);
    }

    /**
     * 
     * @return \DateTime
     */
    public function getRegistrationDateAsDateTime()
    {
        return $this->registrationDate;
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
    public function setPerson(Person $person)
    {
        $person->addRegistration($this);
        $this->person = $person;
        return $this;
    }

    /**
     * 
     * @return Collection
     */
    public function getRecruitmentKnowAbout()
    {
        return $this->recruitmentKnowAbout;
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
        $preInterview->setRegistration($this);
        $this->preInterview = $preInterview;
        return $this;
    }

    /**
     * 
     * @param Collection $arr
     */
    public function addRecruitmentKnowAbout(Collection $arr)
    {
        foreach ($arr as $element) {
            $this->recruitmentKnowAbout->add($element);
        }
    }

    /**
     * 
     * @param Collection $arr
     */
    public function removeRecruitmentKnowAbout(Collection $arr)
    {
        foreach ($arr as $element) {
            $this->recruitmentKnowAbout->removeElement($element);
        }
    }

    /**
     * 
     * @return bool
     */
    public function hasPreInterview()
    {
        return $this->preInterview !== null;
    }

    /**
     * 
     * @return string
     */
    public function getOccupation()
    {
        return $this->occupation;
    }

    /**
     * 
     * @return string
     */
    public function getEducation()
    {
        return $this->education;
    }

    /**
     * 
     * @return string
     */
    public function getVolunteerWork()
    {
        return $this->volunteerWork;
    }

    /**
     * 
     * @return string
     */
    public function getHowAndWhenKnowUs()
    {
        return $this->howAndWhenKnowUs;
    }

    /**
     * 
     * @return string
     */
    public function getWhyWorkWithUs()
    {
        return $this->whyWorkWithUs;
    }

    /**
     * 
     * @return string
     */
    public function getVolunteerWithUs()
    {
        return $this->volunteerWithUs;
    }

    /**
     * 
     * @param string $occupation
     * @return Self
     */
    public function setOccupation($occupation)
    {
        $this->occupation = $occupation;
        return $this;
    }

    /**
     * 
     * @param string $education
     * @return Self
     */
    public function setEducation($education)
    {
        $this->education = $education;
        return $this;
    }

    /**
     * 
     * @param string $volunteerWork
     * @return Self
     */
    public function setVolunteerWork($volunteerWork)
    {
        $this->volunteerWork = $volunteerWork;
        return $this;
    }

    /**
     * 
     * @param string $howAndWhenKnowUs
     * @return Self
     */
    public function setHowAndWhenKnowUs($howAndWhenKnowUs)
    {
        $this->howAndWhenKnowUs = $howAndWhenKnowUs;
        return $this;
    }

    /**
     * 
     * @param string $whyWorkWithUs
     * @return Self
     */
    public function setWhyWorkWithUs($whyWorkWithUs)
    {
        $this->whyWorkWithUs = $whyWorkWithUs;
        return $this;
    }

    /**
     * 
     * @param string $volunteerWithUs
     * @return Self
     */
    public function setVolunteerWithUs($volunteerWithUs)
    {
        $this->volunteerWithUs = $volunteerWithUs;
        return $this;
    }

    /**
     * 
     * @return integer
     */
    public function getResponsibility()
    {
        return $this->responsibility;
    }

    /**
     * 
     * @return integer
     */
    public function getProactive()
    {
        return $this->proactive;
    }

    /**
     * 
     * @return integer
     */
    public function getVolunteerSpirit()
    {
        return $this->volunteerSpirit;
    }

    /**
     * 
     * @return integer
     */
    public function getCommitment()
    {
        return $this->commitment;
    }

    /**
     * 
     * @return integer
     */
    public function getTeamWork()
    {
        return $this->teamWork;
    }

    /**
     * 
     * @return integer
     */
    public function getEfficiency()
    {
        return $this->efficiency;
    }

    /**
     * 
     * @return integer
     */
    public function getCourtesy()
    {
        return $this->courtesy;
    }

    /**
     * 
     * @param integer $responsibility
     * @return Self
     */
    public function setResponsibility($responsibility)
    {
        $this->responsibility = $responsibility;
        return $this;
    }

    /**
     * 
     * @param integer $proactive
     * @return Self
     */
    public function setProactive($proactive)
    {
        $this->proactive = $proactive;
        return $this;
    }

    /**
     * 
     * @param integer $volunteerSpirit
     * @return Self
     */
    public function setVolunteerSpirit($volunteerSpirit)
    {
        $this->volunteerSpirit = $volunteerSpirit;
        return $this;
    }

    /**
     * 
     * @param integer $commitment
     * @return Self
     */
    public function setCommitment($commitment)
    {
        $this->commitment = $commitment;
        return $this;
    }

    /**
     * 
     * @param integer $teamWork
     * @return Self
     */
    public function setTeamWork($teamWork)
    {
        $this->teamWork = $teamWork;
        return $this;
    }

    /**
     * 
     * @param integer $efficiency
     * @return Self
     */
    public function setEfficiency($efficiency)
    {
        $this->efficiency = $efficiency;
        return $this;
    }

    /**
     * 
     * @param integer $courtesy
     * @return Self
     */
    public function setCourtesy($courtesy)
    {
        $this->courtesy = $courtesy;
        return $this;
    }

    /**
     * 
     * @return Collection
     */
    public function getRegistrationStatus()
    {
        return $this->registrationStatus;
    }

    /**
     * 
     * @param RegistrationStatus $regStatus
     * @return Self
     */
    public function addRegistrationStatus(RegistrationStatus $regStatus)
    {
        if (!$this->hasRegistrationStatus($regStatus)) {
            $regStatus->setRegistration($this);
            $this->registrationStatus->add($regStatus);
        }
        return $this;
    }

    /**
     * 
     * @param RegistrationStatus $regStatus
     * @return bool
     */
    public function hasRegistrationStatus(RegistrationStatus $regStatus)
    {
        return $this->registrationStatus->contains($regStatus);
    }

    /**
     * @param RegistrationStatus $regStatus
     * @return Self
     */
    public function removeRegistrationStatus(RegistrationStatus $regStatus)
    {
        $this->registrationStatus->removeElement($regStatus);
        return $this;
    }

    /**
     * 
     * @return RegistrationStatus
     */
    public function getCurrentRegistrationStatus()
    {
        $criteria = Criteria::create()
            ->where(Criteria::expr()->eq("isCurrent", true))
            ->setMaxResults(1);

        $result = $this->registrationStatus->matching($criteria);

        return $result->toArray()[0];
    }

    /**
     * 
     * @return Recruitment\Entity\VolunteerInterview
     */
    public function getVolunteerInterview()
    {
        return $this->volunteerInterview;
    }

    /**
     * 
     * @param Recruitment\Entity\VolunteerInterview $volunteerInterview
     * @return Recruitment\Entity\Registration
     */
    public function setVolunteerInterview(VolunteerInterview $volunteerInterview)
    {
        $this->volunteerInterview = $volunteerInterview;
        return $this;
    }

    /**
     * 
     * @return StudentInterview
     */
    public function getStudentInterview()
    {
        return $this->studentInterview;
    }

    /**
     * 
     * @param Recruitment\Entity\StudentInterview $studentInterview
     * @return Recruitment\Entity\Registration
     */
    public function setStudentInterview(StudentInterview $studentInterview)
    {
        $studentInterview->setRegistration($this);
        $this->studentInterview = $studentInterview;
        return $this;
    }
}
