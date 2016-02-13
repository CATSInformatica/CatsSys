<?php

namespace AdministrativeStructure;

return array(
    'controllers' => array(
        'invokables' => array(
            'AdministrativeStructure\Controller\Department' => Controller\DepartmentController::class,
        ),
    ),
    'router' => array(
        'routes' => array(
            'administrative-structure' => array(
                'type' => 'Literal',
                'options' => array(
                    'route' => '/administrative-structure',
                ),
                'may_terminate' => false,
                'child_routes' => array(
                    'department' => array(
                        'type' => 'Segment',
                        'options' => array(
                            'route' => '/department[/:action[/:id]]',
                            'constraints' => array(
                                'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                                'id' => '[0-9]+',
                            ),
                            'defaults' => array(
                                'controller' => 'AdministrativeStructure\Controller\Department',
                                'action' => 'index',
                            ),
                        ),
                    ),
                ),
            ),
        ),
    ),
    'view_manager' => array(
        'template_map' => array(
        ),
        'template_path_stack' => array(
            __DIR__ . '/../view/',
        ),
        'display_exceptions' => true,
    ),
    'navigation' => array(
        'default' => array(
            array(
                'label' => 'Administrative structure',
                'uri' => '#',
                'icon' => 'fa fa-university',
                'order' => 13,
                'pages' => array(
                    array(
                        'label' => 'Show departments',
                        'route' => 'administrative-structure/department',
                        'action' => 'index',
                        'resource' => 'AdministrativeStructure\Controller\Department',
                        'privilege' => 'index',
                        'icon' => 'fa fa-folder-open',
                        'pages' => array(
                            array(
                                'label' => 'Edit a department',
                                'route' => 'administrative-structure/department',
                                'action' => 'edit',
                                'resource' => 'AdministrativeStructure\Controller\Department',
                                'privilege' => 'edit',
                                'icon' => 'fa fa-folder-open',
                            ),
                        ),
                        'toolbar' => array(
                            array(
                                'url' => '/administrative-structure/department/edit/$id',
                                'title' => 'Editar departamento',
                                'description' => 'Permite alterar as informações do departamento escolhido',
                                'class' => 'fa fa-university  bg-blue',
                                'fntype' => 'selectedHttpClick',
                            ),
                            array(
                                'url' => '/administrative-structure/department/delete/$id',
                                'id' => 'department-delete',
                                'title' => 'Remover departamento',
                                'description' => 'Remove o departamento escolhido se ele não possuir departamentos filhos',
                                'class' => 'fa fa-university  bg-red',
                                'fntype' => 'selectedAjaxClick',
                            ),
                        ),
                    ),
                    array(
                        'label' => 'Add a Department',
                        'route' => 'administrative-structure/department',
                        'action' => 'add',
                        'resource' => 'AdministrativeStructure\Controller\Department',
                        'privilege' => 'add',
                        'icon' => 'fa fa-folder-open',
                    ),
                ),
            ),
        ),
    ),
    'doctrine' => array(
        'driver' => array(
            'orm_default' => array(
                'drivers' => array(
                    'AdministrativeStructure\Entity' => 'administrative-structure_driver',
                ),
            ),
            'administrative-structure_driver' => array(
                'class' => 'Doctrine\ORM\Mapping\Driver\AnnotationDriver',
                'cache' => 'array',
                'paths' => array(
                    __DIR__ . '/../src/AdministrativeStructure/Entity',
                ),
            ),
        ),
    ),
);
