<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Documents\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Description of StudentCardIdConfig
 *
 * @author catsinformatica
 * @ORM\Table(name="student_bg_config")
 * @ORM\Entity
 */
class StudentBgConfig
{
    /**
     *
     * @var integer 
     * @ORM\Column(name="student_bg_config_id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $studentBgConfigId;
    
    /**
     *
     * @var string 
     * @ORM\Column(name="student_bg_config_phrase", type="string", length=150, nullable=false)
     */
    private $studentBgConfigPhrase;
    
    /**
     *
     * @var string 
     * @ORM\Column(name="student_bg_config_author", type="string", length=30, nullable=false)
     */
    private $studentBgConfigAuthor;
    
    /**
     *
     * @var string 
     * @ORM\Column(name="student_bg_config_img", type="string", length=100, nullable=false)
     */
    private $studentBgConfigImg;
    
    /**
     * 
     * @return integer
     */
    public function getStudentBgConfigId() {
        return $this->studentBgConfigId;
    }

    /**
     * 
     * @return string
     */
    public function getStudentBgConfigPhrase() {
        return $this->studentBgConfigPhrase;
    }

    /**
     * 
     * @return string
     */
    public function getStudentBgConfigAuthor() {
        return $this->studentBgConfigAuthor;
    }

    /**
     * 
     * @return string
     */
    public function getStudentBgConfigImg() {
        return $this->studentBgConfigImg;
    }
    
    /**
     * 
     * @param string $studentBgConfigPhrase
     * @return \Documents\Entity\StudentBgConfig
     */
    public function setStudentBgConfigPhrase($studentBgConfigPhrase) {
        $this->studentBgConfigPhrase = $studentBgConfigPhrase;
        return $this;
    }

    /**
     * 
     * @param string $studentBgConfigAuthor
     * @return \Documents\Entity\StudentBgConfig
     */
    public function setStudentBgConfigAuthor($studentBgConfigAuthor) {
        $this->studentBgConfigAuthor = $studentBgConfigAuthor;
        return $this;
    }

    /**
     * 
     * @param string $studentBgConfigImg
     * @return \Documents\Entity\StudentBgConfig
     */
    public function setStudentBgConfigImg($studentBgConfigImg) {
        $this->studentBgConfigImg = $studentBgConfigImg;
        return $this;
    }

}
