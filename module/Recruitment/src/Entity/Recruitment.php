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
use Recruitment\Entity\Registration;
use AdministrativeStructure\Entity\Job;

/**
 * ORM da tabela `recruitment`.
 *
 * @author Márcio Dias <marciojr91@gmail.com>
 * @ORM\Table(name="recruitment", uniqueConstraints={
 * @ORM\UniqueConstraint(name="recruitment_nyeart_idx", columns={"recruitment_number", "recruitment_year", "recruitment_type"})
 * })
 * @ORM\Entity(repositoryClass="Recruitment\Entity\Repository\RecruitmentRepository")
 */
class Recruitment
{

    /**
     * Indica pesquisa por todos os processos seletivos de voluntário
     */
    const ALL_VOLUNTEER_RECRUITMENTS = -1;

    /**
     * 1: Processo seletivo de Alunos
     * 2: Processo seletivo de Voluntários
     */
    const STUDENT_RECRUITMENT_TYPE = 1;
    const VOLUNTEER_RECRUITMENT_TYPE = 2;

    /**
     *
     * @var integer
     * @ORM\Column(name="recruitment_id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $recruitmentId;

    /**
     *
     * @var integer
     * @ORM\Column(name="recruitment_number", type="smallint", nullable=false)
     */
    private $recruitmentNumber;

    /**
     *
     * @var integer
     * @ORM\Column(name="recruitment_year", type="smallint", nullable=false)
     */
    private $recruitmentYear;

    /**
     *
     * @var \DateTime
     * @ORM\Column(name="recruitment_begindate", type="datetime", nullable=false)
     */
    private $recruitmentBeginDate;

    /**
     *
     * @var \DateTime
     * @ORM\Column(name="recruitment_enddate", type="datetime", nullable=false)
     */
    private $recruitmentEndDate;

    /**
     * @var string
     * @ORM\Column(name="recruitment_public_notice", type="string", length=200, nullable=false)
     */
    private $recruitmentPublicNotice;

    /**
     *
     * @var integer
     * @ORM\Column(name="recruitment_type", type="smallint", nullable=false)
     */
    private $recruitmentType;

    /**
     *
     * @var float
     * @ORM\Column(name="recruitment_socioeconomic_target", type="float", nullable=true)
     */
    private $recruitmentSocioeconomicTarget;

    /**
     *
     * @var float
     * @ORM\Column(name="recruitment_vulnerability_target", type="float", nullable=true)
     */
    private $recruitmentVulnerabilityTarget;

    /**
     *
     * @var float
     * @ORM\Column(name="recruitment_student_target", type="float", nullable=true)
     */
    private $recruitmentStudentTarget;

    // new

    /**
     *
     * @var string
     * @ORM\Column(name="recruitment_subdesc", type="string", length=800, nullable=true)
     */
    private $subscriptionDescription;

    /**
     *
     * @var \DateTime
     * @ORM\Column(name="recruitment_consdate", type="datetime", nullable=true)
     */
    private $confirmationStartDate;

    /**
     *
     * @var \DateTime
     * @ORM\Column(name="recruitment_conedate", type="datetime", nullable=true)
     */
    private $confirmationEndDate;

    /**
     *
     * @var string
     * @ORM\Column(name="recruitment_condesc", type="string", length=800, nullable=true)
     */
    private $confirmationDescription;

    /**
     *
     * @var \DateTime
     * @ORM\Column(name="recruitment_exadate", type="datetime", nullable=true)
     */
    private $examDate;

    /**
     *
     * @var string
     * @ORM\Column(name="recruitment_exadesc", type="string", length=800, nullable=true)
     */
    private $examDescription;

    /**
     *
     * @var \DateTime
     * @ORM\Column(name="recruitment_exrdate", type="datetime", nullable=true)
     */
    private $examResultDate;

    /**
     *
     * @var string
     * @ORM\Column(name="recruitment_exrdesc", type="string", length=800, nullable=true)
     */
    private $examResultDescription;

    /**
     *
     * @var \DateTime
     * @ORM\Column(name="recruitment_presdate", type="datetime", nullable=true)
     */
    private $preInterviewStartDate;

    /**
     *
     * @var string
     * @ORM\Column(name="recruitment_predesc", type="string", length=800, nullable=true)
     */
    private $preInterviewDescription;

    /**
     *
     * @var \DateTime
     * @ORM\Column(name="recruitment_intsdate", type="datetime", nullable=true)
     */
    private $interviewStartDate;

    /**
     *
     * @var \DateTime
     * @ORM\Column(name="recruitment_intedate", type="datetime", nullable=true)
     */
    private $interviewEndDate;

