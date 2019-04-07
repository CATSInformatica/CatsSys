<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Authorization\View\Helper;

use Authorization\Acl\AclDb;
use Zend\View\Helper\AbstractHelper;
use Zend\Session\Container;

class IsAllowed extends AbstractHelper
{

    protected $auth;
    protected $acl;

    /**
     * @Todo Remover o parametro auth? (module.config.php)
     * @param type $auth
     * @param type $acl
     */

    public function __construct($auth, $acl)
    {
        $this->auth = $auth;
        $this->acl = $acl;
    }

    /**
     * Checks whether the current user has access to a resource.
     *
     * @param string $resource
     * @param string $privilege
     */
    public function __invoke($resource, $privilege = null)
    {
        $userContainer = new Container('User');

        if ($userContainer->id) {

            $role = $userContainer->activeRole;

            if (!$this->acl->hasResource($resource)) {
                throw new \Exception('Resource ' . $resource . ' not defined');
            }

            return $this->acl->isAllowed($role, $resource, $privilege);
        } else {
            return $this->acl->isAllowed(AclDb::DEFAULT_ROLE, $resource, $privilege);
        }
    }

}
