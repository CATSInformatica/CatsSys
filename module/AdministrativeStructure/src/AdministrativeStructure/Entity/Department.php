<?php

namespace AdministrativeStructure\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\Mapping as ORM;

/**
 * Description of Department
 *
 * @author Márcio Dias <marciojr91@gmail.com>
 * @ORM\Table(name="department")
 * @ORM\Entity
 */
class Department
{

    /**
     *
     * @var integer
     * @ORM\Column(name="department_id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $departmentId;

    /**
     *
     * @var string
     * @ORM\Column(name="department_name", type="string", length=50, nullable=false, unique=true)
     */
    private $departmentName;

    /**
     *
     * @var string
     * @ORM\Column(name="department_icon", type="string", length=50, nullable=true)
     */
    private $departmentIcon;

    /**
     *
     * @var string
     * @ORM\Column(name="department_description", type="text", nullable=false)
     */
    private $departmentDescription;

    /**
     *
     * ManyToOne Bidirectional
     * 
     * @var Department
     * @ORM\ManyToOne(targetEntity="Department", inversedBy="children")
     * @ORM\JoinColumn(name="department_parent", referencedColumnName="department_id")
     */
    private $parent;

    /**
     * 
     * OneToMany Bidirectional
     * 
     * @var Collection
     * @ORM\OneToMany(targetEntity="Department", mappedBy="parent")
     */
    private $children;

    /**
     *
     * @var bool
     * @ORM\Column(name="department_is_active", type="boolean", nullable=false)
     */
    private $isActive;

    /**
     * Cargos associados ao departamento.
     * 
     * @var Collection
     * @ORM\OneToMany(targetEntity="Job", mappedBy="department")
     */
    private $jobs;

    public function __construct()
    {
        $this->children = new ArrayCollection();
        $this->isActive = false;
        $this->departmentIcon = 'glyphicon glyphicon-sunglasses';
    }

    /**
     * 
     * @return integer
     */
    public function getDepartmentId()
    {
        return $this->departmentId;
    }

    /**
     * 
     * @return string
     */
    public function getDepartmentName()
    {
        return $this->departmentName;
    }

    /**
     * 
     * @return string
     */
    public function getDepartmentDescription()
    {
        return $this->departmentDescription;
    }

    /**
     * 
     * @return Department
     */
    public function getParent()
    {
        return $this->parent;
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
     * @return Collection
     */
    public function getActiveChildren()
    {
        $criteria = Criteria::create()
            ->where(Criteria::expr()->eq("isActive", true));

        $result = $this->children->matching($criteria);

        return $result;
    }

    /**
     * 
     * @return bool
     */
    public function getIsActive()
    {
        return $this->isActive;
    }

    /**
     * 
     * @param string $departmentName
     * @return Department
     */
    public function setDepartmentName($departmentName)
    {
        $this->departmentName = $departmentName;
        return $this;
    }

    /**
     * 
     * @return string
     */
    public function getDepartmentIcon()
    {
        return $this->departmentIcon;
    }

    /**
     * 
     * @param string $departmentIcon
     * @return Department
     */
    public function setDepartmentIcon($departmentIcon)
    {
        $this->departmentIcon = $departmentIcon;
        return $this;
    }

    /**
     * 
     * @param string $departmentDescription
     * @return Department
     */
    public function setDepartmentDescription($departmentDescription)
    {
        $this->departmentDescription = $departmentDescription;
        return $this;
    }

    /**
     * 
     * @param Department $parent
     * @return Department
     */
    public function setParent(Department $parent = null)
    {
        if ($parent !== null) {
            $parent->addChildren(new ArrayCollection([$parent]));
        }

        $this->parent = $parent;
        return $this;
    }

    /**
     * 
     * @param Collection $children
     * @return Department
     */
    public function addChildren(Collection $children)
    {
        foreach ($children as $child) {
            if (!$this->hasChild($child)) {
                $this->children->add($child);
            }
        }
        return $this;
    }

    /**
     * 
     * @param Collection $children
     * @return Department
     */
    public function removeChildren(Collection $children)
    {
        foreach ($children as $child) {
            if ($this->hasChild($child)) {
                $this->children->removeElement($child);
            }
        }

        return $this;
    }

    /**
     * 
     * @param Department $child
     * @return bool
     */
    public function hasChild(Department $child)
    {
        return $this->children->contains($child);
    }

    /**
     * @param bool $isActive
     * @return Department
     */
    public function setIsActive($isActive)
    {
        $this->isActive = $isActive;
        return $this;
    }

    /**
     * @return integer
     */
    public function getNumberOfChildren()
    {
        return $this->children->count();
    }

}
