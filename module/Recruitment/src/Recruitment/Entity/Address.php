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

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Recruitment\Entity\Person;

/**
 * Description of People
 * @author Márcio Dias <marciojr91@gmail.com>
 * @ORM\Table(name="address",
 *      uniqueConstraints={@ORM\UniqueConstraint(name="addr_unique_idx", columns={"address_state",
 *      "address_city", "address_neighborhood", "address_street", "address_number","address_complement"})}
 * )
 * @ORM\Entity
 */
class Address
{

    const STATE_AC = 'AC';
    const STATE_AL = 'AL';
    const STATE_AP = 'AP';
    const STATE_AM = 'AM';
    const STATE_BA = 'BA';
    const STATE_CE = 'CE';
    const STATE_DF = 'DF';
    const STATE_ES = 'ES';
    const STATE_GO = 'GO';
    const STATE_MA = 'MA';
    const STATE_MT = 'MT';
    const STATE_MS = 'MS';
    const STATE_MG = 'MG';
    const STATE_PA = 'PA';
    const STATE_PB = 'PB';
    const STATE_PR = 'PR';
    const STATE_PE = 'PE';
    const STATE_PI = 'PI';
    const STATE_RJ = 'RJ';
    const STATE_RN = 'RN';
    const STATE_RS = 'RS';
    const STATE_RO = 'RO';
    const STATE_RR = 'RR';
    const STATE_SC = 'SC';
    const STATE_SP = 'SP';
    const STATE_SE = 'SE';
    const STATE_TO = 'TO';
    const COUNTRY_BRA = 'BRASIL';

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
     * @ORM\ManyToMany(targetEntity="Recruitment\Entity\Person", mappedBy="addresses", fetch="EXTRA_LAZY")
     */
    private $people;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->people = new ArrayCollection();
        $this->addressCountry = self::COUNTRY_BRA;
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
     * 
     * @param integer $addressId
     * @return Recruitment\Entity\Address
     */
    public function setAddressId($addressId)
    {
        $this->addressId = $addressId;
        return $this;
    }

    /**
     * Get Postal Code
     * @return string
     */
    public function getAddressPostalCode()
    {
        return $this->addressPostalCode;
    }

    /**
     * Get Country
     * @return string
     */
    public function getAddressCountry()
    {
        return $this->addressCountry;
    }

    /**
     * Get State
     * @return string
     */
    public function getAddressState()
    {
        return $this->addressState;
    }

    /**
     * Get City
     * @return string
     */
    public function getAddressCity()
    {
        return $this->addressCity;
    }

    /**
     * Get Neighborhood
     * @return string
     */
    public function getAddressNeighborhood()
    {
        return $this->addressNeighborhood;
    }

    /**
     * Get Street
     * @return string
     */
    public function getAddressStreet()
    {
        return $this->addressStreet;
    }

    /**
     * Get Number
     * @return integer
     */
    public function getAddressNumber()
    {
        return $this->addressNumber;
    }

    /**
     * Get Number Complement
     * @return string
     */
    public function getAddressComplement()
    {
        return $this->addressComplement;
    }

    /**
     * 
     * @return array
     */
    public function getPeople()
    {
        return $this->people->toArray();
    }

    /**
     * 
     * @param string $addressPostalCode
     * @return Recruitment\Entity\Address
     */
    public function setAddressPostalCode($addressPostalCode)
    {
        $this->addressPostalCode = $addressPostalCode;
        return $this;
    }

    /**
     * 
     * @param string $addressCountry
     * @return Recruitment\Entity\Address
     */
    public function setAddressCountry($addressCountry)
    {
        $this->addressCountry = $addressCountry;
        return $this;
    }

    /**
     * 
     * @param string $addressState
     * @return Recruitment\Entity\Address Description
     */
    public function setAddressState($addressState)
    {
        $this->addressState = $addressState;
        return $this;
    }

    /**
     * 
     * @param string $addressCity
     * @return Recruitment\Entity\Address
     */
    public function setAddressCity($addressCity)
    {
        $this->addressCity = $addressCity;
        return $this;
    }

    /**
     * 
     * @param string $addressNeighborhood
     * @return Recruitment\Entity\Address
     */
    public function setAddressNeighborhood($addressNeighborhood)
    {
        $this->addressNeighborhood = $addressNeighborhood;
        return $this;
    }

    /**
     * 
     * @param string $addressStreet
     * @return Recruitment\Entity\Address
     */
    public function setAddressStreet($addressStreet)
    {
        $this->addressStreet = $addressStreet;
        return $this;
    }

    /**
     * 
     * @param integer $addressNumber
     * @return Recruitment\Entity\Address
     */
    public function setAddressNumber($addressNumber)
    {
        $this->addressNumber = ($addressNumber !== '') ? $addressNumber : null;
        return $this;
    }

    /**
     * 
     * @param string $addressComplement
     * @return Recruitment\Entity\Address
     */
    public function setAddressComplement($addressComplement)
    {
        $this->addressComplement = $addressComplement;
        return $this;
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
        $this->people->add($person);
        return $this;
    }

    /**
     * 
     * @param Person $person
     * @return boolean
     */
    public function hasPerson(Person $person)
    {
        return $this->people->contains($person);
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
