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
 * Bens móveis da família.
 *
 * @author Márcio Dias <marciojr91@gmail.com>
 * @ORM\Table(name="family_good")
 * @ORM\Entity
 */
class FamilyGood
{

    /**
     *
     * @var int
     * @ORM\Column(name="family_good_id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $familyGoodId;

    /**
     *
     * @var string
     * @ORM\Column(name="family_good_name", type="string", length=200, nullable=false)
     */
    private $goodName;

    /**
     *
     * @var string
     * @ORM\Column(name="family_good_description", type="string", length=500, nullable=true)
     */
    private $goodDescription;

    /**
     *
     * @var string
     * @ORM\Column(name="family_good_value", type="float", nullable=false)
     */
    private $goodValue;

    /**
     *
     * @var PreInterview
     * @ORM\ManyToOne(targetEntity="PreInterview", inversedBy="familyGoods")
     * @ORM\JoinColumn(name="pre_interview_id", referencedColumnName="pre_interview_id", nullable=false)
     */
    private $preInterview;

    public function getFamilyGoodId()
    {
        return $this->familyGoodId;
    }

    public function getGoodName()
    {
        return $this->goodName;
    }

    public function setGoodName($goodName)
    {
        $this->goodName = $goodName;
        return $this;
    }

    public function getGoodDescription()
    {
        return $this->goodDescription;
    }

    public function setGoodDescription($goodDescription)
    {
        $this->goodDescription = $goodDescription;
        return $this;
    }

    public function getGoodValue()
    {
        return $this->goodValue;
    }

    public function setGoodValue($goodValue)
    {
        $this->goodValue = $goodValue;
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
