<?php

namespace SchoolManagement;

return array(
    'controllers' => array(
        'factories' => array(
            'SchoolManagement\Controller\SchoolAttendance' =>
            Factory\Controller\SchoolAttendanceControllerFactory::class,
            'SchoolManagement\Controller\Enrollment' => Factory\Controller\EnrollmentControllerFactory::class,
            'SchoolManagement\Controller\StudentClass' => Factory\Controller\StudentClassControllerFactory::class,
            'SchoolManagement\Controller\SchoolWarning' => Factory\Controller\SchoolWarningControllerFactory::class,
            'SchoolManagement\Controller\StudyResources' => Factory\Controller\StudyResourcesControllerFactory::class,
            'SchoolManagement\Controller\SchoolSubject' => Factory\Controller\SchoolSubjectControllerFactory::class,
            'SchoolManagement\Controller\SchoolExam' => Factory\Controller\SchoolExamControllerFactory::class,
            'SchoolManagement\Controller\SchoolExamPreview' =>
            Factory\Controller\SchoolExamPreviewControllerFactory::class,
        ),
    ),
    'router' => array(
        'routes' => array(
            'school-management' => array(
                'type' => 'Literal',
                'options' => array(
                    'route' => '/school-management',
                ),
                'may_terminate' => false,
                'child_routes' => array(
                    'enrollment' => array(
                        'type' => 'Segment',
                        'options' => array(
                            'route' => '/enrollment[/:action[/:id]]',
                            'constraints' => array(
                                'controller' => 'SchoolManagement\Controller\Enrollment',
                                'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                                'id' => '[0-9]+',
                            ),
                            'defaults' => array(
                                'controller' => 'SchoolManagement\Controller\Enrollment',
                                'action' => 'index',
                            ),
                        ),
                    ),
                    'student-class' => array(
                        'type' => 'Segment',
                        'options' => array(
                            'route' => '/student-class[/:action[/:id]]',
                            'constraints' => array(
                                'controller' => 'SchoolManagement\Controller\StudentClass',
                                'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                                'id' => '[0-9]+',
                            ),
                            'defaults' => array(
                                'controller' => 'SchoolManagement\Controller\StudentClass',
                                'action' => 'index',
                            ),
                        ),
                    ),
                    'school-warning' => array(
                        'type' => 'Segment',
                        'options' => array(
                            'route' => '/school-warning[/:action[/:sid[/:swid]]]',
                            'constraints' => array(
                                'controller' => 'SchoolManagement\Controller\SchoolWarning',
                                'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                                'sid' => '[0-9]+',
                                'swid' => '[0-9]+',
                            ),
                            'defaults' => array(
                                'controller' => 'SchoolManagement\Controller\SchoolWarning',
                                'action' => 'index',
                            ),
                        ),
                    ),
                    'school-attendance' => array(
                        'type' => 'Segment',
                        'options' => array(
                            'route' => '/school-attendance/:action[/:id]',
                            'constraints' => array(
                                'controller' => 'SchoolManagement\Controller\SchoolAttendance',
                                'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                                'id' => '[0-9]+',
                            ),
                            'defaults' => array(
                                'controller' => 'SchoolManagement\Controller\SchoolAttendance',
                            ),
                        ),
                    ),
                    'study-resources' => array(
                        'type' => 'Segment',
                        'options' => array(
                            'route' => '/study-resources[/:action]',
                            'constraints' => array(
                                'controller' => 'SchoolManagement\Controller\StudyResources',
                                'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                            ),
                            'defaults' => array(
                                'controller' => 'SchoolManagement\Controller\StudyResources',
                            ),
                        ),
                    ),
                    'school-subject' => array(
                        'type' => 'Segment',
                        'options' => array(
                            'route' => '/school-subject[/:action[/:id]]',
                            'constraints' => array(
                                'controller' => 'SchoolManagement\Controller\SchoolSubject',
                                'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                                'id' => '[0-9]+',
                            ),
                            'defaults' => array(
                                'controller' => 'SchoolManagement\Controller\SchoolSubject',
                                'action' => 'index',
                            ),
                        ),
                    ),
                    'school-exam' => array(
                        'type' => 'Segment',
                        'options' => array(
                            'route' => '/school-exam[/:action[/:id]]',
                            'constraints' => array(
                                'controller' => 'SchoolManagement\Controller\SchoolExam',
                                'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                                'id' => '[0-9]+',
                            ),
                            'defaults' => array(
                                'controller' => 'SchoolManagement\Controller\SchoolExam',
                                'action' => 'index',
                            ),
                        ),
                    ),
                    'school-exam-preview' => [
                        'type' => 'Segment',
                        'options' => [
                            'route' => '/school-exam-preview[/:action]',
                            'constraints' => [
                                'action' => 'index',
                            ],
                            'defaults' => [
                                'controller' => 'SchoolManagement\Controller\SchoolExamPreview',
                            ],
                        ],
                    ],
                ),
            ),
        ),
    ),
    'view_manager' => array(
        'strategies' => array(
            'ViewJsonStrategy',
        ),
        'template_path_stack' => array(
            __DIR__ . '/../view/',
        ),
        'template_map' => array(
            'download-csv/template' => __DIR__ . '/../view/templates/download-csv.phtml',
        ),
        'display_exceptions' => true,
    ),
    // Doctrine configuration
    'doctrine' => array(
        'driver' => array(
            'school-management_driver' => array(
                'class' => 'Doctrine\ORM\Mapping\Driver\AnnotationDriver',
                'cache' => 'array',
                'paths' => array(
                    __DIR__ . '/../src/SchoolManagement/Entity',
                ),
            ),
            'orm_default' => array(
                'drivers' => array(
                    'SchoolManagement\Entity' => 'school-management_driver',
                ),
            ),
        ),
    ),
    'navigation' => array(
        'default' => array(
            array(
                'label' => 'Class',
                'uri' => '#',
                'icon' => 'fa fa-graduation-cap',
                'order' => 9,
                'pages' => array(
                    array(
                        'label' => 'Show classes',
                        'route' => 'school-management/student-class',
                        'action' => 'index',
                        'resource' => 'SchoolManagement\Controller\StudentClass',
                        'privilege' => 'index',
                        'icon' => 'fa fa-graduation-cap',
                        'toolbar' => array(
                            array(
                                'url' => '/school-management/student-class/show-students-by-class/$id',
                                'title' => 'Ver alunos',
                                'description' => 'Exibe informações de alunos matriculados na turma escolhida',
                                'class' => 'fa fa-users bg-green',
                                'fntype' => 'selectedHttpClick',
                                'target' => '_blank',
                            ),
                            array(
                                'url' => '/school-management/school-attendance/printList/$id',
                                'title' => 'Imprimir chamada',
                                'description' => 'Permite imprimir a lista de chamada da turma selecionada',
                                'class' => 'fa fa-file-text-o bg-white',
                                'fntype' => 'selectedHttpClick',
                                'target' => '_blank',
                            ),
                            array(
                                'url' => '/school-management/student-class/delete/$id',
                                'title' => 'Remover',
                                'description' => 'Permite remover uma turma que ainda não possua alunos',
                                'class' => 'fa fa-trash-o bg-red',
                                'fntype' => 'selectedAjaxClick',
                            ),
                            [
                                'url' => '/school-management/student-class/student-board/$id',
                                'title' => 'Quadro de alunos',
                                'description' => 'Exibe os alunos matriculados (e suas fotos) na turma',
                                'class' => 'fa fa-odnoklassniki-square bg-blue',
                                'fntype' => 'selectedHttpClick',
                            ],
                        ),
                        'pages' => array(
                            array(
                                'label' => 'Print list',
                                'route' => 'school-management/school-attendance',
                                'action' => 'printList',
                                'icon' => 'fa fa-file-text-o',
                            ),
                            array(
                                'label' => 'Students',
                                'route' => 'school-management/student-class',
                                'action' => 'show-students-by-class',
                                'icon' => 'fa fa-users',
                            ),
                            [
                                'label' => 'Student board',
                                'route' => 'school-management/student-class',
                                'action' => 'student-board',
                                'icon' => 'fa fa-odnoklassniki-square',
                            ],
                        ),
                    ),
                    array(
                        'label' => 'Create a class',
                        'route' => 'school-management/student-class',
                        'action' => 'create',
                        'resource' => 'SchoolManagement\Controller\StudentClass',
                        'privilege' => 'create',
                        'icon' => 'fa fa-graduation-cap'
                    ),
                    array(
                        'label' => 'Enroll',
                        'route' => 'school-management/enrollment',
                        'action' => 'index',
                        'resource' => 'SchoolManagement\Controller\Enrollment',
                        'privilege' => 'index',
                        'icon' => 'fa fa-users',
                        'toolbar' => array(
                            array(
                                'url' => '/school-management/enrollment/enroll/$id',
                                'id' => 'fn-enroll',
                                'title' => 'Matricular',
                                'description' => 'Matricula o candidato em uma turma.',
                                'class' => 'fa fa-check bg-green',
                                'fntype' => 'ajaxPostSelectedClick',
                            ),
                        ),
                    ),
                    array(
                        'label' => 'Manage enrollments',
                        'route' => 'school-management/enrollment',
                        'action' => 'manage',
                        'resource' => 'SchoolManagement\Controller\Enrollment',
                        'privilege' => 'manage',
                        'icon' => 'fa fa-ticket',
                        'toolbar' => array(
                            array(
                                'url' => '/school-management/enrollment/unenroll/$id',
                                'id' => 'fn-unenroll',
                                'title' => 'Desmatricular',
                                'description' => 'Remove a matrícula do candidato na turma selecionada.',
                                'class' => 'fa fa-trash bg-red',
                                'fntype' => 'ajaxPostSelectedClick',
                            ),
                            array(
                                'url' => '/school-management/enrollment/close-enroll/$id',
                                'id' => 'fn-close-enroll',
                                'title' => 'Encerrar Matrícula',
                                'description' => 'Faz o encerramento da matrícula de alunos.',
                                'class' => 'fa fa-close bg-blue',
                                'fntype' => 'ajaxPostSelectedClick',
                            ),
                        ),
                    ),
                ),
            ),
            array(
                'label' => 'Warning',
                'uri' => '#',
                'icon' => 'fa fa-exclamation-triangle',
                'order' => 10,
                'resource' => 'SchoolManagement\Controller\SchoolWarning',
                'pages' => array(
                    array(
                        'label' => 'Show warnings',
                        'route' => 'school-management/school-warning',
                        'action' => 'index',
                        'icon' => 'fa fa-exclamation-triangle',
                        'toolbar' => array(
                            array(
                                'url' => '/school-management/school-warning/delete/$id',
                                'title' => 'Remover',
                                'id' => 'warning-delete',
                                'description' => 'Permite remover uma tipo de advertência.',
                                'class' => 'fa fa-trash-o bg-red',
                                'fntype' => 'selectedAjaxClick',
                                'hideOnSuccess' => true,
                            ),
                        ),
                    ),
                    array(
                        'label' => 'Create a warning',
                        'route' => 'school-management/school-warning',
                        'action' => 'create',
                        'icon' => 'fa fa-exclamation-triangle'
                    ),
                    array(
                        'label' => 'Given warnings',
                        'route' => 'school-management/school-warning',
                        'action' => 'given',
                        'icon' => 'fa fa-exclamation-triangle',
                        'toolbar' => array(
                            array(
                                'url' => '/school-management/school-warning/delete-given/$id',
                                'title' => 'Remover',
                                'id' => 'given-warning-delete',
                                'description' => 'Permite remover uma advertência dada',
                                'class' => 'fa fa-trash-o bg-red',
                                'fntype' => 'selectedAjaxClick',
                                'hideOnSuccess' => true,
                            ),
                        ),
                    ),
                    array(
                        'label' => 'Give a warning',
                        'route' => 'school-management/school-warning',
                        'action' => 'give',
                        'icon' => 'fa fa-exclamation-triangle'
                    ),
                ),
            ),
            array(
                'label' => 'Attendance',
                'uri' => '#',
                'icon' => 'fa fa-check',
                'order' => 11,
                'resource' => 'SchoolManagement\Controller\SchoolAttendance',
                'pages' => array(
                    array(
                        'label' => 'Upload lists',
                        'route' => 'school-management/school-attendance',
                        'action' => 'importList',
                        'icon' => 'fa fa-upload',
                        'toolbar' => array(
                            array(
                                'url' => '/school-management/school-attendance/save',
                                'title' => 'Enviar Lista',
                                'id' => 'attendance-list-save',
                                'description' => 'Salva ou atualiza a lista selecionada',
                                'class' => 'fa fa-upload bg-green',
                                'fntype' => 'ajaxPostClick',
                            ),
                        ),
                    ),
                    array(
                        'label' => 'Generate lists',
                        'route' => 'school-management/school-attendance',
                        'action' => 'generateList',
                        'icon' => 'fa fa-download',
                        'pages' => array(
                            array(
                                'label' => 'Download list',
                                'route' => 'school-management/school-attendance',
                                'action' => 'downloadList',
                                'icon' => 'fa fa-download',
                            ),
                        ),
                    ),
                    [
                        'label' => 'Generate lists v2',
                        'route' => 'school-management/school-attendance',
                        'action' => 'generateListV2',
                        'icon' => 'fa fa-list-alt',
                        'toolbar' => array(
                            array(
                                'url' => '/school-management/school-attendance/save',
                                'title' => 'Enviar Lista',
                                'id' => 'attendance-listv2-save',
                                'description' => 'Salva ou atualiza a lista selecionada',
                                'class' => 'fa fa-upload bg-green',
                                'fntype' => 'ajaxPostClick',
                            ),
                        ),
                    ],
                    array(
                        'label' => 'Add allowance',
                        'route' => 'school-management/school-attendance',
                        'action' => 'addAllowance',
                        'icon' => 'fa fa-thumbs-o-up',
                        'toolbar' => array(
                            array(
                                'url' => '/school-management/school-attendance/saveAllowance',
                                'title' => 'Salvar Abono',
                                'id' => 'allowance-save',
                                'description' => 'Salva os abonos escolhidos',
                                'class' => 'fa fa-hdd-o bg-green',
                                'fntype' => 'ajaxPostClick',
                            ),
                        ),
                    ),
                    array(
                        'label' => 'Edit allowance',
                        'route' => 'school-management/school-attendance',
                        'action' => 'allowance',
                        'icon' => 'fa fa-thumbs-o-up',
                        'toolbar' => array(
                            array(
                                'url' => '/school-management/school-attendance/deleteAllowance/$id',
                                'title' => 'Remover Abono',
                                'id' => 'allowance-delete',
                                'description' => 'Remove os abonos selecionados',
                                'class' => 'fa fa-close bg-red',
                                'fntype' => 'selectedAjaxClick',
                            ),
                        ),
                    ),
                    array(
                        'label' => 'Analyze',
                        'route' => 'school-management/school-attendance',
                        'action' => 'analyze',
                        'icon' => 'fa fa-calculator',
                    ),
                ),
            ),
            array(
                'label' => 'Subject',
                'uri' => '#',
                'icon' => 'fa fa-book',
                'order' => 13,
                'resource' => 'SchoolManagement\Controller\SchoolSubject',
                'pages' => array(
                    array(
                        'label' => 'Show Subjects',
                        'route' => 'school-management/school-subject',
                        'action' => 'index',
                        'icon' => 'fa fa-list-alt',
                        'toolbar' => array(
                            array(
                                'url' => '/school-management/school-subject/delete/$id',
                                'title' => 'Remover',
                                'id' => 'subject-delete',
                                'description' => 'Permite remover a disciplina selecionada',
                                'class' => 'fa fa-trash-o bg-red',
                                'fntype' => 'selectedAjaxClick',
                                'hideOnSuccess' => true,
                            ),
                            array(
                                'url' => '/school-management/school-subject/edit/$id',
                                'title' => 'Editar',
                                'id' => 'subject-edit',
                                'description' => 'Permite editar a disciplina selecionada',
                                'class' => 'fa fa-pencil-square-o bg-blue',
                                'fntype' => 'selectedHttpClick',
                            ),
                        ),
                        'pages' => array(
                            array(
                                'label' => 'Edit Subject',
                                'route' => 'school-management/school-subject',
                                'action' => 'edit',
                                'icon' => 'fa fa-list-alt',
                            ),
                        ),
                    ),
                    array(
                        'label' => 'Create Subject',
                        'route' => 'school-management/school-subject',
                        'action' => 'create',
                        'icon' => 'fa fa-list-alt',
                    ),
                ),
            ),
            array(
                'label' => 'Exam',
                'uri' => '#',
                'icon' => 'fa fa-book',
                'order' => 15,
                'resource' => 'SchoolManagement\Controller\SchoolExam',
                'pages' => array(
                    array(
                        'label' => 'Show Exam Contents',
                        'route' => 'school-management/school-exam',
                        'action' => 'contents',
                        'icon' => 'fa fa-files-o',
                        'toolbar' => array(
                            array(
                                'url' => '/school-management/school-exam/edit-content/$id',
                                'privilege' => 'edit-content',
                                'title' => 'Editar Configuração',
                                'id' => 'content-edit',
                                'description' => 'Permite editar a descrição do conteúdo e a quantidade de questões de cada disciplina do conteúdo selecionado',
                                'class' => 'fa fa-pencil-square-o bg-blue',
                                'fntype' => 'selectedHttpClick',
                                'target' => '__blank',
                            ),
                            array(
                                'url' => '/school-management/school-exam/prepare-content/$id',
                                'privilege' => 'prepare-content',
                                'title' => 'Montar',
                                'id' => 'content-prepare',
                                'description' => 'Permite selecionar questões para o conteúdo selecionado',
                                'class' => 'fa fa-check-circle bg-green',
                                'fntype' => 'selectedHttpClick',
                                'target' => '__blank',
                            ),
                            array(
                                'url' => '/school-management/school-exam/delete-content/$id',
                                'privilege' => 'delete-content',
                                'title' => 'Remover',
                                'id' => 'content-delete',
                                'description' => 'Permite remoção do conteúdo selecionado',
                                'class' => 'fa fa-trash-o bg-red',
                                'fntype' => 'selectedAjaxClick',
                                'hideOnSuccess' => true,
                            ),
                        ),
                    ),
                    array(
                        'label' => 'Create Exam Content',
                        'route' => 'school-management/school-exam',
                        'action' => 'create-content',
                        'icon' => 'fa fa-file-word-o'
                    ),
                    array(
                        'label' => 'Show Exams',
                        'route' => 'school-management/school-exam',
                        'action' => 'exams',
                        'icon' => 'fa fa-files-o',
                        'toolbar' => array(
                            array(
                                'url' => '/school-management/school-exam/edit-exam/$id',
                                'privilege' => 'edit-exam',
                                'title' => 'Editar',
                                'id' => 'exam-edit',
                                'description' => 'Permite edição da prova selecionada',
                                'class' => 'fa fa-check-circle bg-green',
                                'fntype' => 'selectedHttpClick',
                                'target' => '__blank',
                            ),
                            array(
                                'url' => '/school-management/school-exam/delete-exam/$id',
                                'privilege' => 'delete-exam',
                                'title' => 'Remover',
                                'id' => 'exam-delete',
                                'description' => 'Permite remoção da prova selecionada',
                                'class' => 'fa fa-trash-o bg-red',
                                'fntype' => 'selectedAjaxClick',
                                'hideOnSuccess' => true,
                            ),
                        ),
                    ),
                    array(
                        'label' => 'Create Exam',
                        'route' => 'school-management/school-exam',
                        'action' => 'create-exam',
                        'icon' => 'fa fa-file-text-o',
                    ),
                    array(
                        'label' => 'Show Exam Applications',
                        'route' => 'school-management/school-exam',
                        'action' => 'applications',
                        'icon' => 'fa fa-book',
                        'toolbar' => array(
                            array(
                                'url' => '/school-management/school-exam/edit-application/$id',
                                'privilege' => 'edit-application',
                                'title' => 'Editar',
                                'id' => 'application-edit',
                                'description' => 'Permite editar a aplicação de prova selecionada',
                                'class' => 'fa fa-check-circle bg-green',
                                'fntype' => 'selectedHttpClick',
                                'target' => '__blank',
                            ),
                            array(
                                'url' => '/school-management/school-exam/prepare-application/$id',
                                'privilege' => 'prepare-application',
                                'title' => 'Imprimir',
                                'id' => 'application-prepare',
                                'description' => 'Permite visualizar e imprimir a aplicação de prova selecionada',
                                'class' => 'fa fa-check-circle bg-green',
                                'fntype' => 'selectedHttpClick',
                                'target' => '__blank',
                            ),
                            array(
                                'url' => '/school-management/school-exam/delete-application/$id',
                                'privilege' => 'delete-application',
                                'title' => 'Remover',
                                'id' => 'application-delete',
                                'description' => 'Permite remoção da aplicação de prova selecionada',
                                'class' => 'fa fa-trash-o bg-red',
                                'fntype' => 'selectedAjaxClick',
                                'hideOnSuccess' => true,
                            ),
                        ),
                    ),
                    array(
                        'label' => 'Create Exam Application',
                        'route' => 'school-management/school-exam',
                        'action' => 'create-application',
                        'icon' => 'fa fa-book',
                    ),
                    
                    [
                        'label' => 'Exam result preview',
                        'route' => 'school-management/school-exam-preview',
                        'action' => 'index',
                        'icon' => 'fa fa-file-text-o',
                    ],
                ),
            ),
            array(
                'label' => 'Question Bank',
                'uri' => '#',
                'icon' => 'fa fa-database',
                'order' => 14,
                'resource' => 'SchoolManagement\Controller\SchoolExam',
                'pages' => array(
                    array(
                        'label' => 'Show Questions',
                        'route' => 'school-management/school-exam',
                        'action' => 'question',
                        'icon' => 'fa fa-question-circle',
                        'toolbar' => array(
                            array(
                                'url' => '/school-management/school-exam/edit-question/$id',
                                'privilege' => 'edit-question',
                                'title' => 'Editar',
                                'id' => 'question-edit',
                                'description' => 'Permite editar a questão selecionada',
                                'class' => 'fa fa-pencil-square-o bg-blue',
                                'fntype' => 'selectedHttpClick',
                            ),
                            array(
                                'url' => '/school-management/school-exam/delete-question/$id',
                                'privilege' => 'delete-question',
                                'title' => 'Remover',
                                'id' => 'question-delete',
                                'description' => 'Permite remoção da questão selecionada',
                                'class' => 'fa fa-trash-o bg-red',
                                'fntype' => 'selectedAjaxClick',
                                'hideOnSuccess' => true,
                            ),
                        ),
                        'pages' => array(
                            array(
                                'label' => 'Get Questions',
                                'route' => 'school-management/school-exam',
                                'action' => 'get-questions',
                                'icon' => 'fa fa-list-alt',
                            ),
                            array(
                                'label' => 'Edit Question',
                                'route' => 'school-management/school-exam',
                                'action' => 'edit-question',
                                'icon' => 'fa fa-list-alt',
                            ),
                        ),
                    ),
                    array(
                        'label' => 'Add Question',
                        'route' => 'school-management/school-exam',
                        'action' => 'add-question',
                        'icon' => 'fa fa-question-circle',
                    ),
                ),
            ),
        ),
    ),
);
