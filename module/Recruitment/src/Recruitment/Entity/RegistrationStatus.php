<?php

namespace Recruitment\Entity;

use DateTime;
use Doctrine\ORM\Mapping as ORM;

/**
 * Description of RegistrationStatus
 *
 * @author Márcio Dias <marciojr91@gmail.com>
 * @ORM\Table(name="registration_status")
 * @ORM\Entity
 */
class RegistrationStatus
{

    /**
     * @var integer
     * @ORM\Column(name="registration_status_id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $registrationStatusId;

    /**
     * @var Registration
     * @ORM\ManyToOne(targetEntity="Registration", inversedBy="registrationStatus")
     * @ORM\JoinColumn(name="registration_id", referencedColumnName="registration_id", nullable=false)
     */
    private $registration;

    /**
     * @var RecruitmentStatus
     * @ORM\ManyToOne(targetEntity="RecruitmentStatus", inversedBy="regStatus")
     * @ORM\JoinColumn(name="recruitment_status_id", referencedColumnName="recruitment_status_id", nullable=false)
     */
    private $recruitmentStatus;

    /**
     * @var DateTime
     * @ORM\Column(name="recruitment_status_datetime", type="datetime", nullable=false)
     */
    private $timestamp;

    /**
     * @var bool
     * @ORM\Column(name="registration_status_iscurrent", type="boolean", nullable=false)
     */
    private $isCurrent = true;

    /**
     * @param Registration $reg
     * @return Recruitment\Entity\RegistrationStatus
     */
    public function setRegistration(Registration $reg)
    {
        $this->registration = $reg;
        return $this;
    }

    /**
     * @return RecruitmentStatus
     */
    public function getRecruitmentStatus()
    {
        return $this->recruitmentStatus;
    }

    /**
     * 
     * @param Recruitment\Entity\RecruitmentStatus $recStatus
     * @return Recruitment\Entity\RegistrationStatus
     */
    public function setRecruitmentStatus(RecruitmentStatus $recStatus)
    {
        $recStatus->addRegStatus($this);
        $this->recruitmentStatus = $recStatus;
        return $this;
    }

    /**
     * 
     * @return string
     */
    public function getTimestamp($format = 'd/m/Y H:i:s')
    {
        return $this->timestamp->format($format);
    }

    /**
     * 
     * @param \DateTime $ts
     * @return Recruitment\Entity\RegistrationStatus
     */
    public function setTimestamp(\DateTime $ts)
    {
        $this->timestamp = $ts;
        return $this;
    }

    /**
     * 
     * @param bool $isCurrent
     * @return Recruitment\Entity\RegistrationStatus
     */
    public function setIsCurrent($isCurrent)
    {
        $this->isCurrent = $isCurrent;
        return $this;
    }

}
