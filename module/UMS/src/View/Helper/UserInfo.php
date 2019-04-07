<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace UMS\View\Helper;

use Zend\Session\Container;
use Zend\View\Helper\AbstractHelper;

/**
 * Description of UserInfo
 *
 * @author marcio
 */
class UserInfo extends AbstractHelper
{

    protected $userRoles;

    public function __construct()
    {
        $userContainer = new Container('User');

        $this->userRoles = [
            'userName' => 'Guest User',
            'activeRole' => 'guest',
            'allRoles' => [],
        ];

        if ($userContainer->id) {
            $this->userRoles['activeRole'] = $userContainer->activeRole;
            $this->userRoles['allRoles'] = $userContainer->allRoles;
        }
    }

    public function __invoke()
    {
        return $this->userRoles;
    }

}
