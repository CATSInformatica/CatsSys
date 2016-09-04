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
 * Description of ApplicationResult
 *
 * @ORM\Table(name="exam_application_result")
 * @ORM\Entity
 * @author Márcio Dias <marciojr91@gmail.com>
 */
class ApplicationResult
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
     * @var Enrollment
     * @ORM\ManyToOne(targetEntity="Enrollment")
     * @ORM\JoinColumn(name="enrollment_id", referencedColumnName="enrollment_id", nullable=true)
     */
    private $resultEnrollment;

    /**
     *
     * @var Recruitment\Entity\Registration
     * @ORM\ManyToOne(targetEntity="Recruitment\Entity\Registration")
     * @ORM\JoinColumn(name="registration_id", referencedColumnName="registration_id", nullable=true)
     */
    private $resultRegistration;

    /**
     *
     * @var string
     * @ORM\Column(name="exam_application_result_answers", type="string", length=1000, nullable=false)
     */
    private $answers;

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

    public function getResultEnrollment()
    {
        return $this->resultEnrollment;
    }

    public function getResultRegistration()
    {
        return $this->resultRegistration;
    }

    public function getAnswers()
    {
        return $this->answers;
    }

    public function getApplication()
    {
        return $this->application;
    }

    public function setResultEnrollment($resultEnrollment)
    {
        $this->resultEnrollment = $resultEnrollment;
        return $this;
    }

    public function setResultRegistration($resultRegistration)
    {
        $this->resultRegistration = $resultRegistration;
        return $this;
    }

    public function setAnswers($answers)
    {
        $this->answers = $answers;
        return $this;
    }

    public function setApplication($application)
    {
        $this->application = $application;
        return $this;
    }
}
