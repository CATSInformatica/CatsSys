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
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Recruitment\Entity\InterviewerEvaluation;
use Recruitment\Entity\Registration;
use DateTime;

/**
 * ORM da tabela `volunteer_interview`.
 *
 * @author Márcio Dias <marciojr91@gmail.com>
 * @ORM\Table(name="volunteer_interview")
 * @ORM\Entity
 */
class VolunteerInterview
{

    const INTEREST_RATING_MAX = 10;
    const INTEREST_RATING_MIN = 0;
    const INTEREST_RATING_STEP = 1;

    const INTERVIEWER_SEPARATOR = ';';
    
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
     * 
     * @var \DateTime
     * @ORM\Column(name="interview_date", type="datetime", nullable=true)
     */
    private $date;

    /**
     *
     * @var string
     * @ORM\Column(name="interviewers", type="string", length=300, nullable=false)
     */
    private $interviewers;

    /**
     * 
     * @var \DateTime
     * @ORM\Column(name="interview_starttime", type="time", nullable=false)
     */
    private $startTime;

    /**
     * 
     * @var \DateTime
     * @ORM\Column(name="interview_endtime", type="time", nullable=false)
     */
    private $endTime;

    /**
     * @var string
     * @ORM\Column(name="interviewers_initial_comments", type="string", length=500, nullable=false)
     */
    private $interviewersInitialComments;
    
    /**
     * @var string
     * @ORM\Column(name="volunteer_interests", type="string", length=500, nullable=false)
     */
    private $interests;

    /**
     * @var string
     * @ORM\Column(name="volunteer_interpersonal_relationship", type="string", length=500, nullable=false)
     */
    private $interpersonalRelationship;
    
    /**
     * @var string
     * @ORM\Column(name="volunteer_proactivity", type="string", length=500, nullable=false)
     */
    private $proactivity;
    
    /**
     * @var string
     * @ORM\Column(name="volunteer_qualities", type="string", length=500, nullable=false)
     */
    private $qualities;
    
    /**
     * @var string
     * @ORM\Column(name="volunteer_flaws", type="string", length=500, nullable=false)
     */
    private $flaws;
    
    /**
     * @var string
     * @ORM\Column(name="volunteer_potential_issues", type="string", length=500, nullable=false)
     */
    private $potentialIssues;
    
    /**
     * @var string
     * @ORM\Column(name="volunteer_flexibility_responsability", type="string", length=500, nullable=false)
     */
    private $flexibilityAndResponsability;
    
    /**
     * @var string
     * @ORM\Column(name="volunteer_coherence_test", type="string", length=500, nullable=false)
     */
    private $coherenceTest;
    
    /**
     * @var string
     * @ORM\Column(name="volunteer_expected_contribution", type="string", length=500, nullable=false)
     */
    private $expectedContribution;
    
    /**
     * 
     * @var integer
     * @ORM\Column(name="volunteer_interest_rating", type="smallint", nullable=false)
     */    
    private $interestRating;
    
    /**
     *
     * @var Collection
     * @ORM\OneToMany(targetEntity="InterviewerEvaluation", mappedBy="volunteerInterview")
     */
    private $interviewersEvaluations;
    
    /**
     *
     * @var string
     * @ORM\Column(name="volunteer_hometown", type="string", length=40, nullable=true) 
     */
    private $hometown;
    

    public function __construct() {
        $this->date = new DateTime('now');
        $this->interviewersEvaluations = new ArrayCollection();
    }
    
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
     * @return \DateTime
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * 
     * @return string
     */
    public function getInterviewers()
    {
        return $this->interviewers;
    }

    /**
     * 
     * @return \DateTime
     */
    public function getStartTime()
    {
        return $this->startTime;
    }
    
    /**
     * 
     * @return \DateTime
     */
    public function getEndTime()
    {
        return $this->endTime;
    }
    
    /**
     * 
     * @return string
     */
    public function getInterviewersInitialComments()
    {
        return $this->interviewersInitialComments;
    }
    
    /**
     * 
     * @return string
     */
    public function getInterests()
    {
        return $this->interests;
    }

    /**
     * 
     * @return string
     */
    public function getQualities()
    {
        return $this->qualities;
    }

    /**
     * 
     * @return string
     */
    public function getFlaws()
    {
        return $this->flaws;
    }

    /**
     * 
     * @return string
     */
    public function getPotentialIssues()
    {
        return $this->potentialIssues;
    }

    /**
     * 
     * @return string
     */
    public function getFlexibilityAndResponsability()
    {
        return $this->flexibilityAndResponsability;
    }
    
    /**
     * 
     * @return string
     */
    public function getCoherenceTest()
    {
        return $this->coherenceTest;
    }

        /**
     * 
     * @return string
     */
    public function getExpectedContribution()
    {
        return $this->expectedContribution;
    }

    /**
     * Inteiro de 0 a 10
     * 
     * @return integer
     */
    public function getInterestRating()
    {
        return $this->interestRating;
    }

