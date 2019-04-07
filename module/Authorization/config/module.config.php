<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Authorization;

return [
    'controllers' => [
        'factories' => [
            'Authorization\Controller\Privilege' => Factory\Controller\PrivilegeControllerFactory::class,
            'Authorization\Controller\Role' => Factory\Controller\RoleControllerFactory::class,
            'Authorization\Controller\Resource' => Factory\Controller\ResourceControllerFactory::class,
            'Authorization\Controller\Index' =>Factory\Controller\IndexControllerFactory::class,
        ],
    ],
    'router' => [
        'routes' => [
            'authorization' => [
                'type' => 'Literal',
                'options' => [
                    'route' => '/authorization',
                    'defaults' => [
                        'controller' => 'Authorization\Controller\Index',
                        'action' => 'index',
                    ],
                ],
                'may_terminate' => true,
                'child_routes' => [
                    'default' => [
                        'type' => 'Segment',
                        'options' => [
                            'route' => '/index[/:action]',
                            'constraints' => [
                                'controller' => 'Authorization\Controller\Index',
                                'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                            ],
                            'defaults' => [
                                'controller' => 'Authorization\Controller\Index',
                                'action' => 'index',
                            ],
                        ],
                    ],
                    'role' => [
                        'type' => 'Segment',
                        'options' => [
                            'route' => '/role[/:action[/:id]]',
                            'constraints' => [
                                'controller' => 'Authorization\Controller\Role',
                                'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                                'id' => '[0-9]+',
                            ],
                            'defaults' => [
                                'controller' => 'Authorization\Controller\Role',
                                'action' => 'index',
                            ],
                        ],
                    ],
                    'resource' => [
                        'type' => 'Segment',
                        'options' => [
                            'route' => '/resource[/:action[/:id]]',
                            'constraints' => [
                                'controller' => 'Authorization\Controller\Resource',
                                'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                                'id' => '[0-9]+',
                            ],
                            'defaults' => [
                                'controller' => 'Authorization\Controller\Resource',
                                'action' => 'index',
                            ],
                        ],
                    ],
                    'privilege' => [
                        'type' => 'Segment',
                        'options' => [
                            'route' => '/privilege[/:action[/:id]]',
                            'constraints' => [
                                'controller' => 'Authorization\Controller\Privilege',
                                'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                                'id' => '[0-9]+',
                            ],
                            'defaults' => [
                                'controller' => 'Authorization\Controller\Privilege',
                                'action' => 'index',
                            ],
                        ],
                    ],
                ],
            ],
        ],
    ],
    'doctrine' => [
        'driver' => [
            'authorization_driver' => [
                'class' => 'Doctrine\ORM\Mapping\Driver\AnnotationDriver',
                'cache' => 'array',
                'paths' => [
                    __DIR__ . '/../src/Authorization/Entity',
                ],
            ],
            'orm_default' => [
                'drivers' => [
                    'Authorization\Entity' => 'authorization_driver',
                ],
            ],
        ],
    ],
    'view_manager' => [
        'template_path_stack' => [
            __DIR__ . '/../view',
        ],
        'template_map' => [
            'empty/layout' => __DIR__ . '/../view/layout/empty-layout.phtml',
        ],
    ],
    'service_manager' => [
        'factories' => [
            'acl' => 'Authorization\Factory\Acl\AclDbFactory'
        ],
    ],
    'view_helpers' => [
        'factories' => [
            'isAllowed' => 'Authorization\Factory\Acl\IsAllowedViewFactory',
        ],
    ],
    'controller_plugins' => [
        'factories' => [
            'isAllowed' => 'Authorization\Factory\Acl\IsAllowedControllerFactory',
        ],
    ],
    'navigation' => [
        'default' => [
            [
                'label' => 'Role',
                'uri' => '#',
                'icon' => 'fa fa-file-o',
                'order' => 3,
                'resource' => 'Authorization\Controller\Role',
                'pages' => [
                    [
                        'label' => 'Show roles',
                        'route' => 'authorization/role',
                        'action' => 'index',
                        'icon' => 'fa fa-files-o',
                        'toolbar' => [
                            [
                                'url' => '/authorization/role/delete/$id',
                                'title' => 'Remover',
                                'description' => 'Remove um papel selecionado',
                                'class' => 'fa fa-trash-o bg-red',
                                'fntype' => 'selectedAjaxClick',
                            ],
//                            [
//                                'url' => '/authorization/role/edit/$id',
//                                'title' => 'Editar',
//                                'description' => 'Editar um papel selecionado',
//                                'class' => 'fa fa-edit bg-blue',
//                                'fntype' => 'selectedHttpClick',
//                                'target' => '_blank',
//                            ],
                        ],
                    ],
                    [
                        'label' => 'Create a role',
                        'route' => 'authorization/role',
                        'action' => 'create',
                        'icon' => 'fa fa-file-o'
                    ],
                    [
                        'label' => 'Add role to a user',
                        'route' => 'authorization/role',
                        'action' => 'add-role-to-user',
                        'icon' => 'fa fa-file-o'
                    ],
                    [
                        'label' => 'Remove user roles',
                        'route' => 'authorization/role',
                        'action' => 'remove-user-role',
                        'icon' => 'fa fa-close'
                    ],
                    [
                        'label' => 'Users x roles',
                        'route' => 'authorization/role',
                        'action' => 'users-x-roles',
                        'icon' => 'fa fa-users',
                    ],
                ]
            ],
            [
                'label' => 'Resource',
                'uri' => '#',
                'icon' => 'fa fa-retweet',
                'order' => 4,
                'resource' => 'Authorization\Controller\Resource',
                'pages' => [
                    [
                        'label' => 'Show resources',
                        'route' => 'authorization/resource',
                        'action' => 'index',
                        'icon' => 'fa fa-retweet',
                        'toolbar' => [
                            [
                                'url' => '/authorization/resource/delete/$id',
                                'title' => 'Remover',
                                'description' => 'Remove o recurso selecionado',
                                'class' => 'fa fa-trash-o bg-red',
                                'fntype' => 'selectedAjaxClick',
                            ],
                        ],
                    ],
                    [
                        'label' => 'Create a resource',
                        'route' => 'authorization/resource',
                        'action' => 'create',
                        'icon' => 'fa fa-retweet'
                    ],
                ]
            ],
            [
                'label' => 'Privilege',
                'uri' => '#',
                'icon' => 'fa fa-bullseye',
                'order' => 5,
                'resource' => 'Authorization\Controller\Privilege',
                'pages' => [
                    [
                        'label' => 'Show privileges',
                        'route' => 'authorization/privilege',
                        'action' => 'index',
                        'icon' => 'fa fa-bullseye',
                        'toolbar' => [
                            [
                                'url' => '/authorization/privilege/delete/$id',
                                'title' => 'Remover',
                                'description' => 'Remove o privilégio selecionado',
                                'class' => 'fa fa-trash-o bg-red',
                                'fntype' => 'selectedAjaxClick',
                            ],
                        ],
                    ],
                    [
                        'label' => 'Create a privilege',
                        'route' => 'authorization/privilege',
                        'action' => 'create',
                        'icon' => 'fa fa-bullseye'
                    ],
                ]
            ],
        ],
    ],
];
