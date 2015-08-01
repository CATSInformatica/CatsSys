<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Authentication\Provider;

/**
 * Description of ProvidesAuthentication
 *
 * @author marcio
 */
trait ProvidesAuthentication
{

    protected $auth;

    protected function hasIdentity()
    {

        if (null == $this->auth) {
            $this->auth = $this->getServiceLocator()->get('Zend\Authentication\AuthenticationService');
        }

        return $this->auth->hasIdentity();
    }

}
