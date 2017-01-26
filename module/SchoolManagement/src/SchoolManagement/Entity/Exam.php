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

/**
 * Entidade de configuração de simulados
 *
 * @author Gabriel Pereira <rickardch@gmail.com>
 * @ORM\Table(name="exam")
 * @ORM\Entity
 */
class Exam
{
    
    /**
     * 
     * @var integer 
     * @ORM\Column(name="exam_id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $examId;
    
    /**
     * 
     * @var string
     * @ORM\Column(name="exam_name", type="string", length=120, unique=true, nullable=false)
     */
    private $name;
    
    /**
     * 
     * @var \DateTime
     * @ORM\Column(name="exam_date", type="datetime", nullable=false)
     */
    private $date;
    
    /**
     * 
     * @var \DateTime
     * @ORM\Column(name="exam_start_time", type="time", nullable=true)
     */
    private $startTime;
    
    /**
     * 
     * @var \DateTime
     * @ORM\Column(name="exam_end_time", type="time", nullable=true)
     */
    private $endTime;
    
    /**
     * 
     * @var string armazena o gabarito no momento da correção das respostas dos alunos em formato JSON.
     * @ORM\Column(name="exam_answers", type="string", length=1000, nullable=true)
     */
    private $answers;
    
    /**
     * 
     * @var ExamContent
     * @ORM\ManyToOne(targetEntity="ExamContent", inversedBy="exams")
     * @ORM\JoinColumn(name="exam_content_id", referencedColumnName="exam_content_id")
     */
    private $content;
    
    /**
     * 
     * @var ExamApplication
     * @ORM\ManyToOne(targetEntity="ExamApplication", inversedBy="exams", fetch="EAGER")
     * @ORM\JoinColumn(name="exam_application_id", referencedColumnName="exam_application_id")
     */
    private $application;
    
    /**
     * 
     * @return integer
     */
    public function getExamId()
    {
        return $this->examId;
    }

    /**
     * 
     * @return string
     */
    public function getName()
    {
        return $this->name;
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
     * @return array
     */
    public function getAnswers()
    {
        return $this->answers;
    }

    /**
     * 
     * @return ExamContent
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * 
     * @return ExamApplication
     */
    public function getApplication()
    {
        return $this->application;
    }
       
    /**
     * 
     * @param string $name
     * @return Exam
     */
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    /**
     * 
     * @param \Datetime $date
     * @return Exam
     */
    public function setDate($date)
    {
        $this->date = $date;
        return $this;
    }
    
    /**
     * 
     * @param \Datetime $startTime
     * @return Exam
     */
    public function setStartTime($startTime)
    {
        $this->startTime = $startTime;
        return $this;
    }

    /**
     * 
     * @param \Datetime $endTime
     * @return Exam
     */
    public function setEndTime($endTime)
    {
        $this->endTime = $endTime;
        return $this;
    }

    /**
     * 
     * @param string $answers
     * @return Exam
     */
    public function setAnswers($answers)
    {
        $this->answers = $answers;
        return $this;
    }  
    
    /**
     * 
     * @param ExamContent $content
     * @return Exam
     */
    public function setContent($content)
    {
        $this->content = $content;
        return $this;
    }
    
    /**
     * 
     * @param ExamApplication $application
     * @return Exam
     */
    public function setApplication($application)
    {
        $this->application = $application;
        return $this;
    }
    
}
