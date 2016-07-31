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
    const STATUS_INVALID = -1;
    const STATUS_CREATED = 0;
    const STATUS_REVISED = 1;
    const STATUS_GIVEN = 2;

    /*
     * Descrição dos status do simulado
     */
    const STATUSDESC_INVALID = 'INVÁLIDO';
    const STATUSDESC_CREATED = 'CRIADO';
    const STATUSDESC_REVISED = 'REVISADO';
    const STATUSDESC_GIVEN = 'APLICADO';
    
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
     * @ORM\Column(name="exam_name", type="text", nullable=false)
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
     * @param integer $examStatus
     */
    public static function statusToString($examStatus)
    {
        switch ($examStatus) {
            case self::STATUS_CREATED:
                $status = self::STATUSDESC_CREATED;
                break;
            case self::STATUS_REVISED:
                $status = self::STATUSDESC_REVISED;
                break;
            case self::STATUS_GIVEN:
                $status = self::STATUSDESC_GIVEN;
                break;
            default:
                $status = self::STATUSDESC_INVALID;
        }

        return $status;
    }
        
    /**
     * @return integer
     */
    function getExamId()
    {
        return $this->examId;
    }

    /**
     * @return string
     */
    function getExamName()
    {
        return $this->examName;
    }

    /**
     * @return \DateTime
     */
    function getExamDate()
    {
        return $this->examDate;
    }

    /**
     * @return string
     */
    function getExamConfig()
    {
        return $this->examConfig;
    }

    /**
     * @return integer
     */
    function getExamStatus()
    {
        return $this->examStatus;
    }

    /**
     * 
     * @param string $examName
     * @return SchoolManagement\Entity\Exam
     */
    function setExamName($examName)
    {
        $this->examName = $examName;
        return $this;
    }

    /**
     * 
     * @param \Datetime $examDate
     * @return SchoolManagement\Entity\Exam
     */
    function setExamDate($examDate)
    {
        $this->examDate = $examDate;
        return $this;
    }

    /**
     * 
     * @param string $examConfig
     * @return SchoolManagement\Entity\Exam
     */
    function setExamConfig($examConfig)
    {
        $this->examConfig = $examConfig;
        return $this;
    }

    /**
     * 
     * @param integer $examStatus
     * @return SchoolManagement\Entity\Exam
     */
    function setExamStatus($examStatus)
    {
        $this->examStatus = $examStatus;
        return $this;
    }


    
}
