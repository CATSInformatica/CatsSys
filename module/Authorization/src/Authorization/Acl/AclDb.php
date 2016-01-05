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
    const ADMIN_ROLE = 'admin';

    /**
     * Constructor
     *
     * @param $entityManager Inject Doctrine's entity manager to load ACL from Database
     * @return void
     */
    public function __construct($entityManager)
    {
        $roles = $entityManager->getRepository('Authorization\Entity\Role')->findAll();
        $resources = $entityManager->getRepository('Authorization\Entity\Resource')->findAll();
        $privileges = $entityManager->getRepository('Authorization\Entity\Privilege')->findAll();

        $this->addDbRoles($roles)
            ->addAclDbRules($resources, $privileges);
    }

    /**
     * Adds Roles to ACL
     *
     * @todo Essa função não verifica ciclos, é fundamental que o cadastro de papéis seja feito com cautela.
     * 
     * Essa função não é à prova de ciclos. A verificação de ciclos deve ser feita no cadastro de papéis.
     * 
     * @param array $roles
     * @return Authorization\Acl\AclDb
     */
    protected function addDbRoles($roles)
    {
        // para cada role verifique se ela já foi adicionada ao sistema de permissão, se não foi tente adicioná-la
        foreach ($roles as $role) {
            $roleName = $role->getRoleName();
            if (!$this->hasRole($roleName)) {

                $parents = $role->getParents()->toArray();
                $parentNames = array();
                foreach ($parents as $parent) {
                    $parentName = $parent->getRoleName();
                    // se uma dos papéis herdados não foi adicionado no sistema de permissão tenta adicioná-lo
                    if (!$this->hasRole($parentName)) {
                        $this->addDbRoles([$parent]);
                    }
                    $parentNames[] = $parentName;
                }
                $this->addRole(new GenericRole($roleName), $parentNames);
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
    protected function addAclDbRules($resources, $privileges)
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
                    ($privilege->getResource() !== null) ? $privilege->getResource()->getResourceName() : null,
                    $privilege->getPrivilegeName()
                );
            } else {
                $this->deny($privilege->getRole()->getRoleName(),
                    ($privilege->getResource() !== null) ? $privilege->getResource()->getResourceName() : null,
                    $privilege->getPrivilegeName()
                );
            }
        }

        // Administrator inherits nothing, but is allowed all privileges
        $this->allow(self::ADMIN_ROLE);

        return $this;
    }

}
