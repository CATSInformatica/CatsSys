<?php

/*
 * Copyright (C) 2017 Gabriel Pereira <rickardch@gmail.com>
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
use Recruitment\Entity\VolunteerInterview;

/**
 * Avaliação feita pelos entrevistadores de um candidato do processo seletivo de 
 * voluntários.
 * 
 * @author Gabriel Pereira <rickardch@gmail.com>
 * @ORM\Table(name="interviewer_evaluation", uniqueConstraints={@ORM\UniqueConstraint(name="evaluation_unique", columns={"interviewer_name", "volunteer_interview"})})
 * @ORM\Entity
 */
class InterviewerEvaluation
{

    const RATING_MAX = 10;
    const RATING_MIN = 0;
    const RATING_STEP = 1;
   
    /**
     *
     * @var integer
     * @ORM\Column(name="interviewer_evaluation_id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY");
     */
    private $interviewerEvaluationId;
    
    /**
     *
     * @var string
     * @ORM\Column(name="interviewer_name", type="string", length=100, nullable=false)
     */
    private $interviewerName;
    
    /**
     * 
     * @var integer
     * @ORM\Column(name="volunteer_profile_rating", type="smallint", nullable=false)
     */
    private $volunteerProfileRating;
    
    /**
     * @var string
     * @ORM\Column(name="volunteer_profile", type="string", length=500, nullable=false)
     */
    private $volunteerProfile;
    
    /**
     * 
     * @var integer
     * @ORM\Column(name="volunteer_availability_rating", type="smallint", nullable=false)
     */
    private $volunteerAvailabilityRating;
    
    /**
     * @var string
     * @ORM\Column(name="volunteer_availability", type="string", length=500, nullable=false)
     */
    private $volunteerAvailability;
    
    /**
     * 
     * @var integer
     * @ORM\Column(name="volunteer_responsability_commitment_rating", type="smallint", nullable=false)
     */
    private $volunteerResponsabilityAndCommitmentRating;
    
    /**
     * @var string
     * @ORM\Column(name="volunteer_responsability_commitment", type="string", length=500, nullable=false)
     */
    private $volunteerResponsabilityAndCommitment;
    
    /**
     * 
     * @var integer
     * @ORM\Column(name="volunteer_overall_rating", type="smallint", nullable=false)
     */    
    private $volunteerOverallRating;
    
    /**
     * 
     * @var string
     * @ORM\Column(name="volunteer_overall_remarks", type="string", length=500, nullable=false)
     */    
    private $volunteerOverallRemarks;

    /**
     * 
     * @ORM\ManyToOne(targetEntity="VolunteerInterview", inversedBy="interviewersEvaluations", cascade={"persist", "merge"})
     * @ORM\JoinColumn(name="volunteer_interview", referencedColumnName="volunteer_interview_id")
     */
    private $volunteerInterview;
    
    
    /**
     * 
     * @return integer
     */
    public function getInterviewerEvaluationId()
    {
        return $this->interviewerEvaluationId;
    }
    
    /**
     * 
     * @return string
     */
    public function getInterviewerName()
    {
        return $this->interviewerName;
    }

