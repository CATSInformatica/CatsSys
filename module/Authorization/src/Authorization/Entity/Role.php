<?php

namespace Authorization\Entity;

use Authentication\Entity\User;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Role
 *
 * @ORM\Table(name="role")
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
     * @ORM\Column(name="role_name", type="string", length=50, nullable=false, unique=true)
     */
    private $roleName;

    /**
     * @var Collection
     *
     * @ORM\ManyToMany(targetEntity="\Authentication\Entity\User", inversedBy="role")
     * @ORM\JoinTable(name="user_has_role",
     *   joinColumns={
     *     @ORM\JoinColumn(name="role_id", referencedColumnName="role_id", nullable=false)
     *   },
     *   inverseJoinColumns={
     *     @ORM\JoinColumn(name="user_id", referencedColumnName="user_id", nullable=false)
     *   }
     * )
     */
    private $user;

    /**
     *
     * @var Collection
     * @ORM\ManyToMany(targetEntity="Role", mappedBy="parents")
     */
    private $children;

    /**
     * @var Collection
     *
     * @ORM\ManyToMany(targetEntity="Role", inversedBy="children")
     * @ORM\JoinTable(name="role_parent",
     *   joinColumns={
     *     @ORM\JoinColumn(name="role_id", referencedColumnName="role_id", nullable=false)
     *   },
     *   inverseJoinColumns={
     *     @ORM\JoinColumn(name="parent_id", referencedColumnName="role_id", nullable=false)
     *   }
     * )
     */
    private $parents;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->user = new ArrayCollection();
        $this->parents = new ArrayCollection();
        $this->children = new ArrayCollection();
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
        $user->removeRole($this);
        $this->user->removeElement($user);
    }

    /**
     * Get User
     *
     * @return Collection
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * 
     * @param Collection $parents
     */
    public function setParents(Collection $parents)
    {
        $this->parents = $parents;
    }

    /**
     * Add parent
     *
     * @param Role $parent
     *
     * @return Role
     */
    public function addParent(Role $parent)
    {
        $this->parents[] = $parent;

        return $this;
    }

    /**
     * Remove parent
     *
     * @param Role $parent
     */
    public function removeParent(Role $parent)
    {
        $this->parents->removeElement($parent);
    }

    /**
     * Get parents
     *
     * @return Collection
     */
    public function getParents()
    {
        return $this->parents;
    }

    /**
     * Get child roles
     * @return type
     */
    function getChildren()
    {
        return $this->children;
    }

    /**
     * Set child roles
     * @param Collection $children
     */
    function setChildren(Collection $children)
    {
        $this->children = $children;
    }

    /**
     * Add child role
     *
     * @param Role $child
     *
     * @return Role
     */
    public function addChild(Role $child)
    {
        $this->children[] = $child;

        return $this;
    }

    /**
     * Remove child role
     *
     * @param Role $child
     */
    public function removeChild(Role $child)
    {
        $this->children->removeElement($child);
    }

}
