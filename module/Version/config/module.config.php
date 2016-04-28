<?php

namespace Version;

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

return array(
    'controllers' => array(
        'invokables' => array(
            'Version\Controller\VersionInfo' => Controller\VersionInfoController::class,
        ),
    ),
    'router' => array(
        'routes' => array(
            'version' => array(
                'type' => 'Literal',
                'options' => array(
                    'route' => '/version',
                ),
                'may_terminate' => false,
                'child_routes' => array(
                    'version-info' => array(
                        'type' => 'Segment',
                        'options' => array(
                            'route' => '/version-info[/:action]',
                            'constraints' => array(
                                'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                            ),
                            'defaults' => array(
                                'controller' => 'Version\Controller\VersionInfo',
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
            'Zend\View\Strategy\PhpRendererStrategy',
        ),
        'template_path_stack' => array(
            __DIR__ . '/../view/',
        ),
        'template_map' => array(
//            'profile/template' => __DIR__ . '/../view/templates/profile.phtml',
        ),
        'display_exceptions' => true,
    ),
    'navigation' => array(
        'default' => array(
            array(
                'label' => 'Version',
                'uri' => '#',
                'icon' => 'fa fa-file-code-o',
                'resource' => 'Version\Controller\VersionInfo',
                'order' => 1000,
                'pages' => array(
                    array(
                        'label' => 'Sobre',
                        'route' => 'version/version-info',
                        'action' => 'index',
                        'icon' => 'fa fa-file-text-o'
                    ),
                ),
            ),
        ),
    ),
);
