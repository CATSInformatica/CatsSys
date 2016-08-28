<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Authentication;

return array(
    'service_manager' => array(
        'factories' => [
            'Authentication\Service\EmailSenderServiceInterface' => Factory\Service\EmailSenderServiceFactory::class,
        ],
     ),
    'controllers' => array(
        'factories' => array(
            'Authentication\Controller\Login' => Factory\Controller\LoginControllerFactory::class,
            'Authentication\Controller\User' => Factory\Controller\UserControllerFactory::class,
        ),
    ),
    'router' => array(
        'routes' => array(
            'authentication' => array(
                'type' => 'Literal',
                'options' => array(
                    'route' => '/authentication',
                    'defaults' => array(
                        '__NAMESPACE__' => 'Authentication\Controller',
                        'controller' => 'Login',
                        'action' => 'login',
                    ),
                ),
                'may_terminate' => true,
                'child_routes' => array(
                    'login' => array(
                        'type' => 'Segment',
                        'options' => array(
                            'route' => '/login[/:action]',
                            'constraints' => array(
                                'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                            ),
                            'defaults' => array(
                                'controller' => 'Authentication\Controller\Login',
                                'action' => 'login',
                            ),
                        ),
                    ),
                    'user' => array(
                        'type' => 'Segment',
                        'options' => array(
                            'route' => '/user[/:action[/:id]]',
                            'constraints' => array(
                                'controller' => 'Authentication\Controller\User',
                                'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                                'id' => '[0-9]+',
                            ),
                            'defaults' => array(
                                'controller' => 'Authentication\Controller\User',
                                'action' => 'index',
                            ),
                        ),
                    ),
                ),
            ),
        ),
    ),
    'view_manager' => array(
        'template_map' => array(
            'login/layout' => __DIR__ . '/../view/layout/login-layout.phtml',
        ),
        'template_path_stack' => array(
            __DIR__ . '/../view/',
        ),
        'display_exceptions' => true,
    ),
    // Doctrine configuration
    'doctrine' => array(
        'authentication' => array(
            'orm_default' => array(
                'object_manager' => 'Doctrine\ORM\EntityManager',
                'identity_class' => 'Authentication\Entity\User',
                'identity_property' => 'userName',
                'credential_property' => 'userPassword',
                'credential_callable' => 'Authentication\Service\UserService::verifyHashedPassword',
            ),
        ),
        'driver' => array(
            'authentication_driver' => array(
                'class' => 'Doctrine\ORM\Mapping\Driver\AnnotationDriver',
                'cache' => 'array',
                'paths' => array(
                    __DIR__ . '/../src/Authentication/Entity',
                ),
            ),
            'orm_default' => array(
                'drivers' => array(
                    'Authentication\Entity' => 'authentication_driver',
                ),
            ),
        ),
    ),
    'session' => array(
        'config' => array(
            'class' => 'Zend\Session\Config\SessionConfig',
            'options' => array(
                'name' => 'User',
                'use_cookies' => true,
                'cookie_lifetime' => 3600,
                'cookie_httponly' => true,
                'cookie_secure' => false,
                'remember_me_seconds' => 3600, // remember me for 1 hour
                'gc_maxlifetime' => 3600,
            )
        ),
        'storage' => 'Zend\Session\Storage\SessionArrayStorage',
        'validators' => array(
            array(
                'Zend\Session\Validator\RemoteAddr',
                'Zend\Session\Validator\HttpUserAgent',
            )
        )
    ),
    'navigation' => array(
        'default' => array(
            array(
                'label' => 'User',
                'uri' => '#',
                'icon' => 'fa fa-user',
                'order' => 2,
                'resource' => 'Authorization\Controller\Privilege',
                'pages' => array(
                    array(
                        'label' => 'Show users',
                        'route' => 'authentication/user',
                        'action' => 'index',
                        'icon' => 'fa fa-users',
                        'toolbar' => array(
                            array(
                                'url' => '/authentication/user/delete/$id',
                                'title' => 'Remover',
                                'description' => 'Remove um usuário selecionado',
                                'class' => 'fa fa-trash-o bg-red',
                                'fntype' => 'selectedAjaxClick',
                            ),
                            array(
                                'url' => '/authentication/user/edit/$id',
                                'title' => 'Editar',
                                'description' => 'Editar o usuário selecionado',
                                'class' => 'fa fa-edit bg-blue',
                                'fntype' => 'selectedHttpClick',
                                'target' => '_blank',
                            ),
                        ),
                        'pages' => array(
                            array(
                                'label' => 'Edit user',
                                'route' => 'authentication/user',
                                'action' => 'edit',
                                'icon' => 'fa fa-user',
                            ),
                        ),
                    ),
                    array(
                        'label' => 'Create a user',
                        'route' => 'authentication/user',
                        'action' => 'create',
                        'icon' => 'fa fa-user-plus'
                    ),
                )
            ),
        ),
    ),
);
