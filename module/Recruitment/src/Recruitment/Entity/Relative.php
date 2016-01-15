<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Recruitment\Entity;

use Doctrine\ORM\Mapping as ORM;
use Recruitment\Entity\Person;

/**
 * Description of Relative
 * @todo create constants for relationships (e.g. father, mother, sister, brother, uncle, ...)
 * @author marcio
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
