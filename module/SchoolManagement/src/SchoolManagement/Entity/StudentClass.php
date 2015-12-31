<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace SchoolManagement\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use SchoolManagement\Entity\Enrollment;

/**
 * Description of StudentClass
 *
 * @author Márcio
 * @ORM\Table(name="class")
 * @ORM\Entity(repositoryClass="SchoolManagement\Entity\Repository\StudentClass")
 */
class StudentClass
{

    /**
     *
     * @var integer
     * @ORM\Column(name="class_id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $classId;

    /**
     *
     * @var \DateTime
     * @ORM\Column(name="class_begin_date", type="date", nullable=false)
     */
    private $classBeginDate;

    /**
     *
     * @var \DateTime
     * @ORM\Column(name="class_end_date", type="date", nullable=false)
     */
    private $classEndDate;

    /**
     *
     * @var string
     * @ORM\Column(name="class_name", type="string", length=80, nullable=false, unique=true)
     */
    private $className;

    /**
     *
     * @var Collection
     * @ORM\OneToMany(targetEntity="Enrollment", mappedBy="class", fetch="EXTRA_LAZY", cascade={"persist"})
     */
    private $enrollments;

    public function __construct()
    {
        $this->enrollments = new ArrayCollection();
    }

    /**
     * 
     * @return integer
     */
    public function getClassId()
    {
        return $this->classId;
    }

    /**
     * 
     * @return \DateTime
     */
    public function getClassBeginDate()
    {
        return $this->classBeginDate;
    }

    /**
     * 
     * @return \DateTime
     */
    public function getClassEndDate()
    {
        return $this->classEndDate;
    }

    /**
     * 
     * @return string
     */
    public function getClassName()
    {
        return $this->className;
    }

    /**
     * 
     * @param \DateTime $classBeginDate
     * @return StudentClass
     */
    public function setClassBeginDate(\DateTime $classBeginDate)
    {
        $this->classBeginDate = $classBeginDate;
        return $this;
    }

    /**
     * 
     * @param \DateTime $classEndDate
     * @return StudentClass
     */
    public function setClassEndDate(\DateTime $classEndDate)
    {
        $this->classEndDate = $classEndDate;
        return $this;
    }

    /**
     * 
     * @param string $className
     * @return StudentClass
     */
    public function setClassName($className)
    {
        $this->className = $className;
        return $this;
    }

    /**
     * 
     * @return Collection
     */
    public function getEnrollments()
    {
        return $this->enrollments;
    }

    /**
     * 
     * @param Collection $enrollments
     * @return StudentClass
     */
    public function setEnrollments(Collection $enrollments)
    {
        $this->enrollments = $enrollments;
        return $this;
    }

    /**
     * 
     * @param Enrollment $enrollment
     * @return SchoolManagement\Entity\StudentClass
     */
    public function addEnrollment(Enrollment $enrollment)
    {
        $enrollment->setClass($this);
        $this->enrollments[] = $enrollment;
        return $this;
    }

    /**
     * 
     * @param Enrollment $enrollment
     * @return SchoolManagement\Entity\StudentClass
     */
    public function removeEnrollment(Enrollment $enrollment)
    {
        $this->Enrollments->removeElement($enrollment);
        return $this;
    }

}
