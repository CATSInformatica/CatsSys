<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Recruitment\Entity;

use Authentication\Entity\User;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Recruitment\Entity\Address;
use Recruitment\Entity\Person;

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
     * @ORM\Column(name="person_birthday", type="date", nullable=false)
     */
    private $personBirthday;

    /**
     * @var Collection
     *
     * @ORM\ManyToMany(targetEntity="Address", inversedBy="people")
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
     *
     * @var User
     * @ORM\OneToOne(targetEntity="\Authentication\Entity\User")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="user_id")
     */
    private $user;

    /**
     *
     * @var Registration
     * @ORM\OneToOne(targetEntity="Registration", mappedBy="person")
     */
    private $registration;

    /**
     *
     * @var Collection 
     * @ORM\OneToMany(targetEntity="Relative", mappedBy="person")
     */
    private $isRelativeOf;

    /**
     *
     * @var Collection
     * @ORM\OneToMany(targetEntity="Relative", mappedBy="relative")
     */
    private $relatives;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->addresses = new ArrayCollection();
        $this->relatives = new ArrayCollection();
        $this->isRelativeOf = new ArrayCollection();
    }

    /**
     * Get Person ID
     * @return integer
     */
    public function getPersonId()
    {
        return $this->personId;
    }

    /**
     * Get Person firstname
     * @return string
     */
    public function getPersonFistName()
    {
        return $this->personFistName;
    }

    /**
     * Get Person lastname
     * @return string
     */
    public function getPersonLastName()
    {
        return $this->personLastName;
    }

    /**
     * 
     * @return \Datetime
     */
    public function getPersonBirthday()
    {
        return $this->personBirthday;
    }

    /**
     * 
     * @param \Datetime $personBirthday
     */
    public function setPersonBirthday(\Datetime $personBirthday)
    {
        $this->personBirthday = $personBirthday;
    }

    /**
     * Get person RG
     * @return string
     */
    public function getPersonRg()
    {
        return $this->personRg;
    }

    /**
     * Get person CPF
     * @return string
     */
    public function getPersonCpf()
    {
        return $this->personCpf;
    }

    /**
     * Get Person Email
     * @return string
     */
    public function getPersonEmail()
    {
        return $this->personEmail;
    }

    /**
     * Get Person socialmedia link
     * @return string
     */
    public function getPersonSocialMedia()
    {
        return $this->personSocialMedia;
    }

    /**
     * Get person photo url
     * @return string
     */
    public function getPersonPhoto()
    {
        return $this->personPhoto;
    }

    /**
     * Set person photo url
     * @param string $personPhoto
     */
    public function setPersonPhoto($personPhoto)
    {
        $this->personPhoto = $personPhoto;
    }

    /**
     * Get Person Phone
     * @return string
     */
    public function getPersonPhone()
    {
        return $this->personPhone;
    }

    /**
     * Get Person Alternative Phone
     * @return string
     */
    public function getPersonAlternativePhone()
    {
        return $this->personAlternativePhone;
    }

    /**
     * Get Person Addresses
     * @return Collection
     */
    public function getAddresses()
    {
        return $this->addresses;
    }

    /**
     * Set Person Firstname
     * @param string $personFistName
     */
    public function setPersonFistName($personFistName)
    {
        $this->personFistName = $personFistName;
    }

    /**
     * 
     * @param string $personLastName
     */
    public function setPersonLastName($personLastName)
    {
        $this->personLastName = $personLastName;
    }

    /**
     * 
     * @param string $personRg
     */
    public function setPersonRg($personRg)
    {
        $this->personRg = $personRg;
    }

    /**
     * 
     * @param string $personCpf
     */
    public function setPersonCpf($personCpf)
    {
        $this->personCpf = $personCpf;
    }

    /**
     * 
     * @param string $personEmail
     */
    public function setPersonEmail($personEmail)
    {
        $this->personEmail = $personEmail;
    }

    /**
     * 
     * @param string $personSocialMedia
     */
    public function setPersonSocialMedia($personSocialMedia)
    {
        $this->personSocialMedia = $personSocialMedia;
    }

    /**
     * 
     * @param string $personPhone
     */
    public function setPersonPhone($personPhone)
    {
        $this->personPhone = $personPhone;
    }

    /**
     * 
     * @param string $personAlternativePhone
     */
    public function setPersonAlternativePhone($personAlternativePhone)
    {
        $this->personAlternativePhone = $personAlternativePhone;
    }

    /**
     * @param Collection $addresses
     */
    public function setAddresses(Collection $addresses)
    {
        $this->addresses = $addresses;
    }

    /**
     * Add address
     *
     * @param Address $address
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
     * @param Address $address
     */
    public function removeAddress(Address $address)
    {
        $this->addresses->removeElement($address);
    }

    /**
     * 
     * @return User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * 
     * @return Registration
     */
    public function getRegistration()
    {
        return $this->registration;
    }

    /**
     * 
     * @param User $user
     * @return Person
     */
    public function setUser(User $user)
    {
        $this->user = $user;
        return $this;
    }

    /**
     * 
     * @param Registration $registration
     * @return Person
     */
    public function setRegistration(Registration $registration)
    {
        $this->registration = $registration;
        return $this;
    }

    /**
     * 
     * @return array
     */
    public function getIsRelativeOf()
    {
        return $this->isRelativeOf->toArray();
    }

    /**
     * 
     * @param Collection $isRelativeOf
     * @return Person
     */
    public function setIsRelativeOf(Collection $isRelativeOf)
    {
        $this->isRelativeOf = $isRelativeOf;
        return $this;
    }

    /**
     * 
     * @param Person $isRelativeOf
     * @return Person
     */
    public function addIsRelativeOf(Person $isRelativeOf)
    {
        $this->isRelativeOf[] = $isRelativeOf;
        return $this;
    }

    public function removeIsRelativeOf(Person $isRelativeOf)
    {
        $this->isRelativeOf->removeElement($isRelativeOf);
    }

    /**
     * 
     * @return array
     */
    public function getRelatives()
    {
        return $this->relatives->toArray();
    }

    /**
     * 
     * @param Collection $relatives
     * @return Person
     */
    public function setRelatives(Collection $relatives)
    {
        $this->relatives = $relatives;
        return $this;
    }
    
    /**
     * 
     * @param Person $relative
     * @return Person
     */
    public function addRelative(Person $relative)
    {
        $this->relatives[] = $relative;
        return $this;
    }
    
    /**
     * 
     * @param Person $relative
     * @return Person
     */
    public function removeRelative(Person $relative)
    {
        $this->relatives->removeElement($relative);
        return $this;
    }

}