    /**
     * 
     * @return integer
     */
    public function getVolunteerProfileRating()
    {
        return $this->volunteerProfileRating;
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
     * @return integer
     */
    public function getVolunteerAvailabilityRating()
    {
        return $this->volunteerAvailabilityRating;
    }

    /**
     * 
     * @return string
     */
    public function getVolunteerAvailability()
    {
        return $this->volunteerAvailability;
    }

    /**
     * 
     * @return integer
     */
    public function getVolunteerResponsabilityAndCommitmentRating()
    {
        return $this->volunteerResponsabilityAndCommitmentRating;
    }

    /**
     * 
     * @return string
     */
    public function getVolunteerResponsabilityAndCommitment()
    {
        return $this->volunteerResponsabilityAndCommitment;
    }

    /**
     * 
     * @return integer
     */
    public function getVolunteerOverallRating()
    {
        return $this->volunteerOverallRating;
    }

    /**
     * 
     * @return string
     */
    public function getVolunteerOverallRemarks()
    {
        return $this->volunteerOverallRemarks;
    }

    /**
     * 
     * @return VolunteerInterview
     */
    public function getVolunteerInterview()
    {
        return $this->volunteerInterview;
    }

    /**
     * 
     * @param string $interviewerName
     * @return \Recruitment\Entity\InterviewerEvaluation
     */
    public function setInterviewerName($interviewerName)
    {
        $this->interviewerName = $interviewerName;
        return $this;
    }

    /**
     * 
     * @param integer $volunteerProfileRating
     * @return \Recruitment\Entity\InterviewerEvaluation
     */
    public function setVolunteerProfileRating($volunteerProfileRating)
    {
        $this->volunteerProfileRating = $volunteerProfileRating;
        return $this;
    }

    /**
     * 
     * @param string $volunteerProfile
     * @return \Recruitment\Entity\InterviewerEvaluation
     */
    public function setVolunteerProfile($volunteerProfile)
    {
        $this->volunteerProfile = $volunteerProfile;
        return $this;
    }

    /**
     * 
     * @param integer $volunteerAvailabilityRating
     * @return \Recruitment\Entity\InterviewerEvaluation
     */
    public function setVolunteerAvailabilityRating($volunteerAvailabilityRating)
    {
        $this->volunteerAvailabilityRating = $volunteerAvailabilityRating;
        return $this;
    }

    /**
     * 
     * @param string $volunteerAvailability
     * @return \Recruitment\Entity\InterviewerEvaluation
     */
    public function setVolunteerAvailability($volunteerAvailability)
    {
        $this->volunteerAvailability = $volunteerAvailability;
        return $this;
    }

    /**
     * 
     * @param integer $volunteerResponsabilityAndCommitmentRating
     * @return \Recruitment\Entity\InterviewerEvaluation
     */
    public function setVolunteerResponsabilityAndCommitmentRating($volunteerResponsabilityAndCommitmentRating)
    {
        $this->volunteerResponsabilityAndCommitmentRating = $volunteerResponsabilityAndCommitmentRating;
        return $this;
    }

    /**
     * 
     * @param string $volunteerResponsabilityAndCommitment
     * @return \Recruitment\Entity\InterviewerEvaluation
     */
    public function setVolunteerResponsabilityAndCommitment($volunteerResponsabilityAndCommitment)
    {
        $this->volunteerResponsabilityAndCommitment = $volunteerResponsabilityAndCommitment;
        return $this;
    }

    /**
     * 
     * @param integer $volunteerOverallRating
     * @return \Recruitment\Entity\InterviewerEvaluation
     */
    public function setVolunteerOverallRating($volunteerOverallRating)
    {
        $this->volunteerOverallRating = $volunteerOverallRating;
        return $this;
    }

    /**
     * 
     * @param string $volunteerOverallRemarks
     * @return \Recruitment\Entity\InterviewerEvaluation
     */
    public function setVolunteerOverallRemarks($volunteerOverallRemarks)
    {
        $this->volunteerOverallRemarks = $volunteerOverallRemarks;
        return $this;
    }

    /**
     * 
     * @param VolunteerInterview $volunteerInterview
     * @return \Recruitment\Entity\InterviewerEvaluation
     */
    public function setVolunteerInterview($volunteerInterview)
    {
        $this->volunteerInterview = $volunteerInterview;
        return $this;
    }
    
    /**
     * 
     * @return integer
     */
    public function getVolunteerFinalRating() {
        return (
                $this->volunteerProfileRating + 
                $this->volunteerAvailabilityRating + 
                $this->volunteerResponsabilityAndCommitmentRating + 
                $this->volunteerOverallRating * 2
                ) / 5;
    }

}
