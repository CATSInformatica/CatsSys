<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Authorization\Acl;

/**
 * Description of AclDb
 *
 * @author marcio
 */
use Zend\Permissions\Acl\Acl as ZendAcl,
    Zend\Permissions\Acl\Role\GenericRole,
    Zend\Permissions\Acl\Resource\GenericResource as Resource;

class AclDb extends ZendAcl
{

    /**
     * Default Role
     */
    const DEFAULT_ROLE = 'guest';

    /**
     * Constructor
     *
     * @param $entityManager Inject Doctrine's entity manager to load ACL from Database
     * @return void
     */
    public function __construct($entityManager)
    {
        // verify ...
        $roles = $entityManager->getRepository('Authorization\Entity\Role')->findBy([], ['roleId' => 'asc']);
        $resources = $entityManager->getRepository('Authorization\Entity\Resource')->findAll();
        $privileges = $entityManager->getRepository('Authorization\Entity\Privilege')->findAll();

        $this->_addRoles($roles)
                ->_addAclRoles($resources, $privileges);
    }

    /**
     * Adds Roles to ACL
     *
     * @param array $roles
     * @return Authorization\Acl\AclDb
     */
    protected function _addRoles($roles)
    {
        foreach ($roles as $role) {
            if (!$this->hasRole($role->getRoleName())) {
                $parents = $role->getParents()->toArray();
                $parentNames = array();
                foreach ($parents as $parent) {
                    $parentNames[] = $parent->getRoleName();
                }
                $this->addRole(new GenericRole($role->getRoleName()), $parentNames);
            }
        }

        return $this;
    }

    /**
     * Adds Resources/privileges to ACL
     *
     * @param $resources
     * @param $privileges
     * @return User\Acl
     * @throws \Exception
     */
    protected function _addAclRoles($resources, $privileges)
    {
        foreach ($resources as $resource) {
            if (!$this->hasResource($resource->getResourceName())) {
                $this->addResource(new Resource($resource->getResourceName()));
            }
        }

        foreach ($privileges as $privilege) {

            if ($privilege->getPrivilegePermissionAllow()) {
                $this->allow(
                        $privilege->getRole()->getRoleName(),
                        $privilege->getResource()->getResourceName(),
                        ($privilege->getPrivilegeName() != 'all') ? $privilege->getPrivilegeName() : null
                );
            } else {
                $this->deny($privilege->getRole()->getRoleName(), $privilege->getResource()->getResourceName(), $privilege->getPrivilegeName());
            }
        }
        return $this;
    }

}
