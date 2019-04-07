<?php

namespace Site;

return array(
    'controllers' => array(
        'factories' => array(
            'Site\Controller\Index' => Factory\Controller\IndexControllerFactory::class,
            'Site\Controller\SiteManagement' => Factory\Controller\SiteManagementControllerFactory::class,
        ),
    ),

    'router' => array(
        'routes' => array(
            'home' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route' => '/',
                    'defaults' => array(
                        'controller' => 'Site\Controller\Index',
                        'action' => 'index',
                    ),
                ),
            ),
            // The following is a route to simplify getting started creating
            // new controllers and actions without needing to create a new
            // module. Simply drop new controllers in, and you can access them
            // using the path /site/:controller/:action
            'site' => array(
                'type' => 'Segment',
                'options' => array(
                    'route' => '/site',
                ),
                'may_terminate' => true,
                'child_routes' => array(
                    'site-management' => array(
                        'type' => 'Segment',
                        'options' => array(
                            'route' => '/site-management[/:action[/:id]]',
                            'constraints' => array(
                                'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                                'id' => '[0-9]+',
                            ),
                            'defaults' => array(
                                'controller' => 'Site\Controller\SiteManagement',
                                'action' => 'contact'
                            ),
                        ),
                    ),

                ),
            ),
        ),
    ),
    'view_manager' => array(
        'strategies' => array(
            'ViewJsonStrategy'
        ),
        'template_path_stack' => array(
            __DIR__ . '/../view',
        ),
        'template_map' => array(
            'layout/layout' => __DIR__ . '/../view/layout/layout.phtml',
        ),
        'display_exceptions' => true,
    ),
    'view_helpers' => array(
        'invokables' => array(
            'CaptchaImageViewHelper' => 'Recruitment\View\Helper\CaptchaImage',
        ),
    ),
    // Doctrine configuration
    'doctrine' => array(
        'driver' => array(
            'site_driver' => array(
                'class' => 'Doctrine\ORM\Mapping\Driver\AnnotationDriver',
                'cache' => 'array',
                'paths' => array(
                    __DIR__ . '/../src/Site/Entity',
                ),
            ),
            'orm_default' => array(
                'drivers' => array(
                    'Site\Entity' => 'site_driver',
                ),
            ),
        ),
    ),
    // Placeholder for console routes
    'console' => array(
        'router' => array(
            'routes' => array(
            ),
        ),
    ),
    'navigation' => array(
        'default' => array(
            array(
                'label' => 'Site Management',
                'uri' => '#',
                'icon' => 'fa fa-gears',
                'resource' => 'Site\Controller\SiteManagement',
                'order' => 20,
                'pages' => array(
                    array(
                        'label' => 'Contact',
                        'route' => 'site/site-management',
                        'action' => 'contact',
                        'icon' => 'fa fa-commenting'
                    ),
                ),
            ),
        ),
    ),
);
