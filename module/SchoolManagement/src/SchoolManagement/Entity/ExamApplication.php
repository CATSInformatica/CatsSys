<?php

/*
 * Copyright (C) 2016 Gabriel Pereira <rickardch@gmail.com>
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
use Doctrine\Common\Collections\ArrayCollection;
use SchoolManagement\Entity\Exam;


/**
 * Description of ExamApplication
 *
 * @author Gabriel Pereira <rickardch@gmail.com>
 * @ORM\Table(name="exam_application")
 * @ORM\Entity
 */
class ExamApplication
{
    
    /**
     *
     * @var integer 
     * @ORM\Column(name="exam_application_id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $examApplicationId;
    
    /**
     *
     * @var string 
     * @ORM\Column(name="exam_application_name", type="string", length=70, unique=true, nullable=false)
     */
    private $name;
    
    /**
     *
     * @var Exam
     * @ORM\OneToMany(targetEntity="Exam", mappedBy="application")
     */
    private $exams;
    
    const EXAM_APP_CREATED = 'Criado';
    const EXAM_APP_APPLIED = 'Aplicado';
    
    const AVAILABLE_STATUS = [
        self::EXAM_APP_CREATED,
        self::EXAM_APP_APPLIED,
    ];
    
    /**
     *
     * @var string
     * @ORM\Column(name="exam_application_status", type="string", length=50, nullable=false)
     */
    private $status;
    
    public function __construct()
    {
        $this->exams = new ArrayCollection();
        $this->status = self::AVAILABLE_STATUS[0];
    }
    
    /**
     * @return integer
     */
    public function getExamApplicationId()
    {
        return $this->examApplicationId;
    }
    
    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return ArrayCollection
     */
    public function getExams()
    {
        return $this->exams;
    }

    /**
     * @param string $name
     * @return ExamApplication
     */
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @param ArrayCollection $exams - Collection de objetos Exam
     * @return ExamApplication
     */
    public function setExams(ArrayCollection $exams)
    {
        $this->exams = $exams;
        return $this;
    }

    /**
     *
     * @param Exam $exam
     * @return ExamApplication
     */
    public function addExam($exam)
    {
        if (!$this->hasChild($exam)) {
            $this->exams->add($exam);
            $exam->setApplication($this);
        }
        return $this;
    }

    /**
     * 
     * @param Exam $exam
     * @return ExamApplication
     */
    public function removeExam($exam)
    {
        $this->exams->removeElement($exam);
        $exam->setApplication(null);
        return $this;
    }

    /**
     * 
     * @return ExamApplication
     */
    public function removeAllExams()
    {
        foreach ($this->exams as $exam) {
            $exam->setApplication(null);            
        }
        $this->exams = new ArrayCollection();
        return $this;
    }

    /**
     * 
     * @param Exam $exam
     * @return boolean
     */
    public function hasChild($exam)
    {
        return $this->exams->contains($exam);
    }
    
    /**
     * 
     * @return string Status da aplicação
     */
    public function getStatus()
    {
        return $this->status;
    }
    
    /**
     * 
     * @param string $status Define o novo status da aplicação
     * @return \SchoolManagement\Entity\ExamApplication
     */
    public function setStatus($status)
    {
        $this->status = $status;
        return $this;
    }

}
