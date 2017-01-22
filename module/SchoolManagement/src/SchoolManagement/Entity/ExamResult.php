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
 * @ORM\Table(name="exam_result", 
 *      uniqueConstraints={
 *          @ORM\UniqueConstraint(name="registration_exam_unique", columns={"registration_id", "exam_id"})
 *      }
 * )
 * @ORM\Entity
 * @author Márcio Dias <marciojr91@gmail.com>
 */
class ExamResult
{

    /**
     *
     * @var int
     * @ORM\Column(name="exam_result_id", type="integer", nullable=false)
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
     * @var string
     * @ORM\Column(name="exam_application_result_answers", type="string", length=2000, nullable=false)
     */
    private $answers;

    /**
     * 
     * @var Exam
     * @ORM\ManyToOne(targetEntity="Exam")
     * @ORM\JoinColumn(name="exam_id", referencedColumnName="exam_id", nullable=false)
     */
    private $exam;

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

    public function getAnswers()
    {
        return $this->answers;
    }

    public function getExam()
    {
        return $this->exam;
    }

    public function setRegistration($registration)
    {
        $this->registration = $registration;
        return $this;
    }

    public function setAnswers($answers)
    {
        $this->answers = $answers;
        return $this;
    }

    public function setExam($exam)
    {
        $this->exam = $exam;
        return $this;
    }
}
