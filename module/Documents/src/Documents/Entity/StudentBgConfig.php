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

namespace Documents\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Description of StudentCardIdConfig
 *
 * @author Gabriel Pereira <rickardch@gmail.com>
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
     * @ORM\Column(name="student_bg_config_phrase", type="string", length=160, nullable=false)
     */
    private $studentBgConfigPhrase;
    
    /**
     *
     * @var string 
     * @ORM\Column(name="student_bg_config_author", type="string", length=50, nullable=false)
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
