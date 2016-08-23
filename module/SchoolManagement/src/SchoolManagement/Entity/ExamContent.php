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
 * Description of ExamContent
 *
 * @author Gabriel Pereira <rickardch@gmail.com>
 * @ORM\Table(name="exam_content")
 * @ORM\Entity
 */
class ExamContent
{
    
    /**
     *
     * @var integer
     * @ORM\Column(name="exam_content_id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $examContentId;
    
    /**
     * Json com o conteúdo da prova
     * Ex: 
     * config = {
     *       "header": {
     *           "areas": [
     *               "area1": {
     *                   "subarea1": <quantity>,
     *                   "subarea2": <quantity>,
     *                   .
     *                   .
     *                   .
     *               },
     *               "area2": {
     *                   "subarea1": <quantity>,
     *                   "subarea2": <quantity>,
     *                   "subarea3": <quantity>,
     *                   .
     *                   .
     *                   .
     *               },
     *               .
     *               .
     *               .
     *           ]
     *       },
     *       "questions": [
     *           {
     *               "questionId": <number>,
     *               "questionNumber": <number|null>,
     *           }
     *       ]
     *  }
     * @var string
     * @ORM\Column(name="exam_content_config", type="text", nullable=false)
     */
    private $config;
    
    /**
     * 
     * @var \DateTime
     * @ORM\Column(name="exam_content_created_date", type="datetime", nullable=false)
     */
    private $createdDate;
    
    /**
     * Breve descrição das questões
     * Ex: Conteúdo que aborda os principais tópicos das seguintes áreas: 
     * Ciências da Natureza e suas Tecnologias e Ciências Humanas e suas Tecnologias
     * 
     * @var string
     * @ORM\Column(name="exam_content_description", type="string", length=200, nullable=true)
     */
    private $description;
    
    /**
     *
     * @var Collection 
     * @ORM\OneToMany(targetEntity="Exam", mappedBy="content")
     */
    private $exams;
    
    public function __construct() {
        $this->createdDate = new \DateTime('now');
        $this->exams = new ArrayCollection();
    }
    
    /**
     * 
     * @return integer
     */
    public function getExamContentId()
    {
        return $this->examContentId;
    }

    /**
     * 
     * @return string
     */
    public function getConfig()
    {
        return $this->config;
    }
    
    /**
     * 
     * @return \DateTime
     */
    public function getCreatedDate()
    {
        return $this->createdDate;
    }

    /**
     * 
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }
        
    /**
     * 
     * @return ArrayCollection
     */
    public function getExams()
    {
        return $this->exams;
    }

    
    /**
     * 
     * @param string $config - JSON
     * Ex: 
     * config = {
     *       "header": {
     *           "areas": [
     *               "area1": {
     *                   "subarea1": <quantity>,
     *                   "subarea2": <quantity>,
     *                   .
     *                   .
     *                   .
     *               },
     *               "area2": {
     *                   "subarea1": <quantity>,
     *                   "subarea2": <quantity>,
     *                   "subarea3": <quantity>,
     *                   .
     *                   .
     *                   .
     *               },
     *               .
     *               .
     *               .
     *           ]
     *       },
     *       "questions": [
     *           {
     *               "questionId": <number>,
     *               "questionCorrectAnswer": <number|null>,
     *               "questionNumber": <number|null>,
     *           }
     *       ]
     *  }
     * @return ExamContent
     */
    public function setConfig($config)
    {
        $this->config = $config;
        return $this;
    }

    /**
     * 
     * @param \DateTime $createdDate
     * @return ExamContent
     */
    public function setCreatedDate($createdDate)
    {
        $this->createdDate = $createdDate;
        return $this;
    }

    /**
     * 
     * @param string $description
     * @return ExamContent
     */
    public function setDescription($description)
    {
        $this->description = $description;
        return $this;
    }
        
    /**
     * 
     * @param ArrayCollection $exams
     * @return ExamContent
     */
    public function setExams(ArrayCollection $exams)
    {
        $this->exams = $exams;
        return $this;
    }

    /**
     *
     * @param Exam $exam
     * @return ExamContent
     */
    function addExam($exam)
    {
        if (!$this->hasExam($exam)) {
            $this->exams->add($exam);
            $exam->setContent($this);
        }
        return $this;
    }

    /**
     * 
     * @param Exam $exam
     * @return ExamContent
     */
    function removeExam($exam)
    {
        $this->exams->removeElement($exam);
        $exam->setContent(null);
        return $this;
    }

    /**
     * 
     * @param Exam $exam
     * @return boolean
     */
    function hasExam($exam)
    {
        return $this->exams->contains($exam);
    }
        

}
