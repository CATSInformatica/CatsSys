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
            'Recruitment\Controller\Interview' => Controller\InterviewController::class,
            'Recruitment\Controller\CsvViewer' => Controller\CsvViewerController::class,
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
                            'route' => '/pre-interview[/:action[/:file[/:rid]]]',
                            'constraints' => array(
                                'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                                'file' => 'personal|income|expendure',
                                'rid' => '[0-9]+',
                            ),
                            'defaults' => array(
                                'controller' => 'Recruitment\Controller\PreInterview',
                                'action' => 'index',
                            ),
                        )
                    ),
                    'interview' => array(
                        'type' => 'Segment',
                        'options' => array(
                            'route' => '/interview[/:action[/:id]]',
                            'constraints' => array(
                                'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                                'id' => '[0-9]+',
                            ),
                            'defaults' => array(
                                'controller' => 'Recruitment\Controller\Interview',
                            ),
                        ),
                    ),
                    'csv-viewer' => array(
                        'type' => 'Segment',
                        'options' => array(
                            'route' => '/csv-viewer[/:action]',
                            'constraints' => array(
                                'action' => '[a-zA-Z][a-zA-Z0-9_-]*'
                            ),
                            'defaults' => array(
                                'controller' => 'Recruitment\Controller\CsvViewer',
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
            'profile/template' => __DIR__ . '/../view/templates/profile.phtml',
            'person/template' => __DIR__ . '/../view/templates/person.phtml',
            'relative/template' => __DIR__ . '/../view/templates/relative.phtml',
            'disclosure/template' => __DIR__ . '/../view/templates/disclosure.phtml',
            'address/template' => __DIR__ . '/../view/templates/address.phtml',
            'volunteer/template' => __DIR__ . '/../view/templates/volunteer.phtml',
            'pre-interview/template' => __DIR__ . '/../view/templates/pre-interview.phtml',
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
                    array(
                        'label' => 'CSV Viewer',
                        'route' => 'recruitment/csv-viewer',
                        'action' => 'index',
                        'resource' => 'Recruitment\Controller\CsvViewer',
                        'privilege' => 'index',
                        'icon' => 'fa fa-info-circle',
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
                        'label' => 'Show student registrations',
                        'route' => 'recruitment/registration',
                        'action' => 'index',
                        'resource' => 'Recruitment\Controller\Registration',
                        'privilege' => 'index',
                        'icon' => 'fa fa-users',
                        'toolbar' => array(
                            array(
                                'url' => '/recruitment/interview/student/$id',
                                'title' => 'Perfil do Candidato',
                                'description' => 'Analizar Perfil do Candidato',
                                'class' => 'fa fa-file-text-o bg-blue',
                                'target' => '_blank',
                                'fntype' => 'selectedHttpClick',
                            ),
                        ),
                        'pages' => array(
                            array(
                                'label' => 'Student',
                                'route' => 'recruitment/interview',
                                'action' => 'student',
                                'resource' => 'Recruitment\Controller\Interview',
                                'privilege' => 'student',
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
                        'label' => 'Show volunteer registrations',
                        'route' => 'recruitment/registration',
                        'action' => 'volunteer-registrations',
                        'resource' => 'Recruitment\Controller\Registration',
                        'privilege' => 'volunteer-registrations',
                        'icon' => 'fa fa-users',
                        'toolbar' => array(
                            array(
                                'url' => '/recruitment/interview/volunteer/$id',
                                'title' => 'Perfil do Candidato',
                                'description' => 'Analizar Perfil do Candidato',
                                'class' => 'fa fa-file-text-o bg-blue',
                                'target' => '_blank',
                                'fntype' => 'selectedHttpClick',
                            ),
                        ),
                        'pages' => array(
                            array(
                                'label' => 'Volunteer',
                                'route' => 'recruitment/interview',
                                'action' => 'volunteer',
                                'resource' => 'Recruitment\Controller\Interview',
                                'privilege' => 'volunteer',
                                'icon' => 'fa fa-user',
//                                'toolbar' => array(
//                                    array(
//                                        'url' => '/recruitment/registration/confirmation',
//                                        'id' => 'fn-confirmation',
//                                        'title' => 'Confirmar',
//                                        'description' => 'Confirmar/Desconfirmar a inscrição do candidato.',
//                                        'class' => 'fa fa-check bg-red',
//                                        'fntype' => 'ajaxUrlClick',
//                                    ),
//                                    array(
//                                        'url' => '/recruitment/registration/convocation',
//                                        'id' => 'fn-convocation',
//                                        'title' => 'Convocar',
//                                        'description' => 'Convocar/Desconvocar o candidato para a pré-entrevista.',
//                                        'class' => 'fa fa-users bg-blue fn-ajaxClick',
//                                        'fntype' => 'ajaxUrlClick',
//                                    ),
//                                    array(
//                                        'url' => '/recruitment/registration/acceptance',
//                                        'title' => 'Aprovar Candidato',
//                                        'id' => 'fn-acceptance',
//                                        'description' => 'Aprova/remove aprovação do candidato. A aprovação é condição suficiente para a matrícula.',
//                                        'class' => 'fa fa-graduation-cap bg-yellow',
//                                        'fntype' => 'ajaxUrlClick',
//                                    ),
//                                ),
                            ),
                        ),
                    ),
                    array(
                        'label' => 'Volunteer registration form',
                        'route' => 'recruitment/registration',
                        'action' => 'volunteerRegistration',
                        'resource' => 'Recruitment\Controller\Registration',
                        'privilege' => 'volunteerRegistration',
                        'icon' => 'fa fa-user-plus',
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
                        'icon' => 'fa fa-check',
                        'pages' => array(
                            array(
                                'label' => 'Student pre-interview II',
                                'route' => 'recruitment/pre-interview',
                                'action' => 'studentPreInterviewFiles',
                                'icon' => 'fa fa-file-pdf-o',
                                'pages' => array(
                                    array(
                                        'label' => 'Student pre-interview III',
                                        'route' => 'recruitment/pre-interview',
                                        'action' => 'studentPreInterviewForm',
                                        'icon' => 'fa fa-check-circle',
                                    ),
                                ),
                            ),
                        ),
                    ),
                ),
            ),
        ),
    ),
);
