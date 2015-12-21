<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Recruitment\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Recruitment\Entity\Person;

/**
 * Description of People
 * @author Márcio
 * @ORM\Table(name="address")
 * @ORM\Entity
 */
class Address
{

    /**
     *
     * @var integer
     * @ORM\Column(name="address_id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $addressId;

    /**
     *
     * @var string
     * @ORM\Column(name="address_postal_code", type="string", length=15, nullable=false)
     */
    private $addressPostalCode;

    /**
     *
     * @var string
     * @ORM\Column(name="address_country", type="string", length=100, nullable=false)
     */
    private $addressCountry;

    /**
     *
     * @var string
     * @ORM\Column(name="address_state", type="string", length=50, nullable=false)
     */
    private $addressState;

    /**
     *
     * @var string
     * @ORM\Column(name="address_city", type="string", length=50, nullable=false)
     */
    private $addressCity;

    /**
     *
     * @var string
     * @ORM\Column(name="address_neighborhood", type="string", length=50, nullable=false)
     */
    private $addressNeighborhood;

    /**
     *
     * @var string
     * @ORM\Column(name="address_street", type="string", length=100, nullable=true)
     */
    private $addressStreet;

    /**
     *
     * @var integer
     * @ORM\Column(name="address_number", type="smallint", nullable=true)
     */
    private $addressNumber;

    /**
     *
     * @var string
     * @ORM\Column(name="address_complement", type="string", length=100, nullable=true)
     */
    private $addressComplement;

    /**
     * @var Collection
     * @ORM\ManyToMany(targetEntity="Recruitment\Entity\Person", mappedBy="addresses")
     */
    private $people;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->people = new ArrayCollection();
    }

    /**
     * 
     * Get Address ID
     * 
     * @return integer
     */
    public function getAddressId()
    {
        return $this->addressId;
    }

    /**
     * Get Postal Code
     * @return string
     */
    function getAddressPostalCode()
    {
        return $this->addressPostalCode;
    }

    /**
     * Get Country
     * @return string
     */
    function getAddressCountry()
    {
        return $this->addressCountry;
    }

    /**
     * Get State
     * @return string
     */
    function getAddressState()
    {
        return $this->addressState;
    }

    /**
     * Get City
     * @return string
     */
    function getAddressCity()
    {
        return $this->addressCity;
    }

    /**
     * Get Neighborhood
     * @return string
     */
    function getAddressNeighborhood()
    {
        return $this->addressNeighborhood;
    }

    /**
     * Get Street
     * @return string
     */
    function getAddressStreet()
    {
        return $this->addressStreet;
    }

    /**
     * Get Number
     * @return integer
     */
    function getAddressNumber()
    {
        return $this->addressNumber;
    }

    /**
     * Get Number Complement
     * @return string
     */
    function getAddressComplement()
    {
        return $this->addressComplement;
    }

    /**
     * 
     * @return Collection
     */
    function getPople()
    {
        return $this->people;
    }

    /**
     * 
     * @param string $addressPostalCode
     */
    function setAddressPostalCode($addressPostalCode)
    {
        $this->addressPostalCode = $addressPostalCode;
    }

    /**
     * 
     * @param string $addressCountry
     */
    function setAddressCountry($addressCountry)
    {
        $this->addressCountry = $addressCountry;
    }

    /**
     * 
     * @param string $addressState
     */
    function setAddressState($addressState)
    {
        $this->addressState = $addressState;
    }

    /**
     * 
     * @param string $addressCity
     */
    function setAddressCity($addressCity)
    {
        $this->addressCity = $addressCity;
    }

    /**
     * 
     * @param string $addressNeighborhood
     */
    function setAddressNeighborhood($addressNeighborhood)
    {
        $this->addressNeighborhood = $addressNeighborhood;
    }

    /**
     * 
     * @param string $addressStreet
     */
    function setAddressStreet($addressStreet)
    {
        $this->addressStreet = $addressStreet;
    }

    /**
     * 
     * @param integer $addressNumber
     */
    function setAddressNumber($addressNumber)
    {
        $this->addressNumber = $addressNumber;
    }

    /**
     * 
     * @param string $addressComplement
     */
    function setAddressComplement($addressComplement)
    {
        $this->addressComplement = $addressComplement;
    }

    /**
     * @param Collection $people
     */
    function setPeople(Collection $people)
    {
        $this->people = $people;
    }

    /**
     * Add person
     *
     * @param Person $person
     *
     * @return Address
     */
    public function addPerson(Person $person)
    {
        $this->people[] = $person;
        return $this;
    }

    /**
     * Remove person
     *
     * @param Person $person
     */
    public function removePerson(Person $person)
    {
        $this->people->removeElement($person);
    }

}