    /**
     *
     * @var string
     * @ORM\Column(name="recruitment_intdesc", type="string", length=800, nullable=true)
     */
    private $interviewDescription;

    /**
     *
     * @var \DateTime
     * @ORM\Column(name="recruitment_resdate", type="datetime", nullable=true)
     */
    private $resultDate;

    /**
     *
     * @var string
     * @ORM\Column(name="recruitment_resdesc", type="string", length=800, nullable=true)
     */
    private $resultDescription;

    /**
     *
     * @var \DateTime
     * @ORM\Column(name="recruitment_enrsdate", type="datetime", nullable=true)
     */
    private $enrollmentStartDate;

    /**
     *
     * @var \DateTime
     * @ORM\Column(name="recruitment_enredate", type="datetime", nullable=true)
     */
    private $enrollmentEndDate;

    /**
     *
     * @var string
     * @ORM\Column(name="recruitment_enrdesc", type="string", length=800, nullable=true)
     */
    private $enrollmentDescription;

    /**
     *
     * @var string
     * @ORM\Column(name="recruitment_tstcdesc", type="string", length=800, nullable=true)
     */
    private $testClassDescription;

    // /new

    /**
     *
     * @var Collection
     * @ORM\OneToMany(targetEntity="\Recruitment\Entity\Registration", mappedBy="recruitment")
     */
    private $registrations;

    /**
     *
     * @var Collection
     * @ORM\ManyToMany(targetEntity="\AdministrativeStructure\Entity\Job")
     * @ORM\JoinTable(name="recruitment_open_jobs",
     *      joinColumns={@ORM\JoinColumn(name="recruitment_id", referencedColumnName="recruitment_id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="job_id", referencedColumnName="job_id")})
     */
    private $openJobs;

    public function __construct()
    {
        $this->registrations = new ArrayCollection();
        $this->openJobs = new ArrayCollection();
    }

    /**
     *
     * @return integer
     */
    public function getRecruitmentId()
    {
        return $this->recruitmentId;
    }

    /**
     *
     * @return integer
     */
    public function getRecruitmentNumber()
    {
        return $this->recruitmentNumber;
    }

    /**
     *
     * @return integer
     */
    public function getRecruitmentYear()
    {
        return $this->recruitmentYear;
    }

    /**
     * Retorna a identificaçao do processo seletivo no formato Y - Numero
     *
     * @return string
     */
    public function getRecruitmentYn()
    {
        return self::formatName($this->recruitmentYear, $this->recruitmentNumber);
    }

    public static function formatName($year, $number)
    {
        return $year . ' - ' . $number;
    }

    /**
     *
     * @param integer $recruitmentNumber
     * @return \Recruitment\Entity\Recruitment
     */
    public function setRecruitmentNumber($recruitmentNumber)
    {
        $this->recruitmentNumber = $recruitmentNumber;
        return $this;
    }

    /**
     *
     * @param integer $recruitmentYear
     * @return \Recruitment\Entity\Recruitment
     */
    public function setRecruitmentYear($recruitmentYear)
    {
        $this->recruitmentYear = $recruitmentYear;
        return $this;
    }

    public function getRecruitmentBeginDate($fmt = 'd/m/Y')
    {
        if ($this->recruitmentBeginDate instanceof \DateTime) {
            return $this->recruitmentBeginDate->format($fmt);
        }
        return null;
    }

    public function getRecruitmentEndDate($fmt = 'd/m/Y')
    {
        if ($this->recruitmentEndDate instanceof \DateTime) {
            return $this->recruitmentEndDate->format($fmt);
        }
        return null;
    }

    /**
     *
     * @return string
     */
    public function getRecruitmentPublicNotice()
    {
        return $this->recruitmentPublicNotice;
    }

    /**
     *
     * @return integer
     */
    public function getRecruitmentType()
    {
        return $this->recruitmentType;
    }

    /**
     *
     * @return float
     */
    public function getRecruitmentSocioeconomicTarget()
    {
        return $this->recruitmentSocioeconomicTarget;
    }

    /**
     *
     * @return float
     */
    public function getRecruitmentVulnerabilityTarget()
    {
        return $this->recruitmentVulnerabilityTarget;
    }

    /**
     *
     * @return float
     */
    public function getRecruitmentStudentTarget()
    {
        return $this->recruitmentStudentTarget;
    }

    /**
     *
     * @param \DateTime $recruitmentBeginDate
     * @return \Recruitment\Entity\Recruitment
     */
    public function setRecruitmentBeginDate(\DateTime $recruitmentBeginDate)
    {
        $this->recruitmentBeginDate = $recruitmentBeginDate;
        return $this;
    }

