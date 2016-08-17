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
        if (!in_array($recruitmentType,
                array(self::STUDENT_RECRUITMENT_TYPE,
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

}
