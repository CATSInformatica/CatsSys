<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Authorization;

// for Acl
use Authorization\Acl\Acl;

/**
 * Description of Module
 *
 * @author marcio
 */
class Module
{

    public function getConfig()
    {
        return include __DIR__ . '/config/module.config.php';
    }

    public function getAutoloaderConfig()
    {
        return array(
            'Zend\Loader\StandardAutoloader' => array(
                'namespaces' => array(
                    __NAMESPACE__ => __DIR__ . '/src/' . __NAMESPACE__,
                ),
            ),
        );
    }

    // FOR Authorization
    public function onBootstrap(\Zend\EventManager\EventInterface $e) // use it to attach event listeners
    {
        $application = $e->getApplication();
        $em = $application->getEventManager();
        $em->attach('route', array($this, 'onRoute'), -100);
    }

    // WORKING the main engine for ACL
    public function onRoute(\Zend\EventManager\EventInterface $e) // Event manager of the app
    {
        $application = $e->getApplication();
        $routeMatch = $e->getRouteMatch();
        $sm = $application->getServiceManager();
        $auth = $sm->get('Zend\Authentication\AuthenticationService');
        $config = $sm->get('Config');
        $acl = new Acl($config);
        // everyone is guest untill it gets logged in
        $role = Acl::DEFAULT_ROLE; // The default role is guest $acl
        if ($auth->hasIdentity()) {
            $user = $auth->getIdentity();
            $role = 'member';
        }
        $resource = $routeMatch->getParam('controller');
        $privilege = $routeMatch->getParam('action');

        if (!$acl->hasResource($resource)) {
            throw new \Exception('Resource ' . $resource . ' not defined');
        }

        if (!$acl->isAllowed($role, $resource, $privilege)) {
            $url = $e->getRouter()->assemble(array(), array('name' => 'authorization'));
            $response = $e->getResponse();
            $response->getHeaders()->addHeaderLine('Location', $url);
            $response->setStatusCode(302);
            $response->sendHeaders();
            exit;
        }
    }

}
