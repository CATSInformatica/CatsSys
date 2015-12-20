<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace UMS\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use UMS\Entity\Address;

/**
 * Description of People
 * @author Márcio
 * @ORM\Table(name="person")
 * @ORM\Entity
 */
class Person
{

    /**
     * @var integer
     *
     * @ORM\Column(name="person_id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $personId;

    /**
     * @var string
     *
     * @ORM\Column(name="person_firstname", type="string", length=80, nullable=false)
     */
    private $personFistName;

    /**
     * @var string
     * 
     * @ORM\Column(name="person_lastname", type="string" , length=200, nullable=false)
     */
    private $personLastName;

    /**
     *
     * @var string
     * @ORM\Column(name="person_rg", type="string", length=25, nullable=false)
     */
    private $personRg;

    /**
     *
     * @var string
     * @ORM\Column(name="person_cpf", type="string", length=14, nullable=false)
     */
    private $personCpf;

    /**
     * @var string
     * @ORM\Column(name="person_email", type="string", length=50, nullable=false)
     */
    private $personEmail;

    /**
     * @var string
     * @ORM\Column(name="person_socialmedia", type="string", length=200, nullable=true)
     */
    private $personSocialMedia;

    /**
     *
     * @var string
     * @ORM\Column(name="person_photo", type="string", length=200, nullable=true)
     */
    private $personPhoto;

    /**
     *
     * @var string
     * @ORM\Column(name="person_phone", type="string", length=20, nullable=true)
     */
    private $personPhone;

    /**
     * @var string
     * @ORM\Column(name="person_alternative_phone", type="string", length=20, nullable=true)
     */
    private $personAlternativePhone;

    /**
     *
     * @var DateTime
     * @ORM\Column(name="person_birthday", type="datetime", nullable=false)
     */
    private $personBirthday;

    /**
     * @var Collection
     *
     * @ORM\ManyToMany(targetEntity="\UMS\Entity\Address", inversedBy="people")
     * @ORM\JoinTable(name="person_has_address",
     *   joinColumns={
     *     @ORM\JoinColumn(name="person_id", referencedColumnName="person_id")
     *   },
     *   inverseJoinColumns={
     *     @ORM\JoinColumn(name="address_id", referencedColumnName="address_id")
     *   }
     * )
     */
    private $addresses;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->addresses = new ArrayCollection();
    }

    /**
     * Get Person ID
     * @return integer
     */
    function getPersonId()
    {
        return $this->personId;
    }

    /**
     * Get Person firstname
     * @return string
     */
    function getPersonFistName()
    {
        return $this->personFistName;
    }

    /**
     * Get Person lastname
     * @return string
     */
    function getPersonLastName()
    {
        return $this->personLastName;
    }

    /**
     * 
     * @return \Datetime
     */
    function getPersonBirthday()
    {
        return $this->personBirthday;
    }

    /**
     * 
     * @param \Datetime $personBirthday
     */
    function setPersonBirthday(\Datetime $personBirthday)
    {
        $this->personBirthday = $personBirthday;
    }

    /**
     * Get person RG
     * @return string
     */
    function getPersonRg()
    {
        return $this->personRg;
    }

    /**
     * Get person CPF
     * @return string
     */
    function getPersonCpf()
    {
        return $this->personCpf;
    }

    /**
     * Get Person Email
     * @return string
     */
    function getPersonEmail()
    {
        return $this->personEmail;
    }

    /**
     * Get Person socialmedia link
     * @return string
     */
    function getPersonSocialMedia()
    {
        return $this->personSocialMedia;
    }

    /**
     * Get person photo url
     * @return string
     */
    function getPersonPhoto()
    {
        return $this->personPhoto;
    }

    /**
     * Set person photo url
     * @param string $personPhoto
     */
    function setPersonPhoto($personPhoto)
    {
        $this->personPhoto = $personPhoto;
    }

    /**
     * Get Person Phone
     * @return string
     */
    function getPersonPhone()
    {
        return $this->personPhone;
    }

    /**
     * Get Person Alternative Phone
     * @return string
     */
    function getPersonAlternativePhone()
    {
        return $this->personAlternativePhone;
    }

    /**
     * Get Person Addresses
     * @return type
     */
    function getAddresses()
    {
        return $this->addresses;
    }

    /**
     * Set Person Firstname
     * @param string $personFistName
     */
    function setPersonFistName($personFistName)
    {
        $this->personFistName = $personFistName;
    }

    /**
     * 
     * @param string $personLastName
     */
    function setPersonLastName($personLastName)
    {
        $this->personLastName = $personLastName;
    }

    /**
     * 
     * @param string $personRg
     */
    function setPersonRg($personRg)
    {
        $this->personRg = $personRg;
    }

    /**
     * 
     * @param string $personCpf
     */
    function setPersonCpf($personCpf)
    {
        $this->personCpf = $personCpf;
    }

    /**
     * 
     * @param string $personEmail
     */
    function setPersonEmail($personEmail)
    {
        $this->personEmail = $personEmail;
    }

    /**
     * 
     * @param string $personSocialMedia
     */
    function setPersonSocialMedia($personSocialMedia)
    {
        $this->personSocialMedia = $personSocialMedia;
    }

    /**
     * 
     * @param string $personPhone
     */
    function setPersonPhone($personPhone)
    {
        $this->personPhone = $personPhone;
    }

    /**
     * 
     * @param string $personAlternativePhone
     */
    function setPersonAlternativePhone($personAlternativePhone)
    {
        $this->personAlternativePhone = $personAlternativePhone;
    }

    /**
     * @param Collection $addresses
     */
    function setAddresses(Collection $addresses)
    {
        $this->addresses = $addresses;
    }

    /**
     * Add address
     *
     * @param Person $address
     *
     * @return Person
     */
    public function addAddress(Address $address)
    {
        $this->addresses[] = $address;
        return $this;
    }

    /**
     * Remove address
     *
     * @param Person $address
     */
    public function removeAddress(Address $address)
    {
        $this->addresses->removeElement($address);
    }

}
