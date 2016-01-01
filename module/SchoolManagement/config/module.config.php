<?php

namespace SchoolManagement;

return array(
    'controllers' => array(
        'invokables' => array(
            'SchoolManagement\Controller\Enrollment' => Controller\EnrollmentController::class,
            'SchoolManagement\Controller\StudentClass' => Controller\StudentClassController::class,
            'SchoolManagement\Controller\SchoolWarning' => Controller\SchoolWarningController::class,
        )
    ),
    'router' => array(
        'routes' => array(
            'school-management' => array(
                'type' => 'Literal',
                'options' => array(
                    'route' => '/school-management',
                ),
                'may_terminate' => true,
                'child_routes' => array(
                    'enrollment' => array(
                        'type' => 'Segment',
                        'options' => array(
                            'route' => '/enrollment[/:action[/:sid[/:cid]]]',
                            'constraints' => array(
                                'controller' => 'SchoolManagement\Controller\Enrollment',
                                'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                                'sid' => '[0-9]+',
                                'cid' => '[0-9]+',
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
);
