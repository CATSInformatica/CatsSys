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
    
    /*
     * Status do simulado
     */
    const STATUS = [-1 => 'INVÁLIDO', 0 => 'CRIADO', 1 => 'REVISADO', 2 => 'APLICADO'];
    
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
     * @ORM\Column(name="exam_name", type="string", unique=true, nullable=false)
     */
    private $examName;
    
    /**
     * 
     * @var \DateTime
     * @ORM\Column(name="exam_date", type="datetime", nullable=true)
     */
    private $examDate;
    
    /**
     * Json contendo metadados sobre o simulado
     * Ex: 
     * exam_config = {
     *      "startQuestionNumber": <number>,
     *       "header": {
     *           "startTime": "<HH:MM>",
     *           "endDate": "<HH:MM>",
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
     * @var string
     * @ORM\Column(name="exam_config", type="text", nullable=true)
     */
    private $examConfig;
    
    /**
     * 
     * @var integer
     * @ORM\Column(name="exam_status", type="integer", nullable=false)
     */
    private $examStatus;
    
    /**
     * 
     * @param integer $examStatusNumber
     * @return string - Nome do status ou '', se o status não existir
     */
    public static function statusToString($examStatusNumber)
    {
        if (array_key_exists($examStatusNumber, self::STATUS)) {
            $status = self::STATUS;
            return $status[$examStatusNumber];
        } else {
            return '';
        }
    }
    
    /**
     * 
     * @param string $examStatusString
     * @return null|integer - número do status ou null se o status não existir
     */
    public static function stringToStatus($examStatusString)
    {
        foreach (self::STATUS as $i => $statusString) { 
            if ($examStatusString === $statusString) {
                return $i;
            }
        }
        return null;
    }
        
    /**
     * @return integer
     */
    public function getExamId()
    {
        return $this->examId;
    }

    /**
     * @return string
     */
    public function getExamName()
    {
        return $this->examName;
    }

    /**
     * @return \DateTime
     */
    public function getExamDate()
    {
        return $this->examDate;
    }

    /**
     * @return string
     */
    public function getExamConfig()
    {
        return $this->examConfig;
    }

    /**
     * @return integer
     */
    public function getExamStatus()
    {
        return $this->examStatus;
    }

    /**
     * 
     * @param string $examName
     * @return SchoolManagement\Entity\Exam
     */
    public function setExamName($examName)
    {
        $this->examName = $examName;
        return $this;
    }

    /**
     * 
     * @param \Datetime $examDate
     * @return SchoolManagement\Entity\Exam
     */
    public function setExamDate($examDate)
    {
        $this->examDate = $examDate;
        return $this;
    }

    /**
     * 
     * @param string $examConfig
     * @return SchoolManagement\Entity\Exam
     */
    public function setExamConfig($examConfig)
    {
        $this->examConfig = $examConfig;
        return $this;
    }

    /**
     * 
     * @param integer $examStatus
     * @return SchoolManagement\Entity\Exam
     */
    public function setExamStatus($examStatus)
    {
        $this->examStatus = $examStatus;
        return $this;
    }


    
}
