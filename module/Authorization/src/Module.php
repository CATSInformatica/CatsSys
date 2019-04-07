<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Authorization;

use Authorization\Acl\AclDb;
use Zend\EventManager\EventInterface;
use Zend\Session\Container;

/**
 * Description of Module
 *
 * @author marcio
 */
class Module
{

    public function getConfig()
    {
        return include __DIR__ . '/../config/module.config.php';
    }

    // FOR Authorization
    public function onBootstrap(EventInterface $e) // use it to attach event listeners
    {
        $application = $e->getApplication();
        $em = $application->getEventManager();
        if (getenv('APP_ENV') === 'production') {
            $em->attach('route', array($this, 'onRoute'), -100);
        }
    }

    // WORKING the main engine for ACL
    public function onRoute(EventInterface $e) // Event manager of the app
    {
        $application = $e->getApplication();
        $routeMatch = $e->getRouteMatch();
        $sm = $application->getServiceManager();

        /**
         * @Todo check if session container 'User' still exists
         */
        $UserContainer = new Container('User');

        //Authorization with database (check module.config.php)
        $acl = $sm->get('acl');

        // everyone is guest until it gets logged in
        $role = AclDb::DEFAULT_ROLE;

        if ($UserContainer->id) {
            $role = $UserContainer->activeRole;
        }

        $resource = $routeMatch->getParam('controller');
        $privilege = $routeMatch->getParam('action');

        if (!$acl->hasResource($resource)) {
            throw new \Exception('Resource ' . $resource . ' not defined');
        }

        if (!$acl->isAllowed($role, $resource, $privilege)) {

            // Get acl configuration to redirect route
            $response = $e->getResponse();
            $config = $sm->get('config');
            $redirect_route = $config['acl']['redirect_route'];

            $url = $e->getRouter()->assemble($redirect_route['params'], $redirect_route['options']);
            $response->getHeaders()->addHeaderLine('Location', $url);

            // The HTTP response status code 302 Found is a common way of performing a redirection.
            $response->setStatusCode(302);
            $response->sendHeaders();
            exit;
        }
    }
}
