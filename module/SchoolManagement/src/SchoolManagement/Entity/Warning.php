<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace SchoolManagement\Entity;

use Doctrine\ORM\Mapping as ORM;
use SchoolManagement\Entity\WarningType;
use SchoolManagement\Entity\Warning;

/**
 * Description of Warning
 *
 * @author Márcio Dias <marciojr91@gmail.com>
 * @ORM\Table(name="warning")
 * @ORM\Entity
 */
class Warning
{
    /**
     *
     * @var integer 
     * @ORM\Column(name="warning_id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $warningId;
    
    /**
     * ManyToOne Bidirectional
     * 
     * @var SchoolManagement\Entity\Enrollment
     * @ORM\ManyToOne(targetEntity="SchoolManagement\Entity\Enrollment", inversedBy="warnings")
     * @ORM\JoinColumn(name="enrollment_id", referencedColumnName="enrollment_id", nullable=false)
     */
    private $enrollment;

    /**
     *
     * @var SchoolManagement\Entity\WarningType
     * @ORM\ManyToOne(targetEntity="SchoolManagement\Entity\WarningType", inversedBy="warnings")
     * @ORM\JoinColumn(name="warning_type_id", referencedColumnName="warning_type_id", nullable=false)
     */
    private $warningType;

    /**
     *
     * @var \DateTime
     * @ORM\Column(name="warning_date", type="date", nullable=false)
     */
    private $warningDate;

    /**
     *
     * @var string
     * @ORM\Column(name="warning_comment", type="text", nullable=false)
     */
    private $warningComment;
    
    /**
     * 
     * @return integer
     */
    function getWarningId()
    {
        return $this->warningId;
    }
    
    /**
     * 
     * @return SchoolManagement\Entity\Enrollment
     */
    function getEnrollment()
    {
        return $this->enrollment;
    }

    /**
     * 
     * @return SchoolManagement\Entity\WarningType
     */
    function getWarningType()
    {
        return $this->warningType;
    }

    /**
     * 
     * @return DateTime
     */
    function getWarningDate()
    {
        return $this->warningDate;
    }

    /**
     * 
     * @return string
     */
    function getWarningComment()
    {
        return $this->warningComment;
    }

    /**
     * 
     * @param $warningId
     * @return Warning
     */
    function setWarningId($warningId)
    {
        $this->warningId = $warningId;
        return $this;
    }

    /**
     * 
     * @param $enrollment
     * @return Warning
     */
    function setEnrollment(Enrollment $enrollment)
    {
        $this->enrollment = $enrollment;
        return $this;
    }

    /**
     * 
     * @param $warningType
     * @return Warning
     */
    function setWarningType(WarningType $warningType)
    {
        $this->warningType = $warningType;
        return $this;
    }

    /**
     * 
     * @param $warningDate
     * @return Warning
     */
    function setWarningDate(\DateTime $warningDate)
    {
        $this->warningDate = $warningDate;
        return $this;
    }

    /**
     * 
     * @param $warningComment
     * @return Warning
     */
    function setWarningComment($warningComment)
    {
        $this->warningComment = $warningComment;
        return $this;
    }


    

}
