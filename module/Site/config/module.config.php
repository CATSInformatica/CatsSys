<?php

namespace Site;

use Zend\Router\Http\Segment;
use Zend\Router\Http\Literal;

return [
    'controllers' => [
        'factories' => [
            'Site\Controller\Index' => Factory\Controller\IndexControllerFactory::class,
            'Site\Controller\SiteManagement' => Factory\Controller\SiteManagementControllerFactory::class,
        ],
    ],

    'router' => [
        'routes' => [
            'home' => [
                'type' => Literal::class,
                'options' => [
                    'route' => '/',
                    'defaults' => [
                        'controller' => 'Site\Controller\Index',
                        'action' => 'index',
                    ],
                ],
            ],
            // The following is a route to simplify getting started creating
            // new controllers and actions without needing to create a new
            // module. Simply drop new controllers in, and you can access them
            // using the path /site/:controller/:action
            'site' => [
                'type' => Segment::class,
                'options' => [
                    'route' => '/site',
                ],
                'may_terminate' => true,
                'child_routes' => [
                    'site-management' => [
                        'type' => Segment::class,
                        'options' => [
                            'route' => '/site-management[/:action[/:id]]',
                            'constraints' => [
                                'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                                'id' => '[0-9]+',
                            ],
                            'defaults' => [
                                'controller' => 'Site\Controller\SiteManagement',
                                'action' => 'contact'
                            ],
                        ],
                    ],

                ],
            ],
        ],
    ],
    'view_manager' => [
        'strategies' => [
            'ViewJsonStrategy'
        ],
        'template_path_stack' => [
            __DIR__ . '/../view',
        ],
        'template_map' => [
            'layout/layout' => __DIR__ . '/../view/layout/layout.phtml',
        ],
        'display_exceptions' => true,
    ],
    // Doctrine configuration
    'doctrine' => [
        'driver' => [
            'site_driver' => [
                'class' => 'Doctrine\ORM\Mapping\Driver\AnnotationDriver',
                'cache' => 'array',
                'paths' => [
                    __DIR__ . '/../src/Site/Entity',
                ],
            ],
            'orm_default' => [
                'drivers' => [
                    'Site\Entity' => 'site_driver',
                ],
            ],
        ],
    ],
    // Placeholder for console routes
    'console' => [
        'router' => [
            'routes' => [
            ],
        ],
    ],
    'navigation' => [
        'default' => [
            [
                'label' => 'Site Management',
                'uri' => '#',
                'icon' => 'fa fa-gears',
                'resource' => 'Site\Controller\SiteManagement',
                'order' => 20,
                'pages' => [
                    [
                        'label' => 'Contact',
                        'route' => 'site/site-management',
                        'action' => 'contact',
                        'icon' => 'fa fa-commenting'
                    ],
                ],
            ],
        ],
    ],
];
