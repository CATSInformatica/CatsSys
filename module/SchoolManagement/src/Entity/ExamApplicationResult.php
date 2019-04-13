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

namespace SchoolManagement\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 *
 * @ORM\Table(name="exam_application_result",
 *      uniqueConstraints={
 *          @ORM\UniqueConstraint(name="registration_application_unique", columns={"registration_id", "exam_application_id"}),
 *          @ORM\UniqueConstraint(name="enrollment_application_unique", columns={"enrollment_id", "exam_application_id"})
 *      }
 * )
 * @ORM\Entity(repositoryClass="SchoolManagement\Entity\Repository\ExamApplicationResultRepository")
 * @author Márcio Dias <marciojr91@gmail.com>
 */
class ExamApplicationResult
{

    /**
     *
     * @var int
     * @ORM\Column(name="exam_application_result_id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $resultId;

    /**
     *
     * @var Recruitment\Entity\Registration
     * @ORM\ManyToOne(targetEntity="Recruitment\Entity\Registration")
     * @ORM\JoinColumn(name="registration_id", referencedColumnName="registration_id", nullable=true)
     */
    private $registration;

    /**
     *
     * @var SchoolManagement\Entity\Enrollment
     * @ORM\ManyToOne(targetEntity="SchoolManagement\Entity\Enrollment")
     * @ORM\JoinColumn(name="enrollment_id", referencedColumnName="enrollment_id", nullable=true)
     */
    private $enrollment;

    /**
     *
     * @var string
     * @ORM\Column(name="exam_application_result_result", type="string", length=2000, nullable=false)
     */
    private $result;

    /**
     *
     * @var ExamApplication
     * @ORM\ManyToOne(targetEntity="ExamApplication")
     * @ORM\JoinColumn(name="exam_application_id", referencedColumnName="exam_application_id", nullable=false)
     */
    private $application;

    public function __construct()
    {

    }

    public function getResultId()
    {
        return $this->resultId;
    }

    public function getRegistration()
    {
        return $this->registration;
    }

    public function getEnrollment()
    {
        return $this->enrollment;
    }

    public function getResult()
    {
        return $this->result;
    }

    public function getApplication()
    {
        return $this->application;
    }

    public function setRegistration($registration)
    {
        $this->registration = $registration;
        return $this;
    }

    public function setEnrollment($enrollment)
    {
        $this->enrollment = $enrollment;
        return $this;
    }

    public function setResult($result)
    {
        $this->result = $result;
        return $this;
    }

    public function setApplication($aplication)
    {
        $this->application = $aplication;
        return $this;
    }
}
