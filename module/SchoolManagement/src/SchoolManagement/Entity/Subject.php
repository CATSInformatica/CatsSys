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
     * @ORM\Column(name="subject_name", type="string", unique=true, nullable=false)
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
    public function getChildren()
    {
        return $this->children;
    }

    /**
     * 
     * @return Subject
     */
    public function getParent()
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
     * @param Subject $parent
     * @return \SchoolManagement\Entity\Subject
     */
    public function setParent($parent)
    {
        if ($parent !== null) {
            $parent->addChild($this);
        } else if ($this->parent !== null) {
            $this->parent->removeChild($this);
        }
        $this->parent = $parent;
        return $this;
    }

    /**
     * Se a disciplina possui pai retorna true.
     * 
     * @return bool
     */
    public function hasParent()
    {
        return $this->parent === null;
    }

    /**
     *
     * @param Subject $child
     * @return Subject
     */
    public function addChild($child)
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
    public function removeChild($child)
    {
        $this->children->removeElement($child);
        return $this;
    }

    /**
     * 
     * @param Subject $child
     * @return boolean
     */
    public function hasChild($child)
    {
        return $this->children->contains($child);
    }

    /**
     * 
     * @param Subject $child
     * @return boolean
     */
    public function hasChildren()
    {
        return ($this->children->count() > 0);
    }

}
