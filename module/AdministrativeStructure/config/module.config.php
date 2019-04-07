<?php

namespace AdministrativeStructure;

return [
    'controllers' => [
        'factories' => [
            'AdministrativeStructure\Controller\Department' => Factory\Controller\DepartmentControllerFactory::class,
            'AdministrativeStructure\Controller\Job' => Factory\Controller\JobControllerFactory::class,
            'AdministrativeStructure\Controller\Documents' => Factory\Controller\DocumentsControllerFactory::class,
        ],
    ],
    'router' => [
        'routes' => [
            'administrative-structure' => [
                'type' => 'Literal',
                'options' => [
                    'route' => '/administrative-structure',
                ],
                'may_terminate' => false,
                'child_routes' => [
                    'department' => [
                        'type' => 'Segment',
                        'options' => [
                            'route' => '/department[/:action[/:id]]',
                            'constraints' => [
                                'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                                'id' => '[0-9]+',
                            ],
                            'defaults' => [
                                'controller' => 'AdministrativeStructure\Controller\Department',
                                'action' => 'index',
                            ],
                        ],
                    ],
                    'job' => [
                        'type' => 'Segment',
                        'options' => [
                            'route' => '/job[/:action[/:id]]',
                            'contraints' => [
                                'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                                'id' => '[0-9]+',
                            ],
                            'defaults' => [
                                'controller' => 'AdministrativeStructure\Controller\Job',
                            ],
                        ],
                    ],
                    'documents' => [
                        'type' => 'Segment',
                        'options' => [
                            'route' => '/documents[/:action]',
                            'contraints' => [
                                'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                            ],
                            'defaults' => [
                                'controller' => 'AdministrativeStructure\Controller\Documents',
                            ],
                        ],
                    ],
                ],
            ],
        ],
    ],
    'view_manager' => [
        'template_map' => [
        ],
        'template_path_stack' => [
            __DIR__ . '/../view/',
        ],
        'display_exceptions' => true,
    ],
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
                        'icon' => 'fa fa-sitemap',
                        'pages' => [
                            ['label' => 'Edit a department',
                                'route' => 'administrative-structure/department',
                                'action' => 'edit',
                                'resource' => 'AdministrativeStructure\Controller\Department',
                                'privilege' => 'edit',
                                'icon' => 'fa fa-sitemap',
                            ],
                        ],
                        'toolbar' => [
                            [
                                'url' => '/administrative-structure/department/edit/$id',
                                'title' => 'Editar departamento',
                                'description' => 'Permite alterar as informações do departamento escolhido',
                                'class' => 'fa fa-university bg-blue',
                                'fntype' => 'selectedHttpClick',
                            ],
                            [
                                'url' => '/administrative-structure/department/delete/$id',
                                'id' => 'department-delete',
                                'title' => 'Remover departamento',
                                'description' => 'Remove o departamento escolhido se ele não possuir departamentos filhos',
                                'class' => 'fa fa-university bg-red',
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
                        'icon' => 'fa fa-sitemap',
                    ],
                    [
                        'label' => 'Show jobs',
                        'route' => 'administrative-structure/job',
                        'action' => 'index',
                        'resource' => 'AdministrativeStructure\Controller\Job',
                        'privilege' => 'index',
                        'icon' => 'fa fa-users',
                        'toolbar' => [
                            [
                                'url' => '/administrative-structure/job/hierarchy',
                                'title' => 'Hierarquia de cargos',
                                'description' => 'Monta um diagrama da hierarquia de cargos',
                                'class' => 'fa fa-usb  bg-green',
                                'fntype' => 'httpClick',
                            ],
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
                                'label' => 'Edit a job',
                                'route' => 'administrative-structure/job',
                                'action' => 'edit',
                                'resource' => 'AdministrativeStructure\Controller\Job',
                                'privilege' => 'edit',
                                'icon' => 'fa fa-usb',
                            ],
                            [
                                'label' => 'Job hierarchy',
                                'route' => 'administrative-structure/job',
                                'action' => 'hierarchy',
                                'resource' => 'AdministrativeStructure\Controller\Job',
                                'privilege' => 'hierarchy',
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
                    [
                        'label' => 'Documents',
                        'route' => 'administrative-structure/documents',
                        'action' => 'index',
                        'resource' => 'AdministrativeStructure\Controller\Documents',
                        'privilege' => 'index',
                        'icon' => 'fa fa-file-text-o',
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
