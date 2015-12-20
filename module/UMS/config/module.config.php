<?php

namespace UMS;

return array(
    'controllers' => array(
        'invokables' => array(
            'UMS\Controller\Index' => 'UMS\Controller\IndexController',
        )
    ),
    'router' => array(
        'routes' => array(
            'ums' => array(
                'type' => 'Literal',
                'options' => array(
                    'route' => '/ums',
                    'defaults' => array(
                        'controller' => 'UMS\Controller\Index',
                        'action' => 'index',
                    ),
                ),
                'may_terminate' => true,
                'child_routes' => array(
                    'default' => array(
                        'type' => 'Segment',
                        'options' => array(
                            'route' => '/default[/:action]',
                            'constraints' => array(
                                'controller' => 'UMS\Controller\Index',
                                'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                            ),
                            'defaults' => array(
                                'action' => 'index',
                            ),
                        ),
                    ),
                ),
            ),
        ),
    ),
    'view_manager' => array(
        'template_map' => array(
            'application/layout' => __DIR__ . '/../view/layout/application-layout.phtml',
        ),
        'template_path_stack' => array(
            __DIR__ . '/../view',
        ),
    ),
    'view_helpers' => array(
        'invokables' => array(
            'userInfo' => 'UMS\View\Helper\UserInfo',
        )
    ),
    // Doctrine configuration
    'doctrine' => array(
        'configuration' => array(
            'orm_default' => array(
                'generate_proxies' => true,
            ),
        ),
        'driver' => array(
            'ums_driver' => array(
                'class' => 'Doctrine\ORM\Mapping\Driver\AnnotationDriver',
                'cache' => 'array',
                'paths' => array(
                    __DIR__ . '/../src/UMS/Entity',
                ),
            ),
            'orm_default' => array(
                'drivers' => array(
                    'UMS\Entity' => 'ums_driver',
                ),
            ),
        ),
    ),
);
