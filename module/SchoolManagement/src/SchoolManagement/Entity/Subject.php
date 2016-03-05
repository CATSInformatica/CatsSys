<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace SchoolManagement\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

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
     * @var Collection
     * @ORM\OneToMany(targetEntity="Subject", mappedBy="parent", cascade={"persist"})
     */
    private $children;

    /**
     *
     * @var Subject
     * @ORM\ManyToOne(targetEntity="Subject", inversedBy="children", cascade={"persist"})
     * @ORM\JoinColumn(name="parent_id", referencedColumnName="subject_id", nullable=true)
     */
    private $parent;
    
    public function __construct()
    {
        $this->children = new ArrayCollection();
    }

    /**
     * 
     * @return integer
     */
    public function getSubjectId()
    {
        return $this->subjectId;
    }

    /**
     * 
     * @return string
     */
    public function getSubjectName()
    {
        return $this->subjectName;
    }

    /**
     * 
     * @return string
     */
    public function getSubjectDescription()
    {
        return $this->subjectDescription;
    }

    /**
     * 
     * @return Collection
     */
    function getChildren()
    {
        return $this->children;
    }

    /**
     * 
     * @return Subject
     */
    function getParent()
    {
        return $this->parent;
    }

        
    /**
     * 
     * @param string $subjectName
     * @return \SchoolManagement\Entity\Subject
     */
    public function setSubjectName($subjectName)
    {
        $this->subjectName = $subjectName;
        return $this;
    }

    /**
     * 
     * @param string $subjectDescription
     * @return \SchoolManagement\Entity\Subject
     */
    public function setSubjectDescription($subjectDescription)
    {
        $this->subjectDescription = $subjectDescription;
        return $this;
    }
    
    /**
     * 
     * @param Collection $children
     * @return \SchoolManagement\Entity\Subject
     */
    function setChildren($children)
    {
        $this->children = $children;
        return $this;
    }
        
    /**
     * 
     * @param Subject $parent
     * @return \SchoolManagement\Entity\Subject
     */
    function setParent($parent)
    {
        if ($parent !== null) {
            $parent->addChild($this);
        }
        $this->parent = $parent;
        return $this;
    }

    /**
     *
     * @param Subject $child
     * @return Subject
     */
    function addChild($child)
    {
        if (!$this->hasChild($child)) {
            $this->children->add($child);
        }
        return $this;
    }

    /**
     * 
     * @param Subject $child
     * @return Subject
     */
    function removeChild($child)
    {
        $this->children->removeElement($child);
        $child->setParent(null);
        return $this;
    }

    /**
     * 
     * @param Subject $child
     * @return boolean
     */
    function hasChild($child)
    {
        return $this->children->contains($child);
    }

    /**
     * 
     * @param Subject $child
     * @return boolean
     */
    function hasChildren()
    {
        return ($this->children->count() > 0);
    }

}
