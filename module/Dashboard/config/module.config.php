<?php

namespace Dashboard;

return array(
    'controllers' => array(
        'invokables' => array(
            'Dashboard\Controller\Index' => 'Dashboard\Controller\IndexController',
            'Dashboard\Controller\User' => 'Dashboard\Controller\UserController',
        )
    ),
    'router' => array(
        'routes' => array(
            'dashboard' => array(
                'type' => 'Literal',
                'options' => array(
                    'route' => '/dashboard',
                    'defaults' => array(
                        '__NAMESPACE__' => 'Dashboard\Controller',
                        'controller' => 'index',
                        'action' => 'index',
                    ),
                ),
                'may_terminate' => true,
                'child_routes' => array(
                    'default' => array(
                        'type' => 'Segment',
                        'options' => array(
                            'route' => '/[:controller[/:action[/:id]]]',
                            'constraints' => array(
                                'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
                                'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                                'id' => '[0-9][0-9]*',
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
        'template_path_stack' => array(
            'dashboard' => __DIR__ . '/../view',
        ),
        'display_exceptions' => true,
    ),
);
