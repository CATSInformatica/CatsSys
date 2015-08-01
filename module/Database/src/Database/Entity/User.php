<?php

namespace Database\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * User
 *
 * @ORM\Table(name="user", uniqueConstraints={@ORM\UniqueConstraint(name="UNIQ_1483A5E9DFDCDFE1", columns={"user_name"})})
 * @ORM\Entity
 */
class User
{
    /**
     * @var integer
     *
     * @ORM\Column(name="user_id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $userId;

    /**
     * @var string
     *
     * @ORM\Column(name="user_name", type="string", length=100, nullable=false)
     */
    private $userName;

    /**
     * @var string
     *
     * @ORM\Column(name="user_password", type="string", length=60, nullable=false)
     */
    private $userPassword;

    /**
     * @var string
     *
     * @ORM\Column(name="user_password_salt", type="string", length=60, nullable=false)
     */
    private $userPasswordSalt;

    /**
     * @var string
     *
     * @ORM\Column(name="user_email", type="string", length=60, nullable=false)
     */
    private $userEmail;

    /**
     * @var boolean
     *
     * @ORM\Column(name="user_active", type="boolean", nullable=false)
     */
    private $userActive;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="user_registration_date", type="datetime", nullable=false)
     */
    private $userRegistrationDate;

    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\ManyToMany(targetEntity="Database\Entity\Group", mappedBy="userUser")
     */
    private $groupGroup;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->groupGroup = new \Doctrine\Common\Collections\ArrayCollection();
        $this->userRegistrationDate = new \DateTime();
    }


    /**
     * Get userId
     *
     * @return integer
     */
    public function getUserId()
    {
        return $this->userId;
    }

    /**
     * Set userName
     *
     * @param string $userName
     *
     * @return User
     */
    public function setUserName($userName)
    {
        $this->userName = $userName;

        return $this;
    }

    /**
     * Get userName
     *
     * @return string
     */
    public function getUserName()
    {
        return $this->userName;
    }

    /**
     * Set userPassword
     *
     * @param string $userPassword
     *
     * @return User
     */
    public function setUserPassword($userPassword)
    {
        $this->userPassword = $userPassword;

        return $this;
    }

    /**
     * Get userPassword
     *
     * @return string
     */
    public function getUserPassword()
    {
        return $this->userPassword;
    }

    /**
     * Set userPasswordSalt
     *
     * @param string $userPasswordSalt
     *
     * @return User
     */
    public function setUserPasswordSalt($userPasswordSalt)
    {
        $this->userPasswordSalt = $userPasswordSalt;

        return $this;
    }

    /**
     * Get userPasswordSalt
     *
     * @return string
     */
    public function getUserPasswordSalt()
    {
        return $this->userPasswordSalt;
    }

    /**
     * Set userEmail
     *
     * @param string $userEmail
     *
     * @return User
     */
    public function setUserEmail($userEmail)
    {
        $this->userEmail = $userEmail;

        return $this;
    }

    /**
     * Get userEmail
     *
     * @return string
     */
    public function getUserEmail()
    {
        return $this->userEmail;
    }

    /**
     * Set userActive
     *
     * @param boolean $userActive
     *
     * @return User
     */
    public function setUserActive($userActive)
    {
        $this->userActive = $userActive;

        return $this;
    }

    /**
     * Get userActive
     *
     * @return boolean
     */
    public function getUserActive()
    {
        return $this->userActive;
    }

    /**
     * Set userRegistrationDate
     *
     * @param \DateTime $userRegistrationDate
     *
     * @return User
     */
    public function setUserRegistrationDate($userRegistrationDate)
    {
        $this->userRegistrationDate = $userRegistrationDate;

        return $this;
    }

    /**
     * Get userRegistrationDate
     *
     * @return \DateTime
     */
    public function getUserRegistrationDate()
    {
        return $this->userRegistrationDate;
    }

    /**
     * Add groupGroup
     *
     * @param \Database\Entity\Group $groupGroup
     *
     * @return User
     */
    public function addGroupGroup(\Database\Entity\Group $groupGroup)
    {
        $this->groupGroup[] = $groupGroup;

        return $this;
    }

    /**
     * Remove groupGroup
     *
     * @param \Database\Entity\Group $groupGroup
     */
    public function removeGroupGroup(\Database\Entity\Group $groupGroup)
    {
        $this->groupGroup->removeElement($groupGroup);
    }

    /**
     * Get groupGroup
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getGroupGroup()
    {
        return $this->groupGroup;
    }
}
