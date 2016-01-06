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
            'Recruitment\Controller\PreInterview' => Controller\PreInterviewController::class,
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
                    'pre-interview' => array(
                        'type' => 'Segment',
                        'options' => array(
                            'route' => '/pre-interview[/:action]',
                            'constraints' => array(
                                'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                            ),
                            'defaults' => array(
                                'controller' => 'Recruitment\Controller\PreInterview',
                                'action' => 'index',
                            ),
                        )
                    )
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
                        'action' => 'index',
                        'resource' => 'Recruitment\Controller\Recruitment',
                        'icon' => 'fa fa-users',
                        'toolbar' => array(
                            array(
                                'url' => '/recruitment/recruitment/edital/$id',
                                'title' => 'Edital',
                                'description' => 'Ler edital',
                                'class' => 'fa fa-file-pdf-o bg-green',
                                'fntype' => 'selectedHttpClick',
                                'target' => '_blank',
                            ),
                            array(
                                'url' => '/recruitment/recruitment/delete/$id',
                                'title' => 'Remover',
                                'description' => 'Remove um processo seletivo existente',
                                'class' => 'fa fa-trash-o bg-red',
                                'fntype' => 'selectedAjaxClick',
                            ),
                        ),
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
                        'action' => 'index',
                        'resource' => 'Recruitment\Controller\Registration',
                        'privilege' => 'index',
                        'icon' => 'fa fa-users',
                        'toolbar' => array(
                            array(
                                'url' => '/recruitment/registration/studentProfile/$id',
                                'title' => 'Perfil do Candidato',
                                'description' => 'Analizar Perfil do Candidato',
                                'class' => 'fa fa-file-text-o bg-blue',
                                'target' => '_blank',
                                'fntype' => 'selectedHttpClick',
                            ),
                        ),
                        'pages' => array(
                            array(
                                'label' => 'Student profile',
                                'route' => 'recruitment/registration',
                                'action' => 'studentProfile',
                                'resource' => 'Recruitment\Controller\Registration',
                                'privilege' => 'studentProfile',
                                'icon' => 'fa fa-user',
                                'toolbar' => array(
                                    array(
                                        'url' => '/recruitment/registration/confirmation',
                                        'id' => 'fn-confirmation',
                                        'title' => 'Confirmar',
                                        'description' => 'Confirmar/Desconfirmar a inscrição do candidato.',
                                        'class' => 'fa fa-check bg-red',
                                        'fntype' => 'ajaxUrlClick',
                                    ),
                                    array(
                                        'url' => '/recruitment/registration/convocation',
                                        'id' => 'fn-convocation',
                                        'title' => 'Convocar',
                                        'description' => 'Convocar/Desconvocar o candidato para a pré-entrevista.',
                                        'class' => 'fa fa-users bg-blue fn-ajaxClick',
                                        'fntype' => 'ajaxUrlClick',
                                    ),
                                    array(
                                        'url' => '/recruitment/registration/acceptance',
                                        'title' => 'Aprovar Candidato',
                                        'id' => 'fn-acceptance',
                                        'description' => 'Aprova/remove aprovação do candidato. A aprovação é condição suficiente para a matrícula.',
                                        'class' => 'fa fa-graduation-cap bg-yellow',
                                        'fntype' => 'ajaxUrlClick',
                                    ),
                                ),
                            ),
                        ),
                    ),
                    array(
                        'label' => 'Student registration form',
                        'route' => 'recruitment/registration',
                        'action' => 'studentRegistration',
                        'resource' => 'Recruitment\Controller\Registration',
                        'privilege' => 'studentRegistration',
                        'icon' => 'fa fa-user-plus',
                    ),
                    array(
                        'label' => 'Student pre-interview I',
                        'route' => 'recruitment/pre-interview',
                        'action' => 'index',
                        'resource' => 'Recruitment\Controller\PreInterview',
                        'privilege' => 'index',
                        'icon' => 'fa fa-female',
                        'pages' => array(
                            array(
                                'label' => 'Student pre-interview II',
                                'route' => 'recruitment/pre-interview',
                                'action' => 'studentPreInterview',
                                'resource' => 'Recruitment\Controller\PreInterview',
                                'privilege' => 'studentPreInterview',
                                'icon' => 'fa fa-female',
                            )
                        )
                    ),
                ),
            ),
        ),
    ),
);