    /**
     *
     * @param \DateTime $recruitmentEndDate
     * @return \Recruitment\Entity\Recruitment
     */
    public function setRecruitmentEndDate(\DateTime $recruitmentEndDate)
    {
        $recruitmentEndDate->setTime(23, 59, 59);
        $this->recruitmentEndDate = $recruitmentEndDate;
        return $this;
    }

    /**
     *
     * @param string $recruitmentPublicNotice
     * @return \Recruitment\Entity\Recruitment
     */
    public function setRecruitmentPublicNotice($recruitmentPublicNotice)
    {
        $this->recruitmentPublicNotice = $recruitmentPublicNotice;
        return $this;
    }

    /**
     *
     * @param integer $recruitmentType
     * @return \Recruitment\Entity\Recruitment
     * @throws \InvalidArgumentException
     */
    public function setRecruitmentType($recruitmentType)
    {
        if (!in_array($recruitmentType, array(self::STUDENT_RECRUITMENT_TYPE,
                self::VOLUNTEER_RECRUITMENT_TYPE))) {
            throw new \InvalidArgumentException("Invalid recruitment type");
        }

        $this->recruitmentType = $recruitmentType;
        return $this;
    }

    /**
     *
     * @param float $recruitmentSocioeconomicTarget
     * @return \Recruitment\Entity\Recruitment
     */
    public function setRecruitmentSocioeconomicTarget($recruitmentSocioeconomicTarget)
    {
        $this->recruitmentSocioeconomicTarget = $recruitmentSocioeconomicTarget;
        return $this;
    }

    /**
     *
     * @param float $recruitmentVulnerabilityTarget
     * @return \Recruitment\Entity\Recruitment
     */
    public function setRecruitmentVulnerabilityTarget($recruitmentVulnerabilityTarget)
    {
        $this->recruitmentVulnerabilityTarget = $recruitmentVulnerabilityTarget;
        return $this;
    }

    /**
     *
     * @param float $recruitmentStudentTarget
     * @return \Recruitment\Entity\Recruitment
     */
    public function setRecruitmentStudentTarget($recruitmentStudentTarget)
    {
        $this->recruitmentStudentTarget = $recruitmentStudentTarget;
        return $this;
    }

    /**
     *
     * @return array
     */
    public function getRegistrations()
    {
        return $this->registrations->toArray();
    }

    /**
     *
     * @param Collection $registrations
     * @return \Recruitment\Entity\Recruitment
     */
    public function setRegistrations(Collection $registrations)
    {
        $this->registrations = $registrations;
        return $this;
    }

    /**
     *
     * @param \Recruitment\Entity\Registration $registration
     * @return \Recruitment\Entity\Recruitment
     */
    public function addRegistration(Registration $registration)
    {
        $this->registrations[] = $registration;
        return $this;
    }

    /**
     *
     * @param \Recruitment\Entity\Registration $registration
     * @return \Recruitment\Entity\Recruitment
     */
    public function removeRegistration(Registration $registration)
    {
        $this->registrations->removeElement($registration);
        return $this;
    }

    /**
     *
     * @return string
     */
    public function getSubscriptionDescription()
    {
        return $this->subscriptionDescription;
    }

    /**
     *
     * @return \DateTime
     */
    public function getConfirmationStartDate($fmt = 'd/m/Y')
    {

        if ($this->confirmationStartDate instanceof \DateTime) {
            return $this->confirmationStartDate->format($fmt);
        }

        return null;
    }

    public function getConfirmationEndDate($fmt = 'd/m/Y')
    {
        if ($this->confirmationEndDate instanceof \DateTime) {
            return $this->confirmationEndDate->format($fmt);
        }
        return null;
    }

    /**
     *
     * @return string
     */
    public function getConfirmationDescription()
    {
        return $this->confirmationDescription;
    }

    public function getExamDate($fmt = 'd/m/Y')
    {
        if ($this->examDate instanceof \DateTime) {
            return $this->examDate->format($fmt);
        }
        return null;
    }

    /**
     *
     * @return \DateTime|null
     */
    public function getExamDateAsDateTime()
    {
        return $this->examDate;
    }

    /**
     *
     * @return string
     */
    public function getExamDescription()
    {
        return $this->examDescription;
    }

    /**
     *
     * @param string $fmt
     * @return string|null
     */
    public function getExamResultDate($fmt = 'd/m/Y')
    {
        if ($this->examResultDate instanceof \DateTime) {
            return $this->examResultDate->format($fmt);
        }
        return null;
    }

