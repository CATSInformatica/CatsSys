<?php

namespace Authentication\Entity;

use Authentication\Service\UserService;
use Authorization\Entity\Role;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * User
 *
 * @ORM\Table(name="user")
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
     * @ORM\Column(name="user_name", type="string", length=100, nullable=false, unique=true)
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
     * @var boolean
     *
     * @ORM\Column(name="user_active", type="boolean", nullable=false)
     */
    private $userActive;

    /**
     * @var DateTime
     *
     * @ORM\Column(name="user_registration_date", type="datetime", nullable=false)
     */
    private $userRegistrationDate;

    /**
     * @var Collection
     *
     * @ORM\ManyToMany(targetEntity="Authorization\Entity\Role", mappedBy="user")
     */
    private $role;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->role = new ArrayCollection();
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
        $pass = UserService::encryptPassword($userPassword);
        $this->userPassword = $pass['password'];
        return $this->setUserPasswordSalt($pass['password_salt']);
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
     * @param DateTime $userRegistrationDate
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
     * @return DateTime
     */
    public function getUserRegistrationDate()
    {
        return $this->userRegistrationDate;
    }

    /**
     * Add role
     *
     * @param Role $role
     *
     * @return User
     */
    public function addRole(Role $role)
    {
        $role->addUser($this);
        $this->role->add($role);
        return $this;
    }

    /**
     * Remove role
     *
     * @param Role $role
     */
    public function removeRole(Role $role)
    {
        $role->removeUser($this);
        $this->role->removeElement($role);
        return $this;
    }

    /**
     * Get role
     *
     * @return Collection
     */
    public function getRole()
    {
        return $this->role;
    }

}
