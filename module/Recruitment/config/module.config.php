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
            'Recruitment\Controller\Captcha' => Controller\CaptchaController::class,
            'Recruitment\Controller\CsvViewer' => Controller\CsvViewerController::class,
        ),
        'factories' => array(
            'Recruitment\Controller\Recruitment' => Factory\Controller\RecruitmentControllerFactory::class,
            'Recruitment\Controller\Registration' => Factory\Controller\RegistrationControllerFactory::class,
            'Recruitment\Controller\PreInterview' => Factory\Controller\PreInterviewControllerFactory::class,
            'Recruitment\Controller\Interview' => Factory\Controller\InterviewControllerFactory::class,
            'Recruitment\Controller\Address' => Factory\Controller\AddressControllerFactory::class,
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
                            'route' => '/registration[/:action[/:id[/:sid]]]',
                            'constraints' => array(
                                '__NAMESPACE__' => 'Recruitment\Controller',
                                'controller' => 'Registration',
                                'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                                'id' => '[0-9]+',
                                'sid' => '[0-9]+',
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
                    'address' => array(
                        'type' => 'Segment',
                        'options' => array(
                            'route' => '/address[/:action]',
                            'constraints' => array(
                                'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                            ),
                            'defaults' => array(
                                'controller' => 'Recruitment\Controller\Address',
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
            'volunteer-interview/template' => __DIR__ . '/../view/templates/volunteer-interview.phtml',
            'pre-interview/template' => __DIR__ . '/../view/templates/pre-interview.phtml',
            'registration-card/template' => __DIR__ . '/../view/templates/registration-card.phtml',
            'family-members/template' => __DIR__ . '/../view/templates/family-members.phtml',
            'family-goods/template' => __DIR__ . '/../view/templates/family-goods.phtml',
            'family-health/template' => __DIR__ . '/../view/templates/family-health.phtml',
            'family-income-expense/template' => __DIR__ . '/../view/templates/family-income-expense.phtml',
            'family-properties/template' => __DIR__ . '/../view/templates/family-properties.phtml',
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
                                'url' => '/recruitment/recruitment/public-notice/$id',
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
                'label' => 'Inscrições',
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
                            array(
                                'url' => '/recruitment/registration/confirmation/$id',
                                'id' => 'fn-confirmation',
                                'title' => 'Confirmar',
                                'description' => 'Confirmar/Desconfirmar a inscrição do candidato.',
                                'class' => 'fa fa-check bg-red',
                                'fntype' => 'ajaxPostSelectedClick',
                            ),
                            array(
                                'url' => '/recruitment/registration/convocation/$id',
                                'id' => 'fn-convocation',
                                'title' => 'Convocar',
                                'description' => 'Convocar/Desconvocar o candidato para a pré-entrevista.',
                                'class' => 'fa fa-users bg-blue fn-ajaxClick',
                                'fntype' => 'ajaxPostSelectedClick',
                            ),
                            array(
                                'url' => '/recruitment/registration/acceptance/$id',
                                'title' => 'Aprovar Candidato',
                                'id' => 'fn-acceptance',
                                'description' => 'Aprova/remove aprovação do candidato. A aprovação é condição suficiente para a matrícula.',
                                'class' => 'fa fa-graduation-cap bg-yellow',
                                'fntype' => 'ajaxPostSelectedClick',
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
                                'class' => 'fa fa-user-plus bg-navy',
                                'target' => '_blank',
                                'fntype' => 'selectedHttpClick',
                            ),
                            array(
                                // const STATUSTYPE_CALLEDFOR_INTERVIEW = 1;
                                'url' => '/recruitment/registration/updateStatus/$id/1',
                                'title' => 'Convocar (entrevista)',
                                'id' => 'fn-interview-convocation',
                                'description' => 'Convocar o candidato para a entrevista na data escolhida',
                                'class' => 'fa fa-users bg-green',
                                'fntype' => 'ajaxPostSelectedClick',
                            ),
                            array(
                                // const STATUSTYPE_INTERVIEW_WAITINGLIST = 4;
                                'url' => '/recruitment/registration/updateStatus/$id/4',
                                'title' => 'Lista de Espera (entrevista)',
                                'id' => 'fn-interview-waitlist',
                                'description' => '',
                                'class' => 'fa fa-file-text-o bg-blue',
                                'fntype' => 'selectedAjaxClick',
                            ),
                            array(
                                // const STATUSTYPE_INTERVIEW_APPROVED = 5;
                                'url' => '/recruitment/registration/updateStatus/$id/5',
                                'title' => 'Aprovar',
                                'id' => 'fn-interview-approved',
                                'description' => 'Aprovar candidato',
                                'class' => 'fa fa-check bg-green',
                                'fntype' => 'selectedAjaxClick',
                            ),
                            array(
                                // const STATUSTYPE_INTERVIEW_DISAPPROVED = 6;
                                'url' => '/recruitment/registration/updateStatus/$id/6',
                                'title' => 'Reprovar',
                                'id' => 'fn-interview-disapproved',
                                'description' => 'Reprovar Candidato',
                                'class' => 'fa fa-close bg-red',
                                'fntype' => 'selectedAjaxClick',
                            ),
                            array(
                                // const STATUSTYPE_VOLUNTEER = 7;
                                'url' => '/recruitment/registration/updateStatus/$id/7',
                                'title' => 'Voluntário',
                                'id' => 'fn-interview-volunteer',
                                'description' => 'Altera a situação do candidato para voluntário regular',
                                'class' => 'fa fa-user bg-green',
                                'fntype' => 'selectedAjaxClick',
                            ),
                            array(
                                // const STATUSTYPE_CALLEDFOR_TESTCLASS = 8;
                                'url' => '/recruitment/registration/updateStatus/$id/8',
                                'title' => 'Convocar (aula teste)',
                                'id' => 'fn-testclass-convocation',
                                'description' => 'Convoca o candidato para aula teste',
                                'class' => 'fa fa-graduation-cap bg-green',
                                'fntype' => 'ajaxPostSelectedClick',
                            ),
                            array(
                                // const STATUSTYPE_TESTCLASS_WAITINGLIST = 10;
                                'url' => '/recruitment/registration/updateStatus/$id/10',
                                'title' => 'Lista de Espera (aula teste)',
                                'id' => 'fn-testclass-waitlist',
                                'description' => '',
                                'class' => 'fa fa-file-text-o bg-blue',
                                'fntype' => 'selectedAjaxClick',
                            ),
                            array(
                                // const STATUSTYPE_CANCELED_REGISTRATION = 2;
                                'url' => '/recruitment/registration/updateStatus/$id/2',
                                'title' => 'Cancelar Inscrição',
                                'id' => 'fn-canceled-registration',
                                'description' => 'Invalida a inscrição do candidato',
                                'class' => 'fa fa-trash bg-red',
                                'fntype' => 'selectedAjaxClick',
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
                            ),
                        ),
                    ),
                    array(
                        'label' => 'Formulários',
                        'route' => 'recruitment/registration',
                        'action' => 'registration-forms',
                        'resource' => 'Recruitment\Controller\Registration',
                        'privilege' => 'registration-forms',
                        'icon' => 'fa fa-file-text-o',
                    ),
                    array(
                        'label' => 'Student pre-interview I',
                        'route' => 'recruitment/pre-interview',
                        'action' => 'index',
                        'resource' => 'Recruitment\Controller\PreInterview',
                        'privilege' => 'index',
                        'icon' => 'fa fa-check',
                        'target' => '_blank',
                        'pages' => array(
                            array(
                                'label' => 'Student pre-interview II',
                                'route' => 'recruitment/pre-interview',
                                'action' => 'studentPreInterviewForm',
                                'icon' => 'fa fa-check-circle',
                            ),
                        ),
                    ),
                    [
                        'label' => 'Student interview',
                        'route' => 'recruitment/interview',
                        'action' => 'student-list',
                        'resource' => 'Recruitment\Controller\Interview',
                        'privilege' => 'student-list',
                        'icon' => 'fa fa-file-text-o',
                        'toolbar' => [
                            [
                                'url' => '/recruitment/pre-interview',
                                'id' => 'fn-pre-interview',
                                'title' => 'Inscrição/Pré-entrevista',
                                'description' => 'Permite editar informações de inscrição e pré-entrevista',
                                'class' => 'fa fa-user bg-blue',
                                'fntype' => 'httpClick',
                                'target' => '_blank',
                            ],
                            [
                                'url' => '/recruitment/interview/student-form/$id',
                                'id' => 'fn-interview',
                                'title' => 'Entrevista',
                                'description' => 'Formulário de entrevista',
                                'class' => 'fa fa-file-text-o bg-green',
                                'fntype' => 'selectedHttpClick',
                                'target' => '_blank',
                            ],
                        ],
                        'pages' => [
                            [
                                'label' => 'Interview form',
                                'route' => 'recruitment/interview',
                                'action' => 'student-form',
                                'resource' => 'Recruitment\Controller\Interview',
                                'privilege' => 'student-form',
                                'icon' => 'fa fa-file-text-o',
                            ]
                        ],
                    ]
                ),
            ),
        ),
    ),
);
