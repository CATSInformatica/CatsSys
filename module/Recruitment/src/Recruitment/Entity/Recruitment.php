<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Recruitment\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Recruitment\Entity\Registration;

/**
 * Description of Recruitment
 *
 * @author marcio
 * @ORM\Table(name="recruitment", uniqueConstraints={
 * @ORM\UniqueConstraint(name="recruitment_nyeart_idx", columns={"recruitment_number", "recruitment_year", "recruitment_type"})
 * })
 * @ORM\Entity(repositoryClass="Recruitment\Entity\Repository\Recruitment")
 */
class Recruitment
{

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
     * @var Collection
     * @ORM\OneToMany(targetEntity="\Recruitment\Entity\Registration", mappedBy="recruitment")
     */
    private $registrations;

    public function __construct()
    {
        $this->registrations = new ArrayCollection();
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

    /**
     * 
     * @return \DateTime
     */
    public function getRecruitmentBeginDate()
    {
        return $this->recruitmentBeginDate;
    }

    /**
     * 
     * @return \DateTime
     */
    public function getRecruitmentEndDate()
    {
        return $this->recruitmentEndDate;
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

}
