<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Recruitment;

use Zend\Router\Http\Segment;
use Zend\Router\Http\Literal;
use Recruitment\Factory\CaptchaViewFactory;

return [
    'controllers' => [
        'factories' => [
            'Recruitment\Controller\Captcha' => Factory\Controller\CaptchaControllerFactory::class,
            'Recruitment\Controller\CsvViewer' => Factory\Controller\CsvViewerControllerFactory::class,
            'Recruitment\Controller\Recruitment' => Factory\Controller\RecruitmentControllerFactory::class,
            'Recruitment\Controller\Registration' => Factory\Controller\RegistrationControllerFactory::class,
            'Recruitment\Controller\PreInterview' => Factory\Controller\PreInterviewControllerFactory::class,
            'Recruitment\Controller\Interview' => Factory\Controller\InterviewControllerFactory::class,
            'Recruitment\Controller\Address' => Factory\Controller\AddressControllerFactory::class,
        ],
    ],
    'router' => [
        'routes' => [
            'recruitment' => [
                'type' => Literal::class,
                'options' => [
                    'route' => '/recruitment',
                    'defaults' => [
                        '__NAMESPACE__' => 'Recruitment\Controller',
                        'controller' => 'Recruitment',
                        'action' => 'index',
                    ],
                ],
                'may_terminate' => true,
                'child_routes' => [
                    'recruitment' => [
                        'type' => Segment::class,
                        'options' => [
                            'route' => '/recruitment[/:action[/:id]]',
                            'constraints' => [
                                'controller' => 'Recruitment\Controller\Recruitment',
                                'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                                'id' => '[0-9]+',
                            ],
                            'defaults' => [
                                'controller' => 'Recruitment\Controller\Recruitment',
                                'action' => 'index',
                            ],
                        ],
                    ],
                    'registration' => [
                        'type' => Segment::class,
                        'options' => [
                            'route' => '/registration[/:action[/:id[/:sid]]]',
                            'constraints' => [
                                '__NAMESPACE__' => 'Recruitment\Controller',
                                'controller' => 'Registration',
                                'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                                'id' => '[0-9]+',
                                'sid' => '[0-9]+',
                            ],
                            'defaults' => [
                                'controller' => 'Recruitment\Controller\Registration',
                                'action' => 'index',
                            ],
                        ],
                    ],
                    'captcha' => [
                        'type' => Segment::class,
                        'options' => [
                            'route' => '/captcha[/:action[/:id]]',
                            'constraints' => [
                                '__NAMESPACE__' => 'Recruitment\Controller',
                                'controller' => 'Captcha',
                                'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                            ],
                            'defaults' => [
                                'controller' => 'Recruitment\Controller\Captcha',
                                'action' => 'generate',
                            ],
                        ],
                    ],
                    'pre-interview' => [
                        'type' => Segment::class,
                        'options' => [
                            'route' => '/pre-interview[/:action]',
                            'constraints' => [
                                'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                            ],
                            'defaults' => [
                                'controller' => 'Recruitment\Controller\PreInterview',
                                'action' => 'index',
                            ],
                        ],
                    ],
                    'interview' => [
                        'type' => Segment::class,
                        'options' => [
                            'route' => '/interview[/:action[/:id]]',
                            'constraints' => [
                                'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                                'id' => '[0-9]+',
                            ],
                            'defaults' => [
                                'controller' => 'Recruitment\Controller\Interview',
                            ],
                        ],
                    ],
                    'address' => [
                        'type' => Segment::class,
                        'options' => [
                            'route' => '/address[/:action]',
                            'constraints' => [
                                'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                            ],
                            'defaults' => [
                                'controller' => 'Recruitment\Controller\Address',
                            ],
                        ],
                    ],
                    'csv-viewer' => [
                        'type' => Segment::class,
                        'options' => [
                            'route' => '/csv-viewer[/:action]',
                            'constraints' => [
                                'action' => '[a-zA-Z][a-zA-Z0-9_-]*'
                            ],
                            'defaults' => [
                                'controller' => 'Recruitment\Controller\CsvViewer',
                                'action' => 'index',
                            ],
                        ],
                    ],
                ],
            ],
        ],
    ],
    'view_manager' => [
        'strategies' => [
            'ViewJsonStrategy',
            'Zend\View\Strategy\PhpRendererStrategy',
        ],
        'template_path_stack' => [
            __DIR__ . '/../view/',
        ],
        'template_map' => [
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
        ],
        'display_exceptions' => true,
    ],
    'view_helpers' => [
        'factories' => [
            'CaptchaImageViewHelper' => CaptchaViewFactory::class,
        ],
    ],
    // Doctrine configuration
    'doctrine' => [
        'driver' => [
            'recruitment_driver' => [
                'class' => 'Doctrine\ORM\Mapping\Driver\AnnotationDriver',
                'cache' => 'array',
                'paths' => [
                    __DIR__ . '/../src/Recruitment/Entity',
                ],
            ],
            'orm_default' => [
                'drivers' => [
                    'Recruitment\Entity' => 'recruitment_driver',
                ],
            ],
        ],
    ],
    'navigation' => [
        'default' => [
            [
                'label' => 'Recruitment',
                'uri' => '#',
                'icon' => 'fa fa-users',
                'order' => 6,
                'pages' => [
                    [
                        'label' => 'Show recruitments',
                        'route' => 'recruitment/recruitment',
                        'action' => 'index',
                        'resource' => 'Recruitment\Controller\Recruitment',
                        'icon' => 'fa fa-users',
                        'toolbar' => [
                            [
                                'url' => '/recruitment/recruitment/public-notice/$id',
                                'title' => 'Edital',
                                'description' => 'Ler edital',
                                'class' => 'fa fa-file-pdf-o bg-green',
                                'fntype' => 'selectedHttpClick',
                                'target' => '_blank',
                            ],
                            [
                                'url' => '/recruitment/recruitment/delete/$id',
                                'title' => 'Remover',
                                'description' => 'Remove um processo seletivo existente',
                                'class' => 'fa fa-trash-o bg-red',
                                'fntype' => 'selectedAjaxClick',
                            ],
                        ],
                    ],
                    [
                        'label' => 'Create a recruitment',
                        'route' => 'recruitment/recruitment',
                        'action' => 'create',
                        'resource' => 'Recruitment\Controller\Recruitment',
                        'privilege' => 'create',
                        'icon' => 'fa fa-user-plus'
                    ],
                    [
                        'label' => 'CSV Viewer',
                        'route' => 'recruitment/csv-viewer',
                        'action' => 'index',
                        'resource' => 'Recruitment\Controller\CsvViewer',
                        'privilege' => 'csv-viewer',
                        'icon' => 'fa fa-info-circle',
                    ],
                ],
            ],
            [
                'label' => 'Inscrições',
                'uri' => '#',
                'icon' => 'fa fa-users',
                'order' => 7,
                'pages' => [
                    [
                        'label' => 'Show student registrations',
                        'route' => 'recruitment/registration',
                        'action' => 'index',
                        'resource' => 'Recruitment\Controller\Registration',
                        'privilege' => 'index',
                        'icon' => 'fa fa-users',
                        'toolbar' => [
                            [
                                'url' => '/recruitment/interview/student/$id',
                                'title' => 'Perfil do Candidato',
                                'description' => 'Analizar Perfil do Candidato',
                                'class' => 'fa fa-file-text-o bg-blue',
                                'target' => '_blank',
                                'fntype' => 'selectedHttpClick',
                            ],
                            [
                                'url' => '/recruitment/registration/confirmation/$id',
                                'id' => 'fn-confirmation',
                                'title' => 'Confirmar',
                                'description' => 'Confirmar/Desconfirmar a inscrição do candidato.',
                                'class' => 'fa fa-check bg-green',
                                'fntype' => 'ajaxPostSelectedClick',
                            ],
                            [
                                'url' => '/recruitment/registration/exam-disapprove/$id',
                                'id' => 'fn-exam-disapprove',
                                'title' => 'Desclassificar na Prova',
                                'description' => 'Desclassifica/Retorna pra confirmado a inscrição do candidato.',
                                'class' => 'fa fa-close bg-red',
                                'fntype' => 'ajaxPostSelectedClick',
                            ],
                            [
                                'url' => '/recruitment/registration/exam-waiting-list/$id',
                                'id' => 'fn-exam-waiting-list',
                                'title' => 'Lista de Espera de Prova',
                                'description' => 'Coloca o candidato na lista de Espera da prova/retorna para confirmado.',
                                'class' => 'fa fa-tasks bg-white',
                                'fntype' => 'ajaxPostSelectedClick',
                            ],
                            [
                                'url' => '/recruitment/registration/convocation/$id',
                                'id' => 'fn-convocation',
                                'title' => 'Convocar',
                                'description' => 'Convocar/Desconvocar o candidato para a pré-entrevista.',
                                'class' => 'fa fa-users bg-blue fn-ajaxClick',
                                'fntype' => 'ajaxPostSelectedClick',
                            ],
                            [
                                'url' => '/recruitment/registration/acceptance/$id',
                                'title' => 'Aprovar Candidato',
                                'id' => 'fn-acceptance',
                                'description' => 'Aprova/remove aprovação do candidato. A aprovação é condição suficiente para a matrícula.',
                                'class' => 'fa fa-graduation-cap bg-yellow',
                                'fntype' => 'ajaxPostSelectedClick',
                            ],
                        ],
                        'pages' => [
                            [
                                'label' => 'Student',
                                'route' => 'recruitment/interview',
                                'action' => 'student',
                                'resource' => 'Recruitment\Controller\Interview',
                                'privilege' => 'student',
                                'icon' => 'fa fa-user',
                            ],
                        ],
                    ],
                    [
                        'label' => 'Show volunteer registrations',
                        'route' => 'recruitment/registration',
                        'action' => 'volunteer-registrations',
                        'resource' => 'Recruitment\Controller\Registration',
                        'privilege' => 'volunteer-registrations',
                        'icon' => 'fa fa-users',
                        'toolbar' => [
                            [
                                'url' => '/recruitment/interview/volunteer/$id',
                                'title' => 'Perfil do Candidato',
                                'description' => 'Analizar Perfil do Candidato',
                                'class' => 'fa fa-user-plus bg-navy',
                                'target' => '_blank',
                                'fntype' => 'selectedHttpClick',
                            ],
                            [
                                // const STATUSTYPE_CALLEDFOR_INTERVIEW = 1;
                                'url' => '/recruitment/registration/updateStatus/$id/1',
                                'title' => 'Convocar (entrevista)',
                                'id' => 'fn-interview-convocation',
                                'description' => 'Convocar o candidato para a entrevista na data escolhida',
                                'class' => 'fa fa-users bg-green',
                                'fntype' => 'ajaxPostSelectedClick',
                            ],
                            [
                                // const STATUSTYPE_INTERVIEW_WAITINGLIST = 4;
                                'url' => '/recruitment/registration/updateStatus/$id/4',
                                'title' => 'Lista de Espera (entrevista)',
                                'id' => 'fn-interview-waitlist',
                                'description' => '',
                                'class' => 'fa fa-file-text-o bg-blue',
                                'fntype' => 'selectedAjaxClick',
                            ],
                            [
                                // const STATUSTYPE_INTERVIEW_APPROVED = 5;
                                'url' => '/recruitment/registration/updateStatus/$id/5',
                                'title' => 'Aprovar',
                                'id' => 'fn-interview-approved',
                                'description' => 'Aprovar candidato',
                                'class' => 'fa fa-check bg-green',
                                'fntype' => 'selectedAjaxClick',
                            ],
                            [
                                // const STATUSTYPE_INTERVIEW_DISAPPROVED = 6;
                                'url' => '/recruitment/registration/updateStatus/$id/6',
                                'title' => 'Reprovar',
                                'id' => 'fn-interview-disapproved',
                                'description' => 'Reprovar Candidato',
                                'class' => 'fa fa-close bg-red',
                                'fntype' => 'selectedAjaxClick',
                            ],
                            [
                                // const STATUSTYPE_VOLUNTEER = 7;
                                'url' => '/recruitment/registration/updateStatus/$id/7',
                                'title' => 'Voluntário',
                                'id' => 'fn-interview-volunteer',
                                'description' => 'Altera a situação do candidato para voluntário regular',
                                'class' => 'fa fa-user bg-green',
                                'fntype' => 'selectedAjaxClick',
                            ],
                            [
                                // const STATUSTYPE_CALLEDFOR_TESTCLASS = 8;
                                'url' => '/recruitment/registration/updateStatus/$id/8',
                                'title' => 'Convocar (aula teste)',
                                'id' => 'fn-testclass-convocation',
                                'description' => 'Convoca o candidato para aula teste',
                                'class' => 'fa fa-graduation-cap bg-green',
                                'fntype' => 'ajaxPostSelectedClick',
                            ],
                            [
                                // const STATUSTYPE_TESTCLASS_WAITINGLIST = 10;
                                'url' => '/recruitment/registration/updateStatus/$id/10',
                                'title' => 'Lista de Espera (aula teste)',
                                'id' => 'fn-testclass-waitlist',
                                'description' => '',
                                'class' => 'fa fa-file-text-o bg-blue',
                                'fntype' => 'selectedAjaxClick',
                            ],
                            [
                                // const STATUSTYPE_CANCELED_REGISTRATION = 2;
                                'url' => '/recruitment/registration/updateStatus/$id/2',
                                'title' => 'Cancelar Inscrição',
                                'id' => 'fn-canceled-registration',
                                'description' => 'Invalida a inscrição do candidato',
                                'class' => 'fa fa-trash bg-red',
                                'fntype' => 'selectedAjaxClick',
                            ],
                        ],
                        'pages' => [
                            [
                                'label' => 'Volunteer',
                                'route' => 'recruitment/interview',
                                'action' => 'volunteer',
                                'resource' => 'Recruitment\Controller\Interview',
                                'privilege' => 'volunteer',
                                'icon' => 'fa fa-user',
                            ],
                        ],
                    ],
                    [
                        'label' => 'Formulários',
                        'route' => 'recruitment/registration',
                        'action' => 'registration-forms',
                        'resource' => 'Recruitment\Controller\Registration',
                        'privilege' => 'registration-forms',
                        'icon' => 'fa fa-file-text-o',
                    ],
                    [
                        'label' => 'Student Candidate Access',
                        'route' => 'recruitment/registration',
                        'action' => 'access',
                        'resource' => 'Recruitment\Controller\Registration',
                        'privilege' => 'access',
                        'icon' => 'fa fa-file-text-o',
                        'pages' => [
                            [
                                'label' => 'Área do Candidato',
                                'route' => 'recruitment/registration',
                                'action' => 'candidate',
                                'resource' => 'Recruitment\Controller\Registration',
                                'privilege' => 'candidate',
                                'icon' => 'fa fa-user',
                                'pages' => [
                                    [
                                        'label' => 'Resultado da Prova',
                                        'route' => 'recruitment/registration',
                                        'action' => 'exam-result',
                                        'resource' => 'Recruitment\Controller\Registration',
                                        'privilege' => 'exam-result',
                                        'icon' => 'fa fa-file-o',
                                    ]
                                ],
                            ]
                        ],
                    ],
                    [
                        'label' => 'Volunteer Candidate Access',
                        'route' => 'recruitment/registration',
                        'action' => 'volunteer-access',
                        'resource' => 'Recruitment\Controller\Registration',
                        'privilege' => 'volunteer-access',
                        'icon' => 'fa fa-file-text-o',
                        'pages' => [
                            [
                                'label' => 'Área do Candidato',
                                'route' => 'recruitment/registration',
                                'action' => 'volunteer-candidate',
                                'resource' => 'Recruitment\Controller\Registration',
                                'privilege' => 'volunteer-candidate',
                                'icon' => 'fa fa-user',
                            ]
                        ],
                    ],
                    [
                        'label' => 'Student interview',
                        'route' => 'recruitment/interview',
                        'action' => 'student-list',
                        'resource' => 'Recruitment\Controller\Interview',
                        'privilege' => 'student-list',
                        'icon' => 'fa fa-file-text-o',
                        'toolbar' => [
                            [
                                'url' => '/recruitment/registration/access',
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
                    ],
                    [
                        'label' => 'Volunteer interview',
                        'route' => 'recruitment/interview',
                        'action' => 'volunteer-list',
                        'resource' => 'Recruitment\Controller\Interview',
                        'privilege' => 'volunteer-list',
                        'icon' => 'fa fa-file-text-o',
                        'toolbar' => [
                            [
                                'url' => '/recruitment/interview/volunteer-form/$id',
                                'id' => 'fn-interview',
                                'title' => 'Entrevista',
                                'description' => 'Formulário de entrevista',
                                'class' => 'fa fa-file-text-o bg-green',
                                'fntype' => 'selectedHttpClick',
                                'target' => '_blank',
                            ],
                            [
                                'url' => '/recruitment/interview/interviewer-evaluation/$id',
                                'id' => 'fn-interviewer-evaluation',
                                'title' => 'Avaliação',
                                'description' => 'Formulário de avaliação do candidato',
                                'class' => 'fa fa-file-text-o bg-blue',
                                'fntype' => 'selectedHttpClick',
                                'target' => '_blank',
                            ],
                        ],
                        'pages' => [
                            [
                                'label' => 'Interview Form',
                                'route' => 'recruitment/interview',
                                'action' => 'volunteer-form',
                                'resource' => 'Recruitment\Controller\Interview',
                                'privilege' => 'volunteer-form',
                                'icon' => 'fa fa-file-text-o',
                            ],
                            [
                                'label' => 'Interviewer Evaluation Form',
                                'route' => 'recruitment/interview',
                                'action' => 'interviewer-evaluation',
                                'resource' => 'Recruitment\Controller\Interview',
                                'privilege' => 'interviewer-evaluation',
                                'icon' => 'fa fa-tag',
                            ]
                        ],
                    ]
                ],
            ],
        ],
    ],
];
