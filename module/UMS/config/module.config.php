<?php

namespace UMS;

use UMS\Factory\UserInfoViewFactory;
use Zend\Router\Http\Segment;
use Zend\Router\Http\Literal;

return [
    'controllers' => [
        'factories' => [
            'UMS\Controller\Index' => Factory\Controller\IndexControllerFactory::class,
        ],
    ],
    'router' => [
        'routes' => [
            'ums' => [
                'type' => Literal::class,
                'options' => [
                    'route' => '/ums',
                    'defaults' => [
                        'controller' => 'UMS\Controller\Index',
                        'action' => 'index',
                    ],
                ],
                'may_terminate' => true,
                'child_routes' => [
                    'default' => [
                        'type' => Segment::class,
                        'options' => [
                            'route' => '/default[/:action]',
                            'constraints' => [
                                'controller' => 'UMS\Controller\Index',
                                'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                            ],
                            'defaults' => [
                                'action' => 'index',
                            ],
                        ],
                    ],
                ],
            ],
        ],
    ],
    'view_manager' => [
        'display_not_found_reason' => true,
        'display_exceptions' => true,
        'doctype' => 'HTML5',
        'not_found_template' => 'error/404',
        'exception_template' => 'error/index',
        'template_map' => [
            'application/layout' => __DIR__ . '/../view/layout/application-layout.phtml',
            'application-clean/layout' => __DIR__ . '/../view/layout/application-clean-layout.phtml',
            'menu/template' => __DIR__ . '/../view/templates/menu.phtml',
            'header/template' => __DIR__ . '/../view/templates/header.phtml',
            'toolbar/template' => __DIR__ . '/../view/templates/toolbar.phtml',
            'error/404' => __DIR__ . '/../view/error/404.phtml',
            'error/index' => __DIR__ . '/../view/error/index.phtml',
        ],
        'template_path_stack' => [
            __DIR__ . '/../view',
        ],
    ],
    'view_helpers' => [
        'aliases' => [
            'navigation' => Zend\View\Helper\Navigation::class,
        ],
        'factories' => [
            'userInfo' => UserInfoViewFactory::class,
            Zend\View\Helper\Navigation::class => 'UMS\Factory\NavigationViewFactory',
        ],
    ],
    'service_manager' => [
        'abstract_factories' => [
            'Zend\Cache\Service\StorageCacheAbstractServiceFactory',
            'Zend\Log\LoggerAbstractServiceFactory',
        ],
        'factories' => [
            'translator' => 'Zend\I18n\Translator\TranslatorServiceFactory',
            'navigation' => 'Zend\Navigation\Service\DefaultNavigationFactory',
        ],
    ],
    'translator' => [
        'locale' => 'pt_BR',
        'translation_file_patterns' => [
            [
                'type' => 'gettext',
                'base_dir' => __DIR__ . '/../language',
                'pattern' => '%s.mo',
            ],
        ],
    ],
    'navigation' => [
        'default' => [
            [
                'label' => 'Home',
                'route' => 'ums',
                'resource' => 'UMS\Controller\Index',
                'privilege' => 'index',
                'icon' => 'glyphicon glyphicon-home',
                'order' => 1,
            ],
        ],
    ],
];
