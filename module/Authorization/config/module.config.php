<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Authorization;

use Authorization\Acl\AclDb;
use Authorization\Controller\Plugin\IsAllowed as IsAllowedControllerPluggin;
use Authorization\View\Helper\IsAllowed as IsAllowedVieHelper;

return array(
    'controllers' => array(
        'invokables' => array(
            'Authorization\Controller\Index' => 'Authorization\Controller\IndexController',
            'Authorization\Controller\Role' => 'Authorization\Controller\RoleController',
            'Authorization\Controller\Resource' => 'Authorization\Controller\ResourceController',
            'Authorization\Controller\Privilege' => 'Authorization\Controller\PrivilegeController',
        )
    ),
    'router' => array(
        'routes' => array(
            'authorization' => array(
                'type' => 'Literal',
                'options' => array(
                    'route' => '/authorization',
                    'defaults' => array(
                        '__NAMESPACE__' => 'Authorization\Controller',
                        'controller' => 'index',
                        'action' => 'index',
                    ),
                ),
                'may_terminate' => true,
                'child_routes' => array(
                    'default' => array(
                        'type' => 'Segment',
                        'options' => array(
                            'route' => '/[:controller[/:action[/:id]]]',
                            'constraints' => array(
                                'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
                                'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                            ),
                            'defaults' => array(
                            ),
                        ),
                    ),
                ),
            ),
        ),
    ),
    'doctrine' => array(
        'configuration' => array(
            'orm_default' => array(
                'generate_proxies' => true,
            ),
        ),
        'driver' => array(
            'authorization_driver' => array(
                'class' => 'Doctrine\ORM\Mapping\Driver\AnnotationDriver',
                'cache' => 'array',
                'paths' => array(
                    __DIR__ . '/../src/Authorization/Entity',
                ),
            ),
            'orm_default' => array(
                'drivers' => array(
                    'Authorization\Entity' => 'authorization_driver',
                ),
            ),
        ),
    ),
    
    'view_manager' => array(
        'template_path_stack' => array(
            'authorization' => __DIR__ . '/../view',
        ),
        'display_exceptions' => true,
    ),
    'service_manager' => array(
        'factories' => array(
            'acl' => function ($sm) {
                return new AclDb($sm->get('Doctrine\ORM\EntityManager'));
            }
        ),
    ),
    'view_helpers' => array(
        'factories' => array(
            'isAllowed' => function($sm) {
                $sm = $sm->getServiceLocator(); // $sm was the view helper's locator
                $auth = $sm->get('Zend\Authentication\AuthenticationService');
                $acl = $sm->get('acl');
                $helper = new IsAllowedVieHelper($auth, $acl);
                return $helper;
            },
        ),
    ),
    'controller_plugins' => array(
        'factories' => array(
            'isAllowed' => function($sm) {
                $sm = $sm->getServiceLocator(); // $sm was the view helper's locator
                $auth = $sm->get('Zend\Authentication\AuthenticationService');
                $acl = $sm->get('acl');
                $plugin = new IsAllowedControllerPluggin($auth, $acl);
                return $plugin;
            }
        ),
    ),
);
