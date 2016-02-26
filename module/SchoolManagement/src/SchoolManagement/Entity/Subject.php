<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace SchoolManagement\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Description of Subject
 *
 * @author Gabriel Pereira <rickardch@gmail.com>
 * @ORM\Table(name="subject")
 * @ORM\Entity
 */
class Subject
{
    /**
     *
     * @var integer 
     * @ORM\Column(name="subject_id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $subjectId;
    
    /**
     *
     * @var string 
     * @ORM\Column(name="subject_name", type="string", nullable=false)
     */
    private $subjectName;
    
    /**
     *
     * @var string 
     * @ORM\Column(name="subject_description", type="string", nullable=false)
     */
    private $subjectDescription;
    
    
    /**
     * 
     * @return integer
     */
    function getSubjectId()
    {
        return $this->subjectId;
    }

    /**
     * 
     * @return string
     */
    function getSubjectName()
    {
        return $this->subjectName;
    }

    /**
     * 
     * @return string
     */
    function getSubjectDescription()
    {
        return $this->subjectDescription;
    }

    /**
     * 
     * @param string $subjectName
     * @return \SchoolManagement\Entity\Subject
     */
    function setSubjectName($subjectName)
    {
        $this->subjectName = $subjectName;
        return $this;
    }

    /**
     * 
     * @param string $subjectDescription
     * @return \SchoolManagement\Entity\Subject
     */
    function setSubjectDescription($subjectDescription)
    {
        $this->subjectDescription = $subjectDescription;
        return $this;
    }


}