    /**
     * 
     * @return Collection
     */
    public function getInterviewersEvaluations()
    {
        return $this->interviewersEvaluations;
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
     * @param string $interviewersInitialComments
     * @return Recruitment\Entity\VolunteerInterview
     */
    public function setInterviewersInitialComments($interviewersInitialComments)
    {
        $this->interviewersInitialComments = $interviewersInitialComments;
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
     * @param string $interviewers
     * @return Recruitment\Entity\VolunteerInterview
     */
    public function setInterviewers($interviewers)
    {
        $this->interviewers = $interviewers;
        return $this;
    }
    
    /**
     * 
     * @param \DateTime $startTime
     * @return Recruitment\Entity\VolunteerInterview
     */
    public function setStartTime(\DateTime $startTime)
    {
        $this->startTime = $startTime;
        return $this;
    }
    
    /**
     * 
     * @param \DateTime $endTime
     * @return Recruitment\Entity\VolunteerInterview
     */
    public function setEndTime(\DateTime $endTime)
    {
        $this->endTime = $endTime;
        return $this;
    }

    /**
     * 
     * @param string $interests
     * @return Recruitment\Entity\VolunteerInterview
     */
    public function setInterests($interests)
    {
        $this->interests = $interests;
        return $this;
    }

    /**
     * 
     * @param string $qualities
     * @return Recruitment\Entity\VolunteerInterview
     */
    public function setQualities($qualities)
    {
        $this->qualities = $qualities;
        return $this;
    }

    /**
     * 
     * @param string $flaws
     * @return Recruitment\Entity\VolunteerInterview
     */
    public function setFlaws($flaws)
    {
        $this->flaws = $flaws;
        return $this;
    }

    /**
     * 
     * @param string $potentialIssues
     * @return Recruitment\Entity\VolunteerInterview
     */
    public function setPotentialIssues($potentialIssues)
    {
        $this->potentialIssues = $potentialIssues;
        return $this;
    }

    /**
     * 
     * @param string $flexibilityAndResponsability
     * @return Recruitment\Entity\VolunteerInterview
     */
    public function setFlexibilityAndResponsability($flexibilityAndResponsability)
    {
        $this->flexibilityAndResponsability = $flexibilityAndResponsability;
        return $this;
    }

    /**
     * 
     * @param string $coherenceTest
     * @return Recruitment\Entity\VolunteerInterview
     */
    public function setCoherenceTest($coherenceTest)
    {
        $this->coherenceTest = $coherenceTest;
        return $this;
    }

    /**
     * 
     * @param string $expectedContribution
     * @return Recruitment\Entity\VolunteerInterview
     */
    public function setExpectedContribution($expectedContribution)
    {
        $this->expectedContribution = $expectedContribution;
        return $this;
    }

    /**
     * 
     * @param integer $interestRating
     * @return Recruitment\Entity\VolunteerInterview
     */
    public function setInterestRating($interestRating)
    {
        $this->interestRating = $interestRating;
        return $this;
    }

    /**
     * 
     * @param Collection $interviewersEvaluations
     * @return Recruitment\Entity\VolunteerInterview
     */
    public function setInterviewersEvaluations(Collection $interviewersEvaluations)
    {
        $this->interviewersEvaluations = $interviewersEvaluations;
        return $this;
    }

    /**
     * @param InterviewerEvaluation $interviewerEvaluation
     * @return Recruitment\Entity\VolunteerInterview
     */
    public function addInterviewerEvaluation(InterviewerEvaluation $interviewerEvaluation)
    {
        if (!$this->hasInterviewerEvaluation($interviewerEvaluation)) {
            $interviewerEvaluation->setVolunteerInterview($this);
            $this->interviewersEvaluations->add($interviewerEvaluation);
        }

        return $this;
    }

    /**
     *
     * @param Collection $interviewersEvaluations
     * @return Recruitment\Entity\VolunteerInterview
     */
    public function addInterviewersEvaluations(Collection $interviewersEvaluations)
    {
        foreach ($interviewersEvaluations as $interviewerEvaluation) {
            if (!$this->hasInterviewerEvaluation($interviewerEvaluation)) {
                $interviewerEvaluation->setVolunteerInterview($this);
                $this->interviewersEvaluations->add($interviewerEvaluation);
            }
        }
        return $this;
    }

    /**
     * @param InterviewerEvaluation $interviewerEvaluation
     * @return Recruitment\Entity\VolunteerInterview
     */
    public function removeInterviewerEvaluation(InterviewerEvaluation $interviewerEvaluation)
    {
        $interviewerEvaluation->setVolunteerInterview(null);
        $this->interviewersEvaluations->removeElement($interviewerEvaluation);
        return $this;
    }

    /**
     *
     * @param Collection $interviewersEvaluations
     * @return Recruitment\Entity\VolunteerInterview
     */
    public function removeInterviewersEvaluations(Collection $interviewersEvaluations)
    {
        foreach ($interviewersEvaluations as $interviewerEvaluation) {
            $interviewerEvaluation->setVolunteerInterview(null);
            $this->interviewersEvaluations->removeElement($interviewerEvaluation);
        }
        return $this;
    }

    /**
     * 
     * @param InterviewerEvaluation $interviewerEvaluation
     * @return boolean
     */
    public function hasInterviewerEvaluation(InterviewerEvaluation $interviewerEvaluation)
    {
        return $this->interviewersEvaluations->contains($interviewerEvaluation);
    }
    
    /**
     * 
     * @return string
     */
    public function getHometown()
    {
        return $this->hometown;
    }

    /**
     * 
     * @param string $hometown
     * @return Recruitment\Entity\VolunteerInterview
     */
    public function setHometown($hometown)
    {
        $this->hometown = $hometown;
        return $this;
    }


    
}