    /**
     *
     * @return \DateTime|null
     */
    public function getExamResultDateAsDateTime()
    {
        return $this->examResultDate;
    }

    /**
     *
     * @return string
     */
    public function getExamResultDescription()
    {
        return $this->examResultDescription;
    }

    public function getPreInterviewStartDate($fmt = 'd/m/Y')
    {
        if ($this->preInterviewStartDate instanceof \DateTime) {
            return $this->preInterviewStartDate->format($fmt);
        }
        return null;
    }

    /**
     *
     * @return \DateTime|null
     */
    public function getPreInterviewStartDateAsDateTime()
    {
        return $this->preInterviewStartDate;
    }

    /**
     *
     * @return string
     */
    public function getPreInterviewDescription()
    {
        return $this->preInterviewDescription;
    }

    /**
     *
     * @return string|null
     */
    public function getInterviewStartDate($fmt = 'd/m/Y')
    {
        if ($this->interviewStartDate instanceof \DateTime) {
            return $this->interviewStartDate->format($fmt);
        }
        return null;
    }

    /**
     *
     * @return \DateTime|null;
     */
    public function getInterviewStartDateAsDateTime()
    {
        return $this->interviewStartDate;
    }

    public function getInterviewEndDate($fmt = 'd/m/Y')
    {
        if ($this->interviewEndDate instanceof \DateTime) {
            return $this->interviewEndDate->format($fmt);
        }
        return null;
    }

    /**
     *
     * @return string
     */
    public function getInterviewDescription()
    {
        return $this->interviewDescription;
    }

    public function getResultDate($fmt = 'd/m/Y')
    {
        if ($this->resultDate instanceof \DateTime) {
            return $this->resultDate->format($fmt);
        }
        return null;
    }

    /**
     *
     * @return \DateTime|null
     */
    public function getResultDateAsDateTime()
    {
        return $this->resultDate;
    }

    /**
     *
     * @return string
     */
    public function getResultDescription()
    {
        return $this->resultDescription;
    }

    public function getEnrollmentStartDate($fmt = 'd/m/Y')
    {
        if ($this->enrollmentStartDate instanceof \DateTime) {
            return $this->enrollmentStartDate->format($fmt);
        }
        return null;
    }

    public function getEnrollmentEndDate($fmt = 'd/m/Y')
    {
        if ($this->enrollmentEndDate instanceof \DateTime) {
            return $this->enrollmentEndDate->format($fmt);
        }
        return null;
    }

    /**
     *
     * @return string
     */
    public function getEnrollmentDescription()
    {
        return $this->enrollmentDescription;
    }

    /**
     *
     * @return Collection
     */
    public function getOpenJobs()
    {
        return $this->openJobs;
    }

    /**
     *
     * @param string $subscriptionDescription
     * @return \Recruitment\Entity\Recruitment
     */
    public function setSubscriptionDescription($subscriptionDescription = null)
    {
        $this->subscriptionDescription = $subscriptionDescription;
        return $this;
    }

    /**
     *
     * @param \DateTime $confirmationStartDate
     * @return \Recruitment\Entity\Recruitment
     */
    public function setConfirmationStartDate(\DateTime $confirmationStartDate = null)
    {
        $this->confirmationStartDate = $confirmationStartDate;
        return $this;
    }

    /**
     *
     * @param \DateTime $confirmationEndDate
     * @return \Recruitment\Entity\Recruitment
     */
    public function setConfirmationEndDate(\DateTime $confirmationEndDate = null)
    {
        $this->confirmationEndDate = $confirmationEndDate;
        return $this;
    }

    /**
     *
     * @param string $confirmationDescription
     * @return \Recruitment\Entity\Recruitment
     */
    public function setConfirmationDescription($confirmationDescription = null)
    {
        $this->confirmationDescription = $confirmationDescription;
        return $this;
    }

    /**
     *
     * @param \DateTime $examDate
     * @return \Recruitment\Entity\Recruitment
     */
    public function setExamDate(\DateTime $examDate = null)
    {
        $this->examDate = $examDate;
        return $this;
    }

    /**
     *
     * @param type $examDescription
     * @return \Recruitment\Entity\Recruitment
     */
    public function setExamDescription($examDescription = null)
    {
        $this->examDescription = $examDescription;
        return $this;
    }

    /**
     *
     * @param \DateTime $examResultDate
     * @return \Recruitment\Entity\Recruitment
     */
    public function setExamResultDate(\DateTime $examResultDate = null)
    {
        $this->examResultDate = $examResultDate;
        return $this;
    }

