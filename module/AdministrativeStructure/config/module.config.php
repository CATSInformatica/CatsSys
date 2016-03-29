<?php

namespace AdministrativeStructure;

return [
    'controllers' => [
        'factories' => [
            'AdministrativeStructure\Controller\Department' => Factory\Controller\DepartmentControllerFactory::class,
            'AdministrativeStructure\Controller\Job' => Factory\Controller\JobControllerFactory::class,
        ],
    ],
    'router' => array(
        'routes' => array(
            'administrative-structure' => array(
                'type' => 'Literal',
                'options' => array(
                    'route' => '/administrative-structure',
                ),
                'may_terminate' => false,
                'child_routes' => array(
                    'department' => array(
                        'type' => 'Segment',
                        'options' => array(
                            'route' => '/department[/:action[/:id]]',
                            'constraints' => array(
                                'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                                'id' => '[0-9]+',
                            ),
                            'defaults' => array(
                                'controller' => 'AdministrativeStructure\Controller\Department',
                                'action' => 'index',
                            ),
                        ),
                    ),
                    'job' => array(
                        'type' => 'Segment',
                        'options' => array(
                            'route' => '/job[/:action[/:id]]',
                            'contraints' => array(
                                'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                                'id' => '[0-9]+',
                            ),
                            'defaults' => array(
                                'controller' => 'AdministrativeStructure\Controller\Job',
                            ),
                        ),
                    ),
                ),
            ),
        ),
    ),
    'view_manager' => array(
        'template_map' => array(
        ),
        'template_path_stack' => array(
            __DIR__ . '/../view/',
        ),
        'display_exceptions' => true,
    ),
    'navigation' => [
        'default' => [
            [
                'label' => 'Administrative structure',
                'uri' => '#',
                'icon' => 'fa fa-university',
                'order' => 13,
                'pages' => [
                    [
                        'label' => 'Show departments',
                        'route' => 'administrative-structure/department',
                        'action' => 'index',
                        'resource' => 'AdministrativeStructure\Controller\Department',
                        'privilege' => 'index',
                        'icon' => 'fa fa-folder-open',
                        'pages' => [
                            ['label' => 'Edit a department',
                                'route' => 'administrative-structure/department',
                                'action' => 'edit',
                                'resource' => 'AdministrativeStructure\Controller\Department',
                                'privilege' => 'edit',
                                'icon' => 'fa fa-folder-open',
                            ],
                        ],
                        'toolbar' => [
                            [
                                'url' => '/administrative-structure/department/edit/$id',
                                'title' => 'Editar departamento',
                                'description' => 'Permite alterar as informações do departamento escolhido',
                                'class' => 'fa fa-university  bg-blue',
                                'fntype' => 'selectedHttpClick',
                            ],
                            [
                                'url' => '/administrative-structure/department/delete/$id',
                                'id' => 'department-delete',
                                'title' => 'Remover departamento',
                                'description' => 'Remove o departamento escolhido se ele não possuir departamentos filhos',
                                'class' => 'fa fa-university  bg-red',
                                'fntype' => 'selectedAjaxClick',
                            ],
                        ],
                    ],
                    [
                        'label' => 'Add a Department',
                        'route' => 'administrative-structure/department',
                        'action' => 'add',
                        'resource' => 'AdministrativeStructure\Controller\Department',
                        'privilege' => 'add',
                        'icon' => 'fa fa-folder-open',
                    ],
                ],
            ],
            [
                'label' => 'Job',
                'uri' => '#',
                'icon' => 'fa fa-usb',
                'resource' => 'AdministrativeStructure\Controller\Job',
                'order' => 14,
                'pages' => [
                    [
                        'label' => 'Show jobs',
                        'route' => 'administrative-structure/job',
                        'action' => 'index',
                        'resource' => 'AdministrativeStructure\Controller\Job',
                        'privilege' => 'index',
                        'icon' => 'fa fa-users',
                        'toolbar' => [
                            [
                                'url' => '/administrative-structure/job/delete/$id',
                                'title' => 'Remover cargo',
                                'description' => 'Permite remover os cargos escolhidos',
                                'class' => 'fa fa-trash  bg-red',
                                'fntype' => 'selectedAjaxClick',
                            ],
                            [
                                'url' => '/administrative-structure/job/edit/$id',
                                'title' => 'Editar cargo',
                                'description' => 'Permite editar o cargo escolhido',
                                'class' => 'fa fa-edit bg-blue',
                                'fntype' => 'selectedHttpClick',
                            ],
                        ],
                        'pages' => [
                            [
                                'label' => 'Show jobs',
                                'route' => 'administrative-structure/job',
                                'action' => 'edit',
                                'resource' => 'AdministrativeStructure\Controller\Job',
                                'privilege' => 'edit',
                                'icon' => 'fa fa-usb',
                            ]
                        ]
                    ],
                    [
                        'label' => 'Create a job',
                        'route' => 'administrative-structure/job',
                        'action' => 'create',
                        'resource' => 'AdministrativeStructure\Controller\Job',
                        'privilege' => 'create',
                        'icon' => 'fa fa-user',
                    ],
                    [
                        'label' => 'Office manager',
                        'route' => 'administrative-structure/job',
                        'action' => 'office-manager',
                        'resource' => 'AdministrativeStructure\Controller\Job',
                        'privilege' => 'office-manager',
                        'icon' => 'fa fa-male',
                        'toolbar' => [
                            [
                                'url' => '/administrative-structure/job/add-office/$id',
                                'title' => 'Associar Cargo',
                                'description' => 'Associa o cargo selecionado ao voluntário '
                                . 'escolhido',
                                'class' => 'fa fa-pencil-square-o bg-green',
                                'fntype' => 'ajaxPostSelectedClick',
                            ],
                            [
                                'url' => '/administrative-structure/job/end-office/$id',
                                'title' => 'Finalizar cargo',
                                'description' => 'Conclui o cargo do voluntário escolhido',
                                'class' => 'fa fa-pencil-square-o bg-yellow',
                                'fntype' => 'ajaxPostSelectedClick',
                            ],
                            [
                                'url' => '/administrative-structure/job/remove-office/$id',
                                'title' => 'Remover Cargo',
                                'description' => 'Remove o cargo selecionado do voluntário escolhido',
                                'class' => 'fa fa-pencil-square-o bg-red',
                                'fntype' => 'ajaxPostSelectedClick',
                            ],
                        ],
                    ],
                ],
            ],
        ],
    ],
    'doctrine' => [
        'driver' => [
            'orm_default' => [
                'drivers' => [
                    'AdministrativeStructure\Entity' => 'administrative-structure_driver',
                ],
            ],
            'administrative-structure_driver' => [
                'class' => 'Doctrine\ORM\Mapping\Driver\AnnotationDriver',
                'cache' => 'array',
                'paths' => [
                    __DIR__ . '/../src/AdministrativeStructure/Entity',
                ],
            ],
        ],
    ],
];
