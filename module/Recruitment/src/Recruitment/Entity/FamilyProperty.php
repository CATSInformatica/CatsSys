<?php
/*
 * Copyright (C) 2016 Márcio Dias <marciojr91@gmail.com>
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

namespace Recruitment\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Bens imóveis da família
 *
 * @author Márcio Dias <marciojr91@gmail.com>
 * @ORM\Table(name="family_property")
 * @ORM\Entity
 */
class FamilyProperty
{

    /**
     *
     * @var int
     * @ORM\Column(name="family_property_id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $familyPropertyId;

    /**
     *
     * @var string
     * @ORM\Column(name="family_property_name", type="string", length=200, nullable=false)
     */
    private $propertyName;

    /**
     *
     * @var string
     * @ORM\Column(name="family_property_description", type="string", length=500, nullable=false)
     */
    private $propertyDescription;

    /**
     *
     * @var string
     * @ORM\Column(name="family_property_address", type="string", length=500, nullable=false)
     */
    private $propertyAddress;

    /**
     *
     * @var PreInterview
     * @ORM\ManyToOne(targetEntity="PreInterview", inversedBy="familyProperties")
     * @ORM\JoinColumn(name="pre_interview_id", referencedColumnName="pre_interview_id", nullable=false)
     */
    private $preInterview;

    public function getFamilyPropertyId()
    {
        return $this->familyPropertyId;
    }

    public function getPropertyName()
    {
        return $this->propertyName;
    }

    public function setPropertyName($propertyName)
    {
        $this->propertyName = $propertyName;
        return $this;
    }

    public function getPropertyDescription()
    {
        return $this->propertyDescription;
    }

    public function setPropertyDescription($propertyDescription)
    {
        $this->propertyDescription = $propertyDescription;
        return $this;
    }

    public function getPropertyAddress()
    {
        return $this->propertyAddress;
    }

    public function setPropertyAddress($propertyAddress)
    {
        $this->propertyAddress = $propertyAddress;
        return $this;
    }

    public function getPreInterview()
    {
        return $this->preInterview;
    }

    public function setPreInterview(PreInterview $preInterview = null)
    {
        $this->preInterview = $preInterview;
        return $this;
    }
}