    /**
     *
     * @param string $examResultDescription
     * @return \Recruitment\Entity\Recruitment
     */
    public function setExamResultDescription($examResultDescription = null)
    {
        $this->examResultDescription = $examResultDescription;
        return $this;
    }

    /**
     *
     * @param \DateTime $preInterviewStartDate
     * @return \Recruitment\Entity\Recruitment
     */
    public function setPreInterviewStartDate(\DateTime $preInterviewStartDate = null)
    {
        $this->preInterviewStartDate = $preInterviewStartDate;
        return $this;
    }

    /**
     *
     * @param string $preInterviewDescription
     * @return \Recruitment\Entity\Recruitment
     */
    public function setPreInterviewDescription($preInterviewDescription = null)
    {
        $this->preInterviewDescription = $preInterviewDescription;
        return $this;
    }

    /**
     *
     * @param \DateTime $interviewStartDate
     * @return \Recruitment\Entity\Recruitment
     */
    public function setInterviewStartDate(\DateTime $interviewStartDate = null)
    {
        $this->interviewStartDate = $interviewStartDate;
        return $this;
    }

    /**
     *
     * @param \DateTime $interviewEndDate
     * @return \Recruitment\Entity\Recruitment
     */
    public function setInterviewEndDate(\DateTime $interviewEndDate = null)
    {
        $this->interviewEndDate = $interviewEndDate;
        return $this;
    }

    /**
     *
     * @param string $interviewDescription
     * @return \Recruitment\Entity\Recruitment
     */
    public function setInterviewDescription($interviewDescription = null)
    {
        $this->interviewDescription = $interviewDescription;
        return $this;
    }

    /**
     *
     * @param \DateTime $resultDate
     * @return \Recruitment\Entity\Recruitment
     */
    public function setResultDate(\DateTime $resultDate = null)
    {
        $this->resultDate = $resultDate;
        return $this;
    }

    /**
     *
     * @param string $resultDescription
     * @return \Recruitment\Entity\Recruitment
     */
    public function setResultDescription($resultDescription = null)
    {
        $this->resultDescription = $resultDescription;
        return $this;
    }

    /**
     *
     * @param \DateTime $enrollmentStartDate
     * @return \Recruitment\Entity\Recruitment
     */
    public function setEnrollmentStartDate(\DateTime $enrollmentStartDate = null)
    {
        $this->enrollmentStartDate = $enrollmentStartDate;
        return $this;
    }

    /**
     *
     * @param \DateTime $enrollmentEndDate
     * @return \Recruitment\Entity\Recruitment
     */
    public function setEnrollmentEndDate(\DateTime $enrollmentEndDate = null)
    {
        $this->enrollmentEndDate = $enrollmentEndDate;
        return $this;
    }

    /**
     *
     * @param string $enrollmentDescription
     * @return \Recruitment\Entity\Recruitment
     */
    public function setEnrollmentDescription($enrollmentDescription = null)
    {
        $this->enrollmentDescription = $enrollmentDescription;
        return $this;
    }

    /**
     * @param Job $job
     * @return Recruitment\Entity\Recruitment
     */
    public function addOpenJob(Job $job)
    {
        if (!$this->hasOpenJob($job)) {
            $this->openJobs->add($job);
        }

        return $this;
    }

    /**
     *
     * @param Collection $jobs
     * @return Recruitment\Entity\Recruitment
     */
    public function addOpenJobs(Collection $jobs)
    {
        foreach ($jobs as $job) {
            $this->addOpenJob($job);
        }

        return $this;
    }

    /**
     * @param Job $job
     * @return Recruitment\Entity\Recruitment
     */
    public function removeOpenJob(Job $job)
    {
        if ($this->hasOpenJob($job)) {
            $this->openJobs->removeElement($job);
        }
        return $this;
    }

    /**
     *
     * @param Collection $jobs
     * @return Recruitment\Entity\Recruitment
     */
    public function removeOpenJobs(Collection $jobs)
    {
        foreach ($jobs as $job) {
            $this->removeOpenJob($job);
        }
        return $this;
    }

    /**
     *
     * @param Job $job
     * @return boolean
     */
    public function hasOpenJob(Job $job)
    {
        return $this->openJobs->contains($job);
    }

    /**
     *
     * @return string
     */
    public function getTestClassDescription()
    {
        return $this->testClassDescription;
    }

    /**
     *
     * @param string $testClassDescription
     * @return Recruitment\Entity\Recruitment
     */
    public function setTestClassDescription($testClassDescription)
    {
        $this->testClassDescription = $testClassDescription;
        return $this;
    }



}
