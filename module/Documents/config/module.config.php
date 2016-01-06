<?php

namespace Documents;

return array(
    'controllers' => array(
        'invokables' => array(
            'Documents\Controller\StudentBgConfig' => Controller\StudentBgConfigController::class,
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
        'configuration' => array(
            'orm_default' => array(
                'generate_proxies' => true,
            ),
        ),
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
                'resource' => 'Documents\Controller\StudentBgConfig',
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
                                'url' => '/documents/student-bg-config/delete/$id',
                                'title' => 'Remover',
                                'description' => 'Remove uma configuração de fundo',
                                'class' => 'fa fa-trash-o bg-red',
                                'fntype' => 'selectedAjaxClick',
                            )
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
                ),
            ),
        ),
    ),
);
