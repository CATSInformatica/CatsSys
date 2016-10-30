<?php

namespace Documents;

return array(
    'controllers' => array(
        'factories' => array(
            'Documents\Controller\StudentBgConfig' => Factory\Controller\StudentBgConfigControllerFactory::class,
            'Documents\Controller\GeneratePdf' => Factory\Controller\GeneratePdfControllerFactory::class,
            'Documents\Controller\StudentAnswersSheets' =>
            Factory\Controller\StudentAnswersSheetsControllerFactory::class,
        ),
    ),
    'router' => array(
        'routes' => array(
            'documents' => array(
                'type' => 'Literal',
                'options' => array(
                    'route' => '/documents',
                ),
                'may_terminate' => true,
                'child_routes' => array(
                    'student-bg-config' => array(
                        'type' => 'Segment',
                        'options' => array(
                            'route' => '/student-bg-config[/:action[/:id]]',
                            'constraints' => array(
                                'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                                'id' => '[0-9]+',
                            ),
                            'defaults' => array(
                                'controller' => 'Documents\Controller\StudentBgConfig',
                                'action' => 'index',
                            ),
                        ),
                    ),
                    'generate-pdf' => array(
                        'type' => 'Segment',
                        'options' => array(
                            'route' => '/generate-pdf/:action[/:id]',
                            'constraints' => array(
                                'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                                'id' => '[0-9]+',
                            ),
                            'defaults' => array(
                                'controller' => 'Documents\Controller\GeneratePdf',
                            ),
                        ),
                    ),
                    'student-answers-sheets' => [
                        'type' => 'Segment',
                        'options' => array(
                            'route' => '/student-answers-sheets/:action[/:id]',
                            'constraints' => array(
                                'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                                'id' => '[0-9]+',
                            ),
                            'defaults' => array(
                                'controller' => 'Documents\Controller\StudentAnswersSheets',
                            ),
                        ),
                    ],
                ),
            ),
        ),
    ),
    'view_manager' => array(
        'strategies' => array(
            'ViewJsonStrategy',
        ),
        'template_path_stack' => array(
            __DIR__ . '/../view/',
        ),
        'template_map' => array(
        ),
        'display_exceptions' => true,
    ),
    'doctrine' => array(
        'driver' => array(
            'documents_driver' => array(
                'class' => 'Doctrine\ORM\Mapping\Driver\AnnotationDriver',
                'cache' => 'array',
                'paths' => array(
                    __DIR__ . '/../src/Documents/Entity',
                ),
            ),
            'orm_default' => array(
                'drivers' => array(
                    'Documents\Entity' => 'documents_driver',
                ),
            ),
        ),
    ),
    'navigation' => array(
        'default' => array(
            array(
                'label' => 'Documents',
                'uri' => '#',
                'icon' => 'fa fa-files-o',
                'order' => 8,
                'pages' => array(
                    array(
                        'label' => 'Show background configs',
                        'route' => 'documents/student-bg-config',
                        'action' => 'index',
                        'resource' => 'Documents\Controller\StudentBgConfig',
                        'privilege' => 'index',
                        'icon' => 'fa fa-files-o',
                        'toolbar' => array(                                                      
                            array(
                                'url' => '/documents/student-bg-config/edit/$id',
                                'id' => 'student-bg-config-edit',
                                'title' => 'Editar',
                                'description' => 'Permite editar a configuração de fundo selecionada',
                                'class' => 'fa fa-pencil-square-o bg-blue',
                                'fntype' => 'selectedHttpClick',
                            ),
                            array(
                                'url' => '/documents/student-bg-config/delete/$id',
                                'id' => 'student-bg-config-delete',
                                'title' => 'Remover',
                                'description' => 'Remove uma configuração de fundo',
                                'class' => 'fa fa-trash-o bg-red',
                                'fntype' => 'selectedAjaxClick',
                                'hideOnSuccess' => true,
                            ),  
                        ),
                    ),
                    array(
                        'label' => 'Create a background config',
                        'route' => 'documents/student-bg-config',
                        'action' => 'create',
                        'resource' => 'Documents\Controller\StudentBgConfig',
                        'privilege' => 'create',
                        'icon' => 'fa fa-file-o'
                    ),
                    array(
                        'label' => 'Generate Student Card IDs',
                        'route' => 'documents/generate-pdf',
                        'action' => 'student-id-card',
                        'resource' => 'Documents\Controller\GeneratePdf',
                        'privilege' => 'student-id-card',
                        'icon' => 'fa fa-file-pdf-o'
                    ),
                    array(
                        'label' => 'Generate Students Board',
                        'route' => 'documents/generate-pdf',
                        'action' => 'students-board',
                        'resource' => 'Documents\Controller\GeneratePdf',
                        'privilege' => 'students-board',
                        'icon' => 'fa fa-file-pdf-o'
                    ),
                    [
                        'label' => 'Student answers sheets',
                        'route' => 'documents/student-answers-sheets',
                        'action' => 'index',
                        'resource' => 'Documents\Controller\StudentAnswersSheets',
                        'privilege' => 'index',
                        'icon' => 'fa fa-chevron-circle-down',
                    ]
                ),
            ),
        ),
    ),
);
