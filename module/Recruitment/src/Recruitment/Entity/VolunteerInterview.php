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

use Doctrine\ORM\Mapping as ORM;
use Recruitment\Entity\Registration;

/**
 * ORM da tabela `volunteer_interview`.
 *
 * @author Márcio Dias <marciojr91@gmail.com>
 * @ORM\Table(name="volunteer_interview")
 * @ORM\Entity
 */
class VolunteerInterview
{

    /**
     *
     * @var integer
     * @ORM\Column(name="volunteer_interview_id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY");
     */
    private $volunteerInterviewId;

    /**
     *
     * @var Registration
     * @ORM\OneToOne(targetEntity="Registration", mappedBy="volunteerInterview")
     */
    private $registration;

    /**
     * @var string
     * @ORM\Column(name="volunteer_proactivity", type="string", length=500, nullable=false)
     */
    private $proactivity;

    /**
     * @var string
     * @ORM\Column(name="volunteer_commitment_efficiency", type="string", length=500, nullable=false)
     */
    private $commitmentAndEfficiency;

    /**
     * @var string
     * @ORM\Column(name="volunteer_profile", type="string", length=500, nullable=false)
     */
    private $volunteerProfile;

    /**
     * @var string
     * @ORM\Column(name="volunteer_interest", type="string", length=500, nullable=false)
     */
    private $interest;

    /**
     * @var string
     * @ORM\Column(name="volunteer_interpersonal_relationship", type="string", length=500, nullable=false)
     */
    private $interpersonalRelationship;

    /**
     * @var string
     * @ORM\Column(name="volunteer_personality", type="string", length=500, nullable=false)
     */
    private $personality;

    /**
     * @var string
     * @ORM\Column(name="volunteer_coherence", type="string", length=500, nullable=false)
     */
    private $coherence;

    /**
     * @var string
     * @ORM\Column(name="volunteer_result", type="string", length=500, nullable=false)
     */
    private $result;

    /**
     * @var string
     * @ORM\Column(name="volunteer_testclass", type="string", length=500, nullable=true)
     */
    private $testClass;

    /**
     * @return string
     */
    public function getVolunteerInterviewId()
    {
        return $this->volunteerInterviewId;
    }

    /**
     * 
     * @return string
     */
    public function getRegistration()
    {
        return $this->registration;
    }

    /**
     * 
     * @return string
     */
    public function getProactivity()
    {
        return $this->proactivity;
    }

    /**
     * 
     * @return string
     */
    public function getCommitmentAndEfficiency()
    {
        return $this->commitmentAndEfficiency;
    }

    /**
     * 
     * @return string
     */
    public function getVolunteerProfile()
    {
        return $this->volunteerProfile;
    }

    /**
     * 
     * @return string
     */
    public function getInterest()
    {
        return $this->interest;
    }

    /**
     * 
     * @return string
     */
    public function getInterpersonalRelationship()
    {
        return $this->interpersonalRelationship;
    }

    /**
     * 
     * @return string
     */
    public function getPersonality()
    {
        return $this->personality;
    }

    /**
     * 
     * @return string
     */
    public function getCoherence()
    {
        return $this->coherence;
    }

    /**
     * 
     * @return string
     */
    public function getResult()
    {
        return $this->result;
    }

    /**
     * 
     * @return string
     */
    public function getTestClass()
    {
        return $this->testClass;
    }

    /**
     * 
     * @param Registration $registration
     * @return Recruitment\Entity\VolunteerInterview
     */
    public function setRegistration(Registration $registration)
    {
        $this->registration = $registration;
        return $this;
    }

    /**
     * 
     * @param string $proactivity
     * @return Recruitment\Entity\VolunteerInterview
     */
    public function setProactivity($proactivity)
    {
        $this->proactivity = $proactivity;
        return $this;
    }

    /**
     * 
     * @param string $commitmentAndEfficiency
     * @return Recruitment\Entity\VolunteerInterview
     */
    public function setCommitmentAndEfficiency($commitmentAndEfficiency)
    {
        $this->commitmentAndEfficiency = $commitmentAndEfficiency;
        return $this;
    }

    /**
     * 
     * @param string $volunteerProfile
     * @return Recruitment\Entity\VolunteerInterview
     */
    public function setVolunteerProfile($volunteerProfile)
    {
        $this->volunteerProfile = $volunteerProfile;
        return $this;
    }

    /**
     * 
     * @param string $interest
     * @return Recruitment\Entity\VolunteerInterview
     */
    public function setInterest($interest)
    {
        $this->interest = $interest;
        return $this;
    }

    /**
     * 
     * @param string $interpersonalRelationship
     * @return Recruitment\Entity\VolunteerInterview
     */
    public function setInterpersonalRelationship($interpersonalRelationship)
    {
        $this->interpersonalRelationship = $interpersonalRelationship;
        return $this;
    }

    /**
     * 
     * @param string $personality
     * @return Recruitment\Entity\VolunteerInterview
     */
    public function setPersonality($personality)
    {
        $this->personality = $personality;
        return $this;
    }

    /**
     * 
     * @param string $coherence
     * @return Recruitment\Entity\VolunteerInterview
     */
    public function setCoherence($coherence)
    {
        $this->coherence = $coherence;
        return $this;
    }

    /**
     * 
     * @param string $result
     * @return Recruitment\Entity\VolunteerInterview
     */
    public function setResult($result)
    {
        $this->result = $result;
        return $this;
    }

    /**
     * 
     * @param string $testClass
     * @return Recruitment\Entity\VolunteerInterview
     */
    public function setTestClass($testClass)
    {
        $this->testClass = $testClass;
        return $this;
    }
}
