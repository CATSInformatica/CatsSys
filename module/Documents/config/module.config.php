<?php

namespace Documents;

use Zend\Router\Http\Segment;
use Zend\Router\Http\Literal;

return [
    'controllers' => [
        'factories' => [
            'Documents\Controller\StudentBgConfig' => Factory\Controller\StudentBgConfigControllerFactory::class,
            'Documents\Controller\GeneratePdf' => Factory\Controller\GeneratePdfControllerFactory::class,
            'Documents\Controller\StudentAnswersSheets' => Factory\Controller\StudentAnswersSheetsControllerFactory::class,
        ],
    ],
    'router' => [
        'routes' => [
            'documents' => [
                'type' => Literal::class,
                'options' => [
                    'route' => '/documents',
                ],
                'may_terminate' => true,
                'child_routes' => [
                    'student-bg-config' => [
                        'type' => Segment::class,
                        'options' => [
                            'route' => '/student-bg-config[/:action[/:id]]',
                            'constraints' => [
                                'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                                'id' => '[0-9]+',
                            ],
                            'defaults' => [
                                'controller' => 'Documents\Controller\StudentBgConfig',
                                'action' => 'index',
                            ],
                        ],
                    ],
                    'generate-pdf' => [
                        'type' => Segment::class,
                        'options' => [
                            'route' => '/generate-pdf/:action[/:id]',
                            'constraints' => [
                                'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                                'id' => '[0-9]+',
                            ],
                            'defaults' => [
                                'controller' => 'Documents\Controller\GeneratePdf',
                            ],
                        ],
                    ],
                    'student-answers-sheets' => [
                        'type' => Segment::class,
                        'options' => [
                            'route' => '/student-answers-sheets/:action[/:id]',
                            'constraints' => [
                                'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                                'id' => '[0-9]+',
                            ],
                            'defaults' => [
                                'controller' => 'Documents\Controller\StudentAnswersSheets',
                            ],
                        ],
                    ],
                ],
            ],
        ],
    ],
    'view_manager' => [
        'strategies' => [
            'ViewJsonStrategy',
        ],
        'template_path_stack' => [
            __DIR__ . '/../view/',
        ],
        'template_map' => [
            'student-bg-configs/template' => __DIR__ . '/../view/templates/student-bg-configs.phtml',
            'student-bg-config-form/template' => __DIR__ . '/../view/templates/student-bg-config-form.phtml',
            'students-board-form/template' => __DIR__ . '/../view/templates/students-board-form.phtml',
            'student-id-cards/template' => __DIR__ . '/../view/templates/student-id-cards.phtml',
        ],
        'display_exceptions' => true,
    ],
    'doctrine' => [
        'driver' => [
            'documents_driver' => [
                'class' => 'Doctrine\ORM\Mapping\Driver\AnnotationDriver',
                'cache' => 'array',
                'paths' => [
                    __DIR__ . '/../src/Entity',
                ],
            ],
            'orm_default' => [
                'drivers' => [
                    'Documents\Entity' => 'documents_driver',
                ],
            ],
        ],
    ],
    'navigation' => [
        'default' => [
            [
                'label' => 'Documents',
                'uri' => '#',
                'icon' => 'fa fa-files-o',
                'order' => 8,
                'pages' => [
                    [
                        'label' => 'Show background configs',
                        'route' => 'documents/student-bg-config',
                        'action' => 'index',
                        'resource' => 'Documents\Controller\StudentBgConfig',
                        'privilege' => 'index',
                        'icon' => 'fa fa-files-o',
                        'toolbar' => [
                            [
                                'url' => '/documents/student-bg-config/edit/$id',
                                'id' => 'student-bg-config-edit',
                                'title' => 'Editar',
                                'description' => 'Permite editar a configuração de fundo selecionada',
                                'class' => 'fa fa-pencil-square-o bg-blue',
                                'fntype' => 'selectedHttpClick',
                            ],
                            [
                                'url' => '/documents/student-bg-config/delete/$id',
                                'id' => 'student-bg-config-delete',
                                'title' => 'Remover',
                                'description' => 'Remove uma configuração de fundo',
                                'class' => 'fa fa-trash-o bg-red',
                                'fntype' => 'selectedAjaxClick',
                                'hideOnSuccess' => true,
                            ],
                        ],
                    ],
                    [
                        'label' => 'Create a background config',
                        'route' => 'documents/student-bg-config',
                        'action' => 'create',
                        'resource' => 'Documents\Controller\StudentBgConfig',
                        'privilege' => 'create',
                        'icon' => 'fa fa-file-o'
                    ],
                    [
                        'label' => 'Generate Student ID Cards',
                        'route' => 'documents/generate-pdf',
                        'action' => 'student-id-card',
                        'resource' => 'Documents\Controller\GeneratePdf',
                        'privilege' => 'student-id-card',
                        'icon' => 'fa fa-file-pdf-o'
                    ],
                    [
                        'label' => 'Generate Students Board',
                        'route' => 'documents/generate-pdf',
                        'action' => 'students-board',
                        'resource' => 'Documents\Controller\GeneratePdf',
                        'privilege' => 'students-board',
                        'icon' => 'fa fa-file-pdf-o'
                    ],
                    [
                        'label' => 'Student answers sheets',
                        'route' => 'documents/student-answers-sheets',
                        'action' => 'index',
                        'resource' => 'Documents\Controller\StudentAnswersSheets',
                        'privilege' => 'index',
                        'icon' => 'fa fa-chevron-circle-down',
                    ]
                ],
            ],
        ],
    ],
];
