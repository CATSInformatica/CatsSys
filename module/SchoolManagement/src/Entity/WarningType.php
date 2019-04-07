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
use SchoolManagement\Entity\Warning;

/**
 * Description of Warning
 *
 * @author Márcio Dias <marciojr91@gmail.com>
 * @ORM\Table(name="warning_type")
 * @ORM\Entity
 */
class WarningType
{

    /**
     *
     * @var integer
     * @ORM\Column(name="warning_type_id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $warningTypeId;

    /**
     *
     * @var string
     * @ORM\Column(name="warning_type_name", type="string", length=80, nullable=false, unique=true)
     */
    private $warningTypeName;

    /**
     *
     * @var string
     * @ORM\Column(name="warning_type_description", type="string", length=200, nullable=false)
     */
    private $warningTypeDescription;

    /**
     *
     * @var Collection
     * @ORM\OneToMany(targetEntity="SchoolManagement\Entity\Warning", mappedBy="warningType")
     */
    private $warnings;

    public function __construct()
    {
        $this->warnings = new ArrayCollection();
    }

    /**
     *
     * @return integer
     */
    public function getWarningTypeId()
    {
        return $this->warningTypeId;
    }

    /**
     *
     * @return string
     */
    public function getWarningTypeName()
    {
        return $this->warningTypeName;
    }

    /**
     *
     * @return string
     */
    public function getWarningTypeDescription()
    {
        return $this->warningTypeDescription;
    }

    /**
     *
     * @param string $warningTypeName
     * @return SchoolManagement\Entity\WarningType
     */
    public function setWarningTypeName($warningTypeName)
    {
        $this->warningTypeName = $warningTypeName;
        return $this;
    }

    /**
     *
     * @param string $warningTypeDescription
     * @return SchoolManagement\Entity\WarningType
     */
    public function setWarningTypeDescription($warningTypeDescription)
    {
        $this->warningTypeDescription = $warningTypeDescription;
        return $this;
    }

    /**
     *
     * @param \SchoolManagement\Entity\Warning $warning
     * @return \SchoolManagement\Entity\WarningType
     */
    public function addWarning(Warning $warning)
    {
        $this->warnings->add($warning);
        return $this;
    }

    /**
     * @param \SchoolManagement\Entity\Warning $warning
     * @return \SchoolManagement\Entity\WarningType
     */
    public function removeWarning(Warning $warning)
    {
        $this->warnings->removeElement($warning);
        return $this;
    }

    /**
     *
     * @return Collection
     */
    public function getWarnings()
    {
        return $this->warnings;
    }

    /**
     * @param Collection
     * @return SchoolManagement\Entity\WarningType
     */
    public function setWarnings($warnings)
    {
        $this->warnings = $warnings;
        return $this;
    }

}
