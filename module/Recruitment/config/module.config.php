<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Recruitment;

return array(
    'controllers' => array(
        'invokables' => array(
            'Recruitment\Controller\Recruitment' => Controller\RecruitmentController::class,
            'Recruitment\Controller\Registration' => Controller\RegistrationController::class,
            'Recruitment\Controller\Captcha' => Controller\CaptchaController::class,
        ),
    ),
    'router' => array(
        'routes' => array(
            'recruitment' => array(
                'type' => 'Literal',
                'options' => array(
                    'route' => '/recruitment',
                    'defaults' => array(
                        '__NAMESPACE__' => 'Recruitment\Controller',
                        'controller' => 'Recruitment',
                        'action' => 'index',
                    ),
                ),
                'may_terminate' => true,
                'child_routes' => array(
                    'recruitment' => array(
                        'type' => 'Segment',
                        'options' => array(
                            'route' => '/recruitment[/:action[/:id]]',
                            'constraints' => array(
                                'controller' => 'Recruitment\Controller\Recruitment',
                                'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                                'id' => '[0-9]+',
                            ),
                            'defaults' => array(
                                'controller' => 'Recruitment\Controller\Recruitment',
                                'action' => 'index',
                            ),
                        ),
                    ),
                    'registration' => array(
                        'type' => 'Segment',
                        'options' => array(
                            'route' => '/registration[/:action[/:id]]',
                            'constraints' => array(
                                '__NAMESPACE__' => 'Recruitment\Controller',
                                'controller' => 'Registration',
                                'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                                'id' => '[0-9]+',
                            ),
                            'defaults' => array(
                                'controller' => 'Recruitment\Controller\Registration',
                                'action' => 'index',
                            ),
                        )
                    ),
                    'captcha' => array(
                        'type' => 'Segment',
                        'options' => array(
                            'route' => '/captcha[/:action[/:id]]',
                            'constraints' => array(
                                '__NAMESPACE__' => 'Recruitment\Controller',
                                'controller' => 'Captcha',
                                'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                            ),
                            'defaults' => array(
                                'controller' => 'Recruitment\Controller\Captcha',
                                'action' => 'generate',
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
            'profile/template' => __DIR__ . '/../view/templates/profile.phtml',
        ),
        'display_exceptions' => true,
    ),
    // Doctrine configuration
    'doctrine' => array(
        'configuration' => array(
            'orm_default' => array(
                'generate_proxies' => true,
            ),
        ),
        'driver' => array(
            'recruitment_driver' => array(
                'class' => 'Doctrine\ORM\Mapping\Driver\AnnotationDriver',
                'cache' => 'array',
                'paths' => array(
                    __DIR__ . '/../src/Recruitment/Entity',
                ),
            ),
            'orm_default' => array(
                'drivers' => array(
                    'Recruitment\Entity' => 'recruitment_driver',
                ),
            ),
        ),
    ),
    'navigation' => array(
        'default' => array(
            array(
                'label' => 'Recruitment',
                'uri' => '#',
                'icon' => 'fa fa-users',
                'order' => 6,
                'resource' => 'Recruitment\Controller\Recruitment',
                'pages' => array(
                    array(
                        'label' => 'Show recruitments',
                        'route' => 'recruitment/recruitment',
                        'resource' => 'Recruitment\Controller\Recruitment',
                        'privilege' => 'index',
                        'icon' => 'fa fa-users'
                    ),
                    array(
                        'label' => 'Create a recruitment',
                        'route' => 'recruitment/recruitment',
                        'action' => 'create',
                        'resource' => 'Recruitment\Controller\Recruitment',
                        'privilege' => 'create',
                        'icon' => 'fa fa-user-plus'
                    ),
                ),
            ),
            array(
                'label' => 'Registration',
                'uri' => '#',
                'icon' => 'fa fa-users',
                'order' => 7,
                'pages' => array(
                    array(
                        'label' => 'Show registrations',
                        'route' => 'recruitment/registration',
                        'resource' => 'Recruitment\Controller\Registration',
                        'privilege' => 'index',
                        'icon' => 'fa fa-users'
                    ),
                    array(
                        'label' => 'Student registration form',
                        'route' => 'recruitment/registration',
                        'action' => 'studentRegistration',
                        'resource' => 'Recruitment\Controller\Registration',
                        'privilege' => 'studentRegistration',
                        'icon' => 'fa fa-user-plus',
                        'target' => '_blank',
                    ),
                ),
            ),
        ),
    ),
);
