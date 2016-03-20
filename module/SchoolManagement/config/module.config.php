<?php

namespace SchoolManagement;

return array(
    'controllers' => array(
//        'invokables' => array(
//        ),
        'factories' => array(
            'SchoolManagement\Controller\SchoolAttendance' =>
            Factory\Controller\SchoolAttendanceControllerFactory::class,
            'SchoolManagement\Controller\Enrollment' => Factory\Controller\EnrollmentControllerFactory::class,
            'SchoolManagement\Controller\StudentClass' => Factory\Controller\StudentClassControllerFactory::class,
            'SchoolManagement\Controller\SchoolWarning' => Factory\Controller\SchoolWarningControllerFactory::class,
            'SchoolManagement\Controller\StudyResources' => Factory\Controller\StudyResourcesControllerFactory::class,
            'SchoolManagement\Controller\SchoolSubject' => Factory\Controller\SchoolSubjectControllerFactory::class,
            'SchoolManagement\Controller\SchoolExam' => Factory\Controller\SchoolExamControllerFactory::class,
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
                                'url' => '/school-management/student-class/delete/$id',
                                'title' => 'Remover',
                                'description' => 'Permite remover uma turma que ainda não possua alunos',
                                'class' => 'fa fa-trash-o bg-red',
                                'fntype' => 'selectedAjaxClick',
                            ),
                            array(
                                'url' => '/school-management/school-attendance/printList/$id',
                                'title' => 'Imprimir chamada',
                                'description' => 'Permite imprimir a lista de chamada da turma selecionada',
                                'class' => 'fa fa-file-text-o bg-white',
                                'fntype' => 'selectedHttpClick',
                                'target' => '_blank',
                            ),
                        ),
                        'pages' => array(
                            array(
                                'label' => 'Print list',
                                'route' => 'school-management/school-attendance',
                                'action' => 'printList',
                                'icon' => 'fa fa-file-text-o',
                            ),
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
//                            array(
//                                'url' => '/recruitment/interview/student/$id',
//                                'title' => 'Perfil do Candidato',
//                                'description' => 'Analizar Perfil do Candidato',
//                                'class' => 'fa fa-file-text-o bg-blue',
//                                'target' => '_blank',
//                                'fntype' => 'selectedHttpClick',
//                            ),
                            array(
                                'url' => '/school-management/enrollment/enroll/$id',
                                'id' => 'fn-enroll',
                                'title' => 'Matricular',
                                'description' => 'Matricula o candidato em uma turma.',
                                'class' => 'fa fa-check bg-blue',
                                'fntype' => 'ajaxPostSelectedClick',
                            ),
                            array(
                                'url' => '/school-management/enrollment/unenroll/$id',
                                'id' => 'fn-unenroll',
                                'title' => 'Desmatricular',
                                'description' => 'Remove a matrícula do candidato na turma selecionada.',
                                'class' => 'fa fa-close bg-red',
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
//                        'toolbar' => array(
//                            array(
//                                'url' => '/school-management/school-attendance/deleteAllowance/$id',
//                                'title' => 'Remover Abono',
//                                'id' => 'allowance-delete',
//                                'description' => 'Remove os abonos selecionados',
//                                'class' => 'fa fa-close bg-red',
//                                'fntype' => 'selectedAjaxClick',
//                            ),
//                        ),
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
                'order' => 14,
                'resource' => 'SchoolManagement\Controller\SchoolExam',
                'pages' => array(
                    array(
                        'label' => 'Show Exams',
                        'route' => 'school-management/school-exam',
                        'action' => 'index',
                        'icon' => 'fa fa-list-alt',
                    ),
                    array(
                        'label' => 'Show Questions',
                        'route' => 'school-management/school-exam',
                        'action' => 'question',
                        'icon' => 'fa fa-question-circle',
                        'toolbar' => array(
                            array(
                                'url' => '/school-management/school-exam/edit-question/$id',
                                'title' => 'Editar',
                                'id' => 'question-edit',
                                'description' => 'Permite editar a questão selecionada',
                                'class' => 'fa fa-pencil-square-o bg-blue',
                                'fntype' => 'selectedHttpClick',
                            ),
                            array(
                                'url' => '/school-management/school-exam/delete-question/$id',
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
