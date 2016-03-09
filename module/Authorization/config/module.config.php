<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Authorization;

return array(
    'controllers' => array(
        'invokables' => array(
            'Authorization\Controller\Index' => 'Authorization\Controller\IndexController',
        ),
        'factories' => array(
            'Authorization\Controller\Privilege' => Factory\Controller\PrivilegeControllerFactory::class,
            'Authorization\Controller\Role' => Factory\Controller\RoleControllerFactory::class,
            'Authorization\Controller\Resource' => Factory\Controller\ResourceControllerFactory::class,
        ),
    ),
    'router' => array(
        'routes' => array(
            'authorization' => array(
                'type' => 'Literal',
                'options' => array(
                    'route' => '/authorization',
                    'defaults' => array(
                        'controller' => 'Authorization\Controller\Index',
                        'action' => 'index',
                    ),
                ),
                'may_terminate' => true,
                'child_routes' => array(
                    'default' => array(
                        'type' => 'Segment',
                        'options' => array(
                            'route' => '/index[/:action]',
                            'constraints' => array(
                                'controller' => 'Authorization\Controller\Index',
                                'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                            ),
                            'defaults' => array(
                                'controller' => 'Authorization\Controller\Index',
                                'action' => 'index',
                            ),
                        ),
                    ),
                    'role' => array(
                        'type' => 'Segment',
                        'options' => array(
                            'route' => '/role[/:action[/:id]]',
                            'constraints' => array(
                                'controller' => 'Authorization\Controller\Role',
                                'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                                'id' => '[0-9]+',
                            ),
                            'defaults' => array(
                                'controller' => 'Authorization\Controller\Role',
                                'action' => 'index',
                            ),
                        ),
                    ),
                    'resource' => array(
                        'type' => 'Segment',
                        'options' => array(
                            'route' => '/resource[/:action[/:id]]',
                            'constraints' => array(
                                'controller' => 'Authorization\Controller\Resource',
                                'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                                'id' => '[0-9]+',
                            ),
                            'defaults' => array(
                                'controller' => 'Authorization\Controller\Resource',
                                'action' => 'index',
                            ),
                        ),
                    ),
                    'privilege' => array(
                        'type' => 'Segment',
                        'options' => array(
                            'route' => '/privilege[/:action[/:id]]',
                            'constraints' => array(
                                'controller' => 'Authorization\Controller\Privilege',
                                'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                                'id' => '[0-9]+',
                            ),
                            'defaults' => array(
                                'controller' => 'Authorization\Controller\Privilege',
                                'action' => 'index',
                            ),
                        ),
                    ),
                ),
            ),
        ),
    ),
    'doctrine' => array(
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
            __DIR__ . '/../view',
        ),
        'template_map' => array(
            'empty/layout' => __DIR__ . '/../view/layout/empty-layout.phtml',
        ),
    ),
    'service_manager' => array(
        'factories' => array(
            'acl' => 'Authorization\Factory\Acl\AclDbFactory'
        ),
    ),
    'view_helpers' => array(
        'factories' => array(
            'isAllowed' => 'Authorization\Factory\Acl\IsAllowedViewFactory',
        ),
    ),
    'controller_plugins' => array(
        'factories' => array(
            'isAllowed' => 'Authorization\Factory\Acl\IsAllowedControllerFactory',
        ),
    ),
    'navigation' => array(
        'default' => array(
            array(
                'label' => 'Role',
                'uri' => '#',
                'icon' => 'fa fa-file-o',
                'order' => 3,
                'resource' => 'Authorization\Controller\Role',
                'pages' => array(
                    array(
                        'label' => 'Show roles',
                        'route' => 'authorization/role',
                        'action' => 'index',
                        'icon' => 'fa fa-files-o',
                        'toolbar' => array(
                            array(
                                'url' => '/authorization/role/delete/$id',
                                'title' => 'Remover',
                                'description' => 'Remove um papel selecionado',
                                'class' => 'fa fa-trash-o bg-red',
                                'fntype' => 'selectedAjaxClick',
                            ),
//                            array(
//                                'url' => '/authorization/role/delete/$id',
//                                'title' => 'Editar',
//                                'description' => 'Editar um papel selecionado',
//                                'class' => 'fa fa-edit bg-blue',
//                                'fntype' => 'selectedHttpClick',
//                                'target' => '_blank',
//                            ),
                        ),
                    ),
                    array(
                        'label' => 'Create a role',
                        'route' => 'authorization/role',
                        'action' => 'create',
                        'icon' => 'fa fa-file-o'
                    ),
                    array(
                        'label' => 'Add role to a user',
                        'route' => 'authorization/role',
                        'action' => 'add-role-to-user',
                        'icon' => 'fa fa-file-o'
                    ),
                    array(
                        'label' => 'Remove user roles',
                        'route' => 'authorization/role',
                        'action' => 'remove-user-role',
                        'icon' => 'fa fa-close'
                    ),
                    array(
                        'label' => 'Users x roles',
                        'route' => 'authorization/role',
                        'action' => 'users-x-roles',
                        'icon' => 'fa fa-users',
                    ),
                )
            ),
            array(
                'label' => 'Resource',
                'uri' => '#',
                'icon' => 'fa fa-retweet',
                'order' => 4,
                'resource' => 'Authorization\Controller\Resource',
                'pages' => array(
                    array(
                        'label' => 'Show resources',
                        'route' => 'authorization/resource',
                        'action' => 'index',
                        'icon' => 'fa fa-retweet'
                    ),
                    array(
                        'label' => 'Create a resource',
                        'route' => 'authorization/resource',
                        'action' => 'create',
                        'icon' => 'fa fa-retweet'
                    ),
                )
            ),
            array(
                'label' => 'Privilege',
                'uri' => '#',
                'icon' => 'fa fa-bullseye',
                'order' => 5,
                'resource' => 'Authorization\Controller\Privilege',
                'pages' => array(
                    array(
                        'label' => 'Show privileges',
                        'route' => 'authorization/privilege',
                        'action' => 'index',
                        'icon' => 'fa fa-bullseye'
                    ),
                    array(
                        'label' => 'Create a privilege',
                        'route' => 'authorization/privilege',
                        'action' => 'create',
                        'icon' => 'fa fa-bullseye'
                    ),
                )
            ),
        ),
    ),
);
