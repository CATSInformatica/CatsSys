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
use Recruitment\Entity\Person;

/**
 * ORM da tabela `person_relative`.
 * @author Márcio Dias <marciojr91@gmail.com>
 * @ORM\Table(name="person_relative", uniqueConstraints={
 * @ORM\UniqueConstraint(name="relative_relative_relationship_idx", columns={"person_id", "person_relative_id"})
 * })
 * @ORM\Entity
 */
class Relative
{

    /**
     *
     * @var integer
     * @ORM\Column(name="relative_id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $relativeId;

    /**
     *
     * @var Person
     * @ORM\ManyToOne(targetEntity="Person", inversedBy="relatives")
     * @ORM\JoinColumn(name="person_id", referencedColumnName="person_id", nullable=false)
     */
    private $person;

    /**
     *
     * @var Person 
     * @ORM\ManyToOne(targetEntity="Person", inversedBy="isRelativeOf", cascade={"persist"})
     * @ORM\JoinColumn(name="person_relative_id", referencedColumnName="person_id", nullable=false)
     */
    private $relative;

    /**
     *
     * @var string
     * @ORM\Column(name="relative_relationship", type="string", length=50, nullable=false)
     */
    private $relativeRelationship;

    /**
     * 
     * @return integer
     */
    public function getRelativeId()
    {
        return $this->relativeId;
    }

    /**
     * 
     * @return Person
     */
    public function getPerson()
    {
        return $this->person;
    }

    /**
     * 
     * @param mixed Person|null $person
     * @return Relative
     */
    public function setPerson($person)
    {
        $this->person = $person;
    }

    /**
     * 
     * @return Person
     */
    public function getRelative()
    {
        return $this->relative;
    }

    /**
     * 
     * @param mixed Person|null $person
     * @return Relative
     */
    public function setRelative($person)
    {
        if ($person !== null) {
            $person->addIsRelativeOf($this);
        }
        $this->relative = $person;
        return $this;
    }

    /**
     * 
     * @return string
     */
    public function getRelativeRelationship()
    {
        return $this->relativeRelationship;
    }

    /**
     * 
     * @param string $relativeRelationship
     * @return Relative
     */
    public function setRelativeRelationship($relativeRelationship)
    {
        $this->relativeRelationship = $relativeRelationship;
        return $this;
    }
}
