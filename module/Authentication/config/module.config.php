<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Authentication;

use Zend\Authentication\AuthenticationService;
use Zend\Router\Http\Segment;
use Zend\Router\Http\Literal;

return [
   'service_manager' => [
       'factories' => [
            AuthenticationService::class => 'doctrine.authenticationservice.orm_default',
       ]
    ],
    'controllers' => [
        'factories' => [
            'Authentication\Controller\Login' => Factory\Controller\LoginControllerFactory::class,
            'Authentication\Controller\User' => Factory\Controller\UserControllerFactory::class,
        ],
    ],
    'router' => [
        'routes' => [
            'authentication' => [
                'type' => Literal::class,
                'options' => [
                    'route' => '/authentication',
                    'defaults' => [
                        '__NAMESPACE__' => 'Authentication\Controller',
                        'controller' => 'Login',
                        'action' => 'login',
                    ],
                ],
                'may_terminate' => true,
                'child_routes' => [
                    'login' => [
                        'type' => Segment::class,
                        'options' => [
                            'route' => '/login[/:action]',
                            'constraints' => [
                                'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                            ],
                            'defaults' => [
                                'controller' => 'Authentication\Controller\Login',
                                'action' => 'login',
                            ],
                        ],
                    ],
                    'user' => [
                        'type' => Segment::class,
                        'options' => [
                            'route' => '/user[/:action[/:id]]',
                            'constraints' => [
                                'controller' => 'Authentication\Controller\User',
                                'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                                'id' => '[0-9]+',
                            ],
                            'defaults' => [
                                'controller' => 'Authentication\Controller\User',
                                'action' => 'index',
                            ],
                        ],
                    ],
                ],
            ],
        ],
    ],
    'view_manager' => [
        'template_map' => [
            'login/layout' => __DIR__ . '/../view/layout/login-layout.phtml',
        ],
        'template_path_stack' => [
            __DIR__ . '/../view/',
        ],
        'display_exceptions' => true,
    ],
    // Doctrine configuration
    'doctrine' => [
        'authentication' => [
            'orm_default' => [
                'object_manager' => 'Doctrine\ORM\EntityManager',
                'identity_class' => 'Authentication\Entity\User',
                'identity_property' => 'userName',
                'credential_property' => 'userPassword',
                'credential_callable' => 'Authentication\Service\UserService::verifyHashedPassword',
            ],
        ],
        'driver' => [
            'authentication_driver' => [
                'class' => 'Doctrine\ORM\Mapping\Driver\AnnotationDriver',
                'cache' => 'array',
                'paths' => [
                    __DIR__ . '/../src/Authentication/Entity',
                ],
            ],
            'orm_default' => [
                'drivers' => [
                    'Authentication\Entity' => 'authentication_driver',
                ],
            ],
        ],
    ],
    'session' => [
        'config' => [
            'class' => 'Zend\Session\Config\SessionConfig',
            'options' => [
                'name' => 'User',
                'use_cookies' => true,
                'cookie_lifetime' => 7200, // session alive for 2 hours
                'cookie_httponly' => true,
                'cookie_secure' => false,
                'remember_me_seconds' => 3600, // remember me for 1 hour
                'gc_maxlifetime' => 7200, // session alive for 2 hours
            ],
        ],
        'storage' => 'Zend\Session\Storage\SessionArrayStorage',
        'validators' => [
            [
                'Zend\Session\Validator\RemoteAddr',
                'Zend\Session\Validator\HttpUserAgent',
            ],
        ],
    ],
    'navigation' => [
        'default' => [
            [
                'label' => 'User',
                'uri' => '#',
                'icon' => 'fa fa-user',
                'order' => 2,
                'resource' => 'Authorization\Controller\Privilege',
                'pages' => [
                    [
                        'label' => 'Show users',
                        'route' => 'authentication/user',
                        'action' => 'index',
                        'icon' => 'fa fa-users',
                        'toolbar' => [
                            [
                                'url' => '/authentication/user/delete/$id',
                                'title' => 'Remover',
                                'description' => 'Remove um usuário selecionado',
                                'class' => 'fa fa-trash-o bg-red',
                                'fntype' => 'selectedAjaxClick',
                            ],
                            [
                                'url' => '/authentication/user/edit/$id',
                                'title' => 'Editar',
                                'description' => 'Editar o usuário selecionado',
                                'class' => 'fa fa-edit bg-blue',
                                'fntype' => 'selectedHttpClick',
                                'target' => '_blank',
                            ],
                        ],
                        'pages' => [
                            [
                                'label' => 'Edit user',
                                'route' => 'authentication/user',
                                'action' => 'edit',
                                'icon' => 'fa fa-user',
                            ],
                        ],
                    ],
                    [
                        'label' => 'Create a user',
                        'route' => 'authentication/user',
                        'action' => 'create',
                        'icon' => 'fa fa-user-plus'
                    ],
                ],
            ],
        ],
    ],
];
