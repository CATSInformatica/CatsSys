<?php

namespace Authorization\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Privilege
 *
 * @ORM\Table(name="privilege", indexes={@ORM\Index(name="IDX_87209A8789329D25", columns={"resource_id"}), @ORM\Index(name="fk_privilege_role1_idx", columns={"role_id"})})
 * @ORM\Entity
 */
class Privilege
{
    /**
     * @var integer
     *
     * @ORM\Column(name="privilege_id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $privilegeId;

    /**
     * @var string
     *
     * @ORM\Column(name="privilege_name", type="string", length=100, nullable=true)
     */
    private $privilegeName;

    /**
     * @var boolean
     *
     * @ORM\Column(name="privilege_permission_allow", type="boolean", nullable=false)
     */
    private $privilegePermissionAllow;

    /**
     * @var \Authorization\Entity\Resource
     *
     * @ORM\ManyToOne(targetEntity="Resource")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="resource_id", referencedColumnName="resource_id")
     * })
     */
    private $resource;

    /**
     * @var \Authorization\Entity\Role
     *
     * @ORM\ManyToOne(targetEntity="Role")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="role_id", referencedColumnName="role_id")
     * })
     */
    private $role;



    /**
     * Get privilegeId
     *
     * @return integer
     */
    public function getPrivilegeId()
    {
        return $this->privilegeId;
    }

    /**
     * Set privilegeName
     *
     * @param string $privilegeName
     *
     * @return Privilege
     */
    public function setPrivilegeName($privilegeName)
    {
        $this->privilegeName = $privilegeName;

        return $this;
    }

    /**
     * Get privilegeName
     *
     * @return string
     */
    public function getPrivilegeName()
    {
        return $this->privilegeName;
    }

    /**
     * Set privilegePermissionAllow
     *
     * @param boolean $privilegePermissionAllow
     *
     * @return Privilege
     */
    public function setPrivilegePermissionAllow($privilegePermissionAllow)
    {
        $this->privilegePermissionAllow = $privilegePermissionAllow;

        return $this;
    }

    /**
     * Get privilegePermissionAllow
     *
     * @return boolean
     */
    public function getPrivilegePermissionAllow()
    {
        return $this->privilegePermissionAllow;
    }

    /**
     * Set resource
     *
     * @param Resource $resource
     *
     * @return Privilege
     */
    public function setResource(Resource $resource = null)
    {
        $this->resource = $resource;

        return $this;
    }

    /**
     * Get resource
     *
     * @return \Authorization\Entity\Resource
     */
    public function getResource()
    {
        return $this->resource;
    }

    /**
     * Set role
     *
     * @param \Authorization\Entity\Role $role
     *
     * @return Privilege
     */
    public function setRole(Role $role = null)
    {
        $this->role = $role;

        return $this;
    }

    /**
     * Get role
     *
     * @return \Authorization\Entity\Role
     */
    public function getRole()
    {
        return $this->role;
    }
}
