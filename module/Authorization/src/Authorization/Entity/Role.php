<?php

namespace Authorization\Entity;

use Authentication\Entity\User;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Role
 *
 * @ORM\Table(name="role", uniqueConstraints={@ORM\UniqueConstraint(name="name_UNIQUE", columns={"role_name"})})
 * @ORM\Entity
 */
class Role
{

    /**
     * @var integer
     *
     * @ORM\Column(name="role_id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $roleId;

    /**
     * @var string
     *
     * @ORM\Column(name="role_name", type="string", length=50, nullable=false)
     */
    private $roleName;

    /**
     * @var Collection
     *
     * @ORM\ManyToMany(targetEntity="\Authentication\Entity\User", inversedBy="role")
     * @ORM\JoinTable(name="user_has_role",
     *   joinColumns={
     *     @ORM\JoinColumn(name="role_id", referencedColumnName="role_id")
     *   },
     *   inverseJoinColumns={
     *     @ORM\JoinColumn(name="user_id", referencedColumnName="user_id")
     *   }
     * )
     */
    private $user;

    /**
     * @var Collection
     *
     * @ORM\ManyToMany(targetEntity="\Authorization\Entity\Role", inversedBy="role")
     * @ORM\JoinTable(name="role_parents",
     *   joinColumns={
     *     @ORM\JoinColumn(name="role_id", referencedColumnName="role_id")
     *   },
     *   inverseJoinColumns={
     *     @ORM\JoinColumn(name="parent_id", referencedColumnName="role_id")
     *   }
     * )
     */
    private $role;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->user = new ArrayCollection();
        $this->role = new ArrayCollection();
    }

    /**
     * Get roleId
     *
     * @return integer
     */
    public function getRoleId()
    {
        return $this->roleId;
    }

    /**
     * Set roleName
     *
     * @param string $roleName
     *
     * @return Role
     */
    public function setRoleName($roleName)
    {
        $this->roleName = $roleName;

        return $this;
    }

    /**
     * Get roleName
     *
     * @return string
     */
    public function getRoleName()
    {
        return $this->roleName;
    }

    /**
     * Add user
     *
     * @param Usereturn Role
     */
    public function addUser(User $user)
    {
        $this->user[] = $user;
        return $this;
    }

    /**
     * Remove user
     *
     * @param User $user
     */
    public function removeUser(User $user)
    {
        $this->user->removeElement($user);
    }

    /**
     * Get user
     *
     * @return Collection
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Add role
     *
     * @param Role $role
     *
     * @return Role
     */
    public function addRole(Role $role)
    {
        $this->role[] = $role;

        return $this;
    }

    /**
     * Remove role
     *
     * @param Role $role
     */
    public function removeRole(Role $role)
    {
        $this->role->removeElement($role);
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
