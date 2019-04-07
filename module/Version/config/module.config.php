<?php

namespace Version;

use Zend\Router\Http\Segment;
use Zend\Router\Http\Literal;

return [
    'controllers' => [
        'factories' => [
            'Version\Controller\VersionInfo' => Factory\Controller\VersionInfoControllerFactory::class,
        ],
    ],
    'router' => [
        'routes' => [
            'version' => [
                'type' => Literal::class,
                'options' => [
                    'route' => '/version',
                ],
                'may_terminate' => false,
                'child_routes' => [
                    'version-info' => [
                        'type' => Segment::class,
                        'options' => [
                            'route' => '/version-info[/:action]',
                            'constraints' => [
                                'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                            ],
                            'defaults' => [
                                'controller' => 'Version\Controller\VersionInfo',
                                'action' => 'index',
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
            'Zend\View\Strategy\PhpRendererStrategy',
        ],
        'template_path_stack' => [
            __DIR__ . '/../view/',
        ],
        'template_map' => [
//            'profile/template' => __DIR__ . '/../view/templates/profile.phtml',
        ],
        'display_exceptions' => true,
    ],
    'navigation' => [
        'default' => [
            [
                'label' => 'Version',
                'uri' => '#',
                'icon' => 'fa fa-file-code-o',
                'resource' => 'Version\Controller\VersionInfo',
                'order' => 1000,
                'pages' => [
                    [
                        'label' => 'Sobre',
                        'route' => 'version/version-info',
                        'action' => 'index',
                        'icon' => 'fa fa-file-text-o'
                    ],
                ],
            ],
        ],
    ],
];
