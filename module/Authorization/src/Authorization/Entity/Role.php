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
 * @ORM\Entity(repositoryClass="Authorization\Entity\Repository\Role")
 */
class Role
{

    const ADMIN_ROLE = 'admin';
    const GUEST_ROLE = 'guest';

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
     * 
     * @ORM\ManyToMany(targetEntity="Role", mappedBy="parents", orphanRemoval=true)
     */
    private $children;

    /**
     * @var Collection
     *
     * @ORM\ManyToMany(targetEntity="Role", inversedBy="children", orphanRemoval=true)
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
     * @return Role
     */
    public function addParents(Collection $parents)
    {
        foreach ($parents as $parent) {
            if (!$this->hasParent($parent)) {
                $this->parents->add($parent);
            }
        }

        return $this;
    }

    /**
     * 
     * @param Collection $parents
     * @return Role
     */
    public function removeParents(Collection $parents)
    {
        foreach ($parents as $parent) {
            $this->parents->removeElement($parent);
        }
        return $this;
    }

    /**
     * Remove todos os papéis que cujos nomes não são numéricos
     * Papéis com nomes numéricos são definidos por usuários comuns da aplicação
     */
    public function removeNonNumericalParentRoles()
    {
        foreach ($this->parents as $parent) {
            if (!is_numeric($parent->getRoleName())) {
                $this->parents->removeElement($parent);
            }
        }
    }

    public function hasParent(Role $role)
    {
        return $this->parents->contains($role);
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
     * @return Collection
     */
    function getChildren()
    {
        return $this->children;
    }

    /**
     * Add children roles
     *
     * @param Collection
     *
     * @return Role
     */
    public function addChildren(Collection $children)
    {
        foreach ($children as $child) {
            if (!$this->hasChild($child)) {
                $this->children->add($child);
            }
        }
        return $this;
    }

    /**
     * Remove children roles
     *
     * @param Collection
     */
    public function removeChildren(Collection $children)
    {
        foreach ($children as $child) {
            if (!$this->hasChild($child)) {
                $this->children->removeElement($child);
            }
        }
        return $this;
    }

    /**
     * Check if child $child exists
     * 
     * @param Role $child
     * @return bool
     */
    public function hasChild(Role $child)
    {
        return $this->children->contains($child);
    }

}
