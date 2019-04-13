<?php

namespace SchoolManagement;

use Zend\Router\Http\Segment;
use Zend\Router\Http\Literal;

return [
    'controllers' => [
        'factories' => [
            'SchoolManagement\Controller\SchoolAttendance' => Factory\Controller\SchoolAttendanceControllerFactory::class,
            'SchoolManagement\Controller\Enrollment' => Factory\Controller\EnrollmentControllerFactory::class,
            'SchoolManagement\Controller\StudentClass' => Factory\Controller\StudentClassControllerFactory::class,
            'SchoolManagement\Controller\SchoolWarning' => Factory\Controller\SchoolWarningControllerFactory::class,
            'SchoolManagement\Controller\StudyResources' => Factory\Controller\StudyResourcesControllerFactory::class,
            'SchoolManagement\Controller\SchoolSubject' => Factory\Controller\SchoolSubjectControllerFactory::class,
            'SchoolManagement\Controller\SchoolExam' => Factory\Controller\SchoolExamControllerFactory::class,
            'SchoolManagement\Controller\SchoolExamResult' => Factory\Controller\SchoolExamResultControllerFactory::class,
        ],
    ],
    'router' => [
        'routes' => [
            'school-management' => [
                'type' => Literal::class,
                'options' => [
                    'route' => '/school-management',
                ],
                'may_terminate' => false,
                'child_routes' => [
                    'enrollment' => [
                        'type' => Segment::class,
                        'options' => [
                            'route' => '/enrollment[/:action[/:id]]',
                            'constraints' => [
                                'controller' => 'SchoolManagement\Controller\Enrollment',
                                'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                                'id' => '[0-9]+',
                            ],
                            'defaults' => [
                                'controller' => 'SchoolManagement\Controller\Enrollment',
                                'action' => 'index',
                            ],
                        ],
                    ],
                    'student-class' => [
                        'type' => Segment::class,
                        'options' => [
                            'route' => '/student-class[/:action[/:id]]',
                            'constraints' => [
                                'controller' => 'SchoolManagement\Controller\StudentClass',
                                'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                                'id' => '[0-9]+',
                            ],
                            'defaults' => [
                                'controller' => 'SchoolManagement\Controller\StudentClass',
                                'action' => 'index',
                            ],
                        ],
                    ],
                    'school-warning' => [
                        'type' => Segment::class,
                        'options' => [
                            'route' => '/school-warning[/:action[/:sid[/:swid]]]',
                            'constraints' => [
                                'controller' => 'SchoolManagement\Controller\SchoolWarning',
                                'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                                'sid' => '[0-9]+',
                                'swid' => '[0-9]+',
                            ],
                            'defaults' => [
                                'controller' => 'SchoolManagement\Controller\SchoolWarning',
                                'action' => 'index',
                            ],
                        ],
                    ],
                    'school-attendance' => [
                        'type' => Segment::class,
                        'options' => [
                            'route' => '/school-attendance/:action[/:id]',
                            'constraints' => [
                                'controller' => 'SchoolManagement\Controller\SchoolAttendance',
                                'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                                'id' => '[0-9]+',
                            ],
                            'defaults' => [
                                'controller' => 'SchoolManagement\Controller\SchoolAttendance',
                            ],
                        ],
                    ],
                    'study-resources' => [
                        'type' => Segment::class,
                        'options' => [
                            'route' => '/study-resources[/:action]',
                            'constraints' => [
                                'controller' => 'SchoolManagement\Controller\StudyResources',
                                'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                            ],
                            'defaults' => [
                                'controller' => 'SchoolManagement\Controller\StudyResources',
                            ],
                        ],
                    ],
                    'school-subject' => [
                        'type' => Segment::class,
                        'options' => [
                            'route' => '/school-subject[/:action[/:id]]',
                            'constraints' => [
                                'controller' => 'SchoolManagement\Controller\SchoolSubject',
                                'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                                'id' => '[0-9]+',
                            ],
                            'defaults' => [
                                'controller' => 'SchoolManagement\Controller\SchoolSubject',
                                'action' => 'index',
                            ],
                        ],
                    ],
                    'school-exam' => [
                        'type' => Segment::class,
                        'options' => [
                            'route' => '/school-exam[/:action[/:id]]',
                            'constraints' => [
                                'controller' => 'SchoolManagement\Controller\SchoolExam',
                                'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                                'id' => '[0-9]+',
                            ],
                            'defaults' => [
                                'controller' => 'SchoolManagement\Controller\SchoolExam',
                                'action' => 'index',
                            ],
                        ],
                    ],
                    'school-exam-result' => [
                        'type' => Segment::class,
                        'options' => [
                            'route' => '/school-exam-result[/:action[/:id]]',
                            'constraints' => [
                                'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                                'id' => '[0-9]+',
                            ],
                            'defaults' => [
                                'controller' => 'SchoolManagement\Controller\SchoolExamResult',
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
        ],
        'template_path_stack' => [
            __DIR__ . '/../view/',
        ],
        'template_map' => [
            'download-csv/template' => __DIR__ . '/../view/templates/download-csv.phtml',
        ],
        'display_exceptions' => true,
    ],
    // Doctrine configuration
    'doctrine' => [
        'driver' => [
            'school-management_driver' => [
                'class' => 'Doctrine\ORM\Mapping\Driver\AnnotationDriver',
                'cache' => 'array',
                'paths' => [
                    __DIR__ . '/../src/Entity',
                ],
            ],
            'orm_default' => [
                'drivers' => [
                    'SchoolManagement\Entity' => 'school-management_driver',
                ],
            ],
        ],
    ],
    'navigation' => [
        'default' => [
            [
                'label' => 'Class',
                'uri' => '#',
                'icon' => 'fa fa-graduation-cap',
                'order' => 9,
                'pages' => [
                    [
                        'label' => 'Show classes',
                        'route' => 'school-management/student-class',
                        'action' => 'index',
                        'resource' => 'SchoolManagement\Controller\StudentClass',
                        'privilege' => 'index',
                        'icon' => 'fa fa-graduation-cap',
                        'toolbar' => [
                            [
                                'url' => '/school-management/student-class/show-students-by-class/$id',
                                'title' => 'Ver alunos',
                                'description' => 'Exibe informações de alunos matriculados na turma escolhida',
                                'class' => 'fa fa-users bg-green',
                                'fntype' => 'selectedHttpClick',
                                'target' => '_blank',
                            ],
                            [
                                'url' => '/school-management/school-attendance/printList/$id',
                                'title' => 'Imprimir chamada',
                                'description' => 'Permite imprimir a lista de chamada da turma selecionada',
                                'class' => 'fa fa-file-text-o bg-white',
                                'fntype' => 'selectedHttpClick',
                                'target' => '_blank',
                            ],
                            [
                                'url' => '/school-management/student-class/delete/$id',
                                'title' => 'Remover',
                                'description' => 'Permite remover uma turma que ainda não possua alunos',
                                'class' => 'fa fa-trash-o bg-red',
                                'fntype' => 'selectedAjaxClick',
                            ],
                            [
                                'url' => '/school-management/student-class/student-board/$id',
                                'title' => 'Quadro de alunos',
                                'description' => 'Exibe os alunos matriculados (e suas fotos) na turma',
                                'class' => 'fa fa-odnoklassniki-square bg-blue',
                                'fntype' => 'selectedHttpClick',
                            ],
                        ],
                        'pages' => [
                            [
                                'label' => 'Print list',
                                'route' => 'school-management/school-attendance',
                                'action' => 'printList',
                                'icon' => 'fa fa-file-text-o',
                            ],
                            [
                                'label' => 'Students',
                                'route' => 'school-management/student-class',
                                'action' => 'show-students-by-class',
                                'icon' => 'fa fa-users',
                            ],
                            [
                                'label' => 'Student board',
                                'route' => 'school-management/student-class',
                                'action' => 'student-board',
                                'icon' => 'fa fa-odnoklassniki-square',
                            ],
                        ],
                    ],
                    [
                        'label' => 'Create a class',
                        'route' => 'school-management/student-class',
                        'action' => 'create',
                        'resource' => 'SchoolManagement\Controller\StudentClass',
                        'privilege' => 'create',
                        'icon' => 'fa fa-graduation-cap'
                    ],
                    [
                        'label' => 'Enroll',
                        'route' => 'school-management/enrollment',
                        'action' => 'index',
                        'resource' => 'SchoolManagement\Controller\Enrollment',
                        'privilege' => 'index',
                        'icon' => 'fa fa-users',
                        'toolbar' => [
                            [
                                'url' => '/school-management/enrollment/enroll/$id',
                                'id' => 'fn-enroll',
                                'title' => 'Matricular',
                                'description' => 'Matricula o candidato em uma turma.',
                                'class' => 'fa fa-check bg-green',
                                'fntype' => 'ajaxPostSelectedClick',
                            ],
                        ],
                    ],
                    [
                        'label' => 'Manage enrollments',
                        'route' => 'school-management/enrollment',
                        'action' => 'manage',
                        'resource' => 'SchoolManagement\Controller\Enrollment',
                        'privilege' => 'manage',
                        'icon' => 'fa fa-ticket',
                        'toolbar' => [
                            [
                                'url' => '/school-management/enrollment/unenroll/$id',
                                'id' => 'fn-unenroll',
                                'title' => 'Desmatricular',
                                'description' => 'Remove a matrícula do candidato na turma selecionada.',
                                'class' => 'fa fa-trash bg-red',
                                'fntype' => 'ajaxPostSelectedClick',
                            ],
                            [
                                'url' => '/school-management/enrollment/close-enroll/$id',
                                'id' => 'fn-close-enroll',
                                'title' => 'Encerrar Matrícula',
                                'description' => 'Faz o encerramento da matrícula de alunos.',
                                'class' => 'fa fa-close bg-blue',
                                'fntype' => 'ajaxPostSelectedClick',
                            ],
                        ],
                    ],
                ],
            ],
            [
                'label' => 'Warning',
                'uri' => '#',
                'icon' => 'fa fa-exclamation-triangle',
                'order' => 10,
                'resource' => 'SchoolManagement\Controller\SchoolWarning',
                'pages' => [
                    [
                        'label' => 'Show warnings',
                        'route' => 'school-management/school-warning',
                        'action' => 'index',
                        'icon' => 'fa fa-exclamation-triangle',
                        'toolbar' => [
                            [
                                'url' => '/school-management/school-warning/delete/$id',
                                'title' => 'Remover',
                                'id' => 'warning-delete',
                                'description' => 'Permite remover uma tipo de advertência.',
                                'class' => 'fa fa-trash-o bg-red',
                                'fntype' => 'selectedAjaxClick',
                                'hideOnSuccess' => true,
                            ],
                        ],
                    ],
                    [
                        'label' => 'Create a warning',
                        'route' => 'school-management/school-warning',
                        'action' => 'create',
                        'icon' => 'fa fa-exclamation-triangle'
                    ],
                    [
                        'label' => 'Given warnings',
                        'route' => 'school-management/school-warning',
                        'action' => 'given',
                        'icon' => 'fa fa-exclamation-triangle',
                        'toolbar' => [
                            [
                                'url' => '/school-management/school-warning/delete-given/$id',
                                'title' => 'Remover',
                                'id' => 'given-warning-delete',
                                'description' => 'Permite remover uma advertência dada',
                                'class' => 'fa fa-trash-o bg-red',
                                'fntype' => 'selectedAjaxClick',
                                'hideOnSuccess' => true,
                            ],
                        ],
                    ],
                    [
                        'label' => 'Give a warning',
                        'route' => 'school-management/school-warning',
                        'action' => 'give',
                        'icon' => 'fa fa-exclamation-triangle'
                    ],
                ],
            ],
            [
                'label' => 'Attendance',
                'uri' => '#',
                'icon' => 'fa fa-check',
                'order' => 11,
                'resource' => 'SchoolManagement\Controller\SchoolAttendance',
                'pages' => [
                    [
                        'label' => 'Upload lists',
                        'route' => 'school-management/school-attendance',
                        'action' => 'importList',
                        'icon' => 'fa fa-upload',
                        'toolbar' => [
                            [
                                'url' => '/school-management/school-attendance/save',
                                'title' => 'Enviar Lista',
                                'id' => 'attendance-list-save',
                                'description' => 'Salva ou atualiza a lista selecionada',
                                'class' => 'fa fa-upload bg-green',
                                'fntype' => 'ajaxPostClick',
                            ],
                        ],
                    ],
                    [
                        'label' => 'Generate lists',
                        'route' => 'school-management/school-attendance',
                        'action' => 'generateList',
                        'icon' => 'fa fa-download',
                        'pages' => [
                            [
                                'label' => 'Download list',
                                'route' => 'school-management/school-attendance',
                                'action' => 'downloadList',
                                'icon' => 'fa fa-download',
                            ],
                        ],
                    ],
                    [
                        'label' => 'Generate lists v2',
                        'route' => 'school-management/school-attendance',
                        'action' => 'generateListV2',
                        'icon' => 'fa fa-list-alt',
                        'toolbar' => [
                            [
                                'url' => '/school-management/school-attendance/save',
                                'title' => 'Enviar Lista',
                                'id' => 'attendance-listv2-save',
                                'description' => 'Salva ou atualiza a lista selecionada',
                                'class' => 'fa fa-upload bg-green',
                                'fntype' => 'ajaxPostClick',
                            ],
                        ],
                    ],
                    [
                        'label' => 'Add allowance',
                        'route' => 'school-management/school-attendance',
                        'action' => 'addAllowance',
                        'icon' => 'fa fa-thumbs-o-up',
                        'toolbar' => [
                            [
                                'url' => '/school-management/school-attendance/saveAllowance',
                                'title' => 'Salvar Abono',
                                'id' => 'allowance-save',
                                'description' => 'Salva os abonos escolhidos',
                                'class' => 'fa fa-hdd-o bg-green',
                                'fntype' => 'ajaxPostClick',
                            ],
                        ],
                    ],
                    [
                        'label' => 'Edit allowance',
                        'route' => 'school-management/school-attendance',
                        'action' => 'allowance',
                        'icon' => 'fa fa-thumbs-o-up',
                        'toolbar' => [
                            [
                                'url' => '/school-management/school-attendance/deleteAllowance/$id',
                                'title' => 'Remover Abono',
                                'id' => 'allowance-delete',
                                'description' => 'Remove os abonos selecionados',
                                'class' => 'fa fa-close bg-red',
                                'fntype' => 'selectedAjaxClick',
                            ],
                        ],
                    ],
                    [
                        'label' => 'Analyze',
                        'route' => 'school-management/school-attendance',
                        'action' => 'analyze',
                        'icon' => 'fa fa-calculator',
                    ],
                ],
            ],
            [
                'label' => 'Subject',
                'uri' => '#',
                'icon' => 'fa fa-book',
                'order' => 13,
                'resource' => 'SchoolManagement\Controller\SchoolSubject',
                'pages' => [
                    [
                        'label' => 'Show Subjects',
                        'route' => 'school-management/school-subject',
                        'action' => 'index',
                        'icon' => 'fa fa-list-alt',
                        'toolbar' => [],
                        'pages' => [
                            [
                                'label' => 'Edit Subject',
                                'route' => 'school-management/school-subject',
                                'action' => 'edit',
                                'icon' => 'fa fa-list-alt',
                            ],
                            [
                                'label' => 'Create Subject',
                                'route' => 'school-management/school-subject',
                                'action' => 'create',
                                'icon' => 'fa fa-list-alt',
                            ],
                        ],
                    ],
                ],
            ],
            [
                'label' => 'Exam',
                'uri' => '#',
                'icon' => 'fa fa-book',
                'order' => 15,
                'resource' => 'SchoolManagement\Controller\SchoolExam',
                'pages' => [
                    [
                        'label' => 'Show Exam Contents',
                        'route' => 'school-management/school-exam',
                        'action' => 'contents',
                        'icon' => 'fa fa-files-o',
                        'toolbar' => [
                            [
                                'url' => '/school-management/school-exam/edit-content/$id',
                                'privilege' => 'edit-content',
                                'title' => 'Editar Configuração',
                                'id' => 'content-edit',
                                'description' => 'Permite editar a descrição do conteúdo e a quantidade de questões de cada disciplina do conteúdo selecionado',
                                'class' => 'fa fa-pencil-square-o bg-blue',
                                'fntype' => 'selectedHttpClick',
                                'target' => '__blank',
                            ],
                            [
                                'url' => '/school-management/school-exam/prepare-content/$id',
                                'privilege' => 'prepare-content',
                                'title' => 'Montar',
                                'id' => 'content-prepare',
                                'description' => 'Permite selecionar questões para o conteúdo selecionado',
                                'class' => 'fa fa-check-circle bg-green',
                                'fntype' => 'selectedHttpClick',
                                'target' => '__blank',
                            ],
                            [
                                'url' => '/school-management/school-exam/delete-content/$id',
                                'privilege' => 'delete-content',
                                'title' => 'Remover',
                                'id' => 'content-delete',
                                'description' => 'Permite remoção do conteúdo selecionado',
                                'class' => 'fa fa-trash-o bg-red',
                                'fntype' => 'selectedAjaxClick',
                                'hideOnSuccess' => true,
                            ],
                        ],
                    ],
                    [
                        'label' => 'Create Exam Content',
                        'route' => 'school-management/school-exam',
                        'action' => 'create-content',
                        'icon' => 'fa fa-file-word-o',
                    ],
                    [
                        'label' => 'Show Exams',
                        'route' => 'school-management/school-exam',
                        'action' => 'exams',
                        'icon' => 'fa fa-files-o',
                        'toolbar' => [
                            [
                                'url' => '/school-management/school-exam/edit-exam/$id',
                                'privilege' => 'edit-exam',
                                'title' => 'Editar',
                                'id' => 'exam-edit',
                                'description' => 'Permite edição da prova selecionada',
                                'class' => 'fa fa-check-circle bg-green',
                                'fntype' => 'selectedHttpClick',
                                'target' => '__blank',
                            ],
                            [
                                'url' => '/school-management/school-exam/delete-exam/$id',
                                'privilege' => 'delete-exam',
                                'title' => 'Remover',
                                'id' => 'exam-delete',
                                'description' => 'Permite remoção da prova selecionada',
                                'class' => 'fa fa-trash-o bg-red',
                                'fntype' => 'selectedAjaxClick',
                                'hideOnSuccess' => true,
                            ],
                        ],
                    ],
                    [
                        'label' => 'Create Exam',
                        'route' => 'school-management/school-exam',
                        'action' => 'create-exam',
                        'icon' => 'fa fa-file-text-o',
                    ],
                    [
                        'label' => 'Show Exam Applications',
                        'route' => 'school-management/school-exam',
                        'action' => 'applications',
                        'icon' => 'fa fa-book',
                        'toolbar' => [
                            [
                                'url' => '/school-management/school-exam/edit-application/$id',
                                'privilege' => 'edit-application',
                                'title' => 'Editar',
                                'id' => 'application-edit',
                                'description' => 'Permite editar a aplicação de prova selecionada',
                                'class' => 'fa fa-check-circle bg-green',
                                'fntype' => 'selectedHttpClick',
                                'target' => '__blank',
                            ],
                            [
                                'url' => '/school-management/school-exam/prepare-application/$id',
                                'privilege' => 'prepare-application',
                                'title' => 'Imprimir',
                                'id' => 'application-prepare',
                                'description' => 'Permite visualizar e imprimir a aplicação de prova selecionada',
                                'class' => 'fa fa-check-circle bg-green',
                                'fntype' => 'selectedHttpClick',
                                'target' => '__blank',
                            ],
                            [
                                'url' => '/school-management/school-exam/delete-application/$id',
                                'privilege' => 'delete-application',
                                'title' => 'Remover',
                                'id' => 'application-delete',
                                'description' => 'Permite remoção da aplicação de prova selecionada',
                                'class' => 'fa fa-trash-o bg-red',
                                'fntype' => 'selectedAjaxClick',
                                'hideOnSuccess' => true,
                            ],
                        ],
                    ],
                    [
                        'label' => 'Create Exam Application',
                        'route' => 'school-management/school-exam',
                        'action' => 'create-application',
                        'icon' => 'fa fa-book',
                    ],
                ],
            ],
            [
                'label' => 'Exam Result',
                'uri' => '#',
                'icon' => 'fa fa-file-o',
                'order' => 15,
                'resource' => 'SchoolManagement\Controller\SchoolExamResult',
                'pages' => [
                    [
                        'label' => 'Preview',
                        'route' => 'school-management/school-exam-result',
                        'action' => 'preview',
                        'icon' => 'fa fa-file-text-o',
                    ],
                    [
                        'label' => 'Answers template',
                        'route' => 'school-management/school-exam-result',
                        'action' => 'answers-template',
                        'icon' => 'fa fa-cloud-upload',
                        'toolbar' => [
                            [
                                'url' => '/school-management/school-exam-result/save-template',
                                'title' => 'Salvar',
                                'id' => 'save-template',
                                'description' => 'Salva o gabrito da prova selecionada',
                                'class' => 'fa fa-hdd-o bg-green',
                                'fntype' => 'ajaxPostClick',
                            ]
                        ],
                    ],
                    [
                        'label' => 'Upload answers (class)',
                        'route' => 'school-management/school-exam-result',
                        'action' => 'upload-answers-by-class',
                        'icon' => 'fa fa-cloud-upload',
                        'toolbar' => [
                            [
                                'url' => '/school-management/school-exam-result/save-answers',
                                'title' => 'Salvar',
                                'id' => 'save-student-answers',
                                'description' => 'Salva as respostas dos alunos selecionados',
                                'class' => 'fa fa-hdd-o bg-green',
                                'fntype' => 'ajaxPostClick',
                            ]
                        ],
                    ],
                    [
                        'label' => 'Upload answers (rec)',
                        'route' => 'school-management/school-exam-result',
                        'action' => 'upload-answers-by-std-recruitment',
                        'icon' => 'fa fa-cloud-upload',
                        'toolbar' => [
                            [
                                'url' => '/school-management/school-exam-result/save-answers',
                                'title' => 'Salvar',
                                'id' => 'save-answers',
                                'description' => 'Salva as respostas dos candidatos selecionados',
                                'class' => 'fa fa-hdd-o bg-green',
                                'fntype' => 'ajaxPostClick',
                            ]
                        ],
                    ],
                ],
            ],
            [
                'label' => 'Question Bank',
                'uri' => '#',
                'icon' => 'fa fa-database',
                'order' => 14,
                'pages' => [
                    [
                        'label' => 'Show Questions',
                        'route' => 'school-management/school-exam',
                        'action' => 'question',
                        'resource' => 'SchoolManagement\Controller\SchoolExam',
                        'privilege' => 'question',
                        'icon' => 'fa fa-question-circle',
                        'toolbar' => [
                            [
                                'url' => '/school-management/school-exam/edit-question/$id',
                                'title' => 'Editar',
                                'id' => 'question-edit',
                                'description' => 'Permite editar a questão selecionada',
                                'class' => 'fa fa-pencil-square-o bg-blue',
                                'fntype' => 'selectedHttpClick',
                            ],
                            [
                                'url' => '/school-management/school-exam/delete-question/$id',
                                'title' => 'Remover',
                                'id' => 'question-delete',
                                'description' => 'Permite remoção da questão selecionada',
                                'class' => 'fa fa-trash-o bg-red',
                                'fntype' => 'selectedAjaxClick',
                                'hideOnSuccess' => true,
                            ],
                        ],
                        'pages' => [
                            [
                                'label' => 'Get Questions',
                                'route' => 'school-management/school-exam',
                                'action' => 'get-subject-questions',
                                'icon' => 'fa fa-list-alt',
                            ],
                            [
                                'label' => 'Edit Question',
                                'route' => 'school-management/school-exam',
                                'action' => 'edit-question',
                                'icon' => 'fa fa-list-alt',
                            ],
                        ],
                    ],
                    [
                        'label' => 'Add Question',
                        'route' => 'school-management/school-exam',
                        'action' => 'add-question',
                        'resource' => 'SchoolManagement\Controller\SchoolExam',
                        'privilege' => 'add-question',
                        'icon' => 'fa fa-question-circle',
                    ],
                ],
            ],
        ],
    ],
];
