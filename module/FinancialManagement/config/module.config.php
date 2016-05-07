<?php

namespace FinancialManagement;

return array(
    'controllers' => array(
        'factories' => array(
            'FinancialManagement\Controller\CashFlow' => Factory\Controller\CashFlowControllerFactory::class,
        ),
    ),
    'router' => array(
        'routes' => array(
            'financial-management' => array(
                'type' => 'Literal',
                'options' => array(
                    'route' => '/financial-management',
                ),
                'may_terminate' => true,
                'child_routes' => array(
                    'cash-flow' => array(
                        'type' => 'Segment',
                        'options' => array(
                            'route' => '/cash-flow[/:action[/:id]]',
                            'constraints' => array(
                                'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                                'id' => '[0-9]+',
                            ),
                            'defaults' => array(
                                'controller' => 'FinancialManagement\Controller\CashFlow',
                                'action' => 'index',
                            ),
                        ),
                    ),
                ),
            ),
        ),
    ),
    // Doctrine configuration
    'doctrine' => array(
        'driver' => array(
            'financial_management_driver' => array(
                'class' => 'Doctrine\ORM\Mapping\Driver\AnnotationDriver',
                'cache' => 'array',
                'paths' => array(
                    __DIR__ . '/../src/FinancialManagement/Entity',
                ),
            ),
            'orm_default' => array(
                'drivers' => array(
                    'FinancialManagement\Entity' => 'financial_management_driver',
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
    'navigation' => array(
        'default' => array(
            array(
                'label' => 'Financial Management',
                'uri' => '#',
                'icon' => 'fa fa-money',
                'order' => 11,
                'pages' => array(
                    array(
                        'label' => 'Expense and Revenue',
                        'route' => 'financial-management/cash-flow',
                        'action' => 'index',
                        'resource' => 'FinancialManagement\Controller\CashFlow',
                        'privilege' => 'index',
                        'icon' => 'fa fa-bar-chart',
                        'pages' => array(
                            array(
                                'label' => 'Get Month Balances',
                                'route' => 'financial-management/cash-flow',
                                'action' => 'get-month-balances',
                                'icon' => 'fa fa-list-alt',
                            ),
                        ),
                    ),
                    array(
                        'label' => 'Open Month Balance',
                        'route' => 'financial-management/cash-flow',
                        'action' => 'open-month-balance',
                        'resource' => 'FinancialManagement\Controller\CashFlow',
                        'privilege' => 'open-month-balance',
                        'icon' => 'fa fa-calendar-plus-o',
                    ),
                    array(
                        'label' => 'Close Month Balance',
                        'route' => 'financial-management/cash-flow',
                        'action' => 'close-month-balance',
                        'resource' => 'FinancialManagement\Controller\CashFlow',
                        'privilege' => 'close-month-balance',
                        'icon' => 'fa fa-calendar-times-o',
                    ),
                    array(
                        'label' => 'Show Month Balances',
                        'route' => 'financial-management/cash-flow',
                        'action' => 'month-balances',
                        'resource' => 'FinancialManagement\Controller\CashFlow',
                        'privilege' => 'month-balances',
                        'icon' => 'fa fa-calendar',
                        'toolbar' => array(
                            array(
                                'url' => '/financial-management/cash-flow/delete-month-balance/$id',
                                'id' => 'month-balance-delete',
                                'title' => 'Remover',
                                'description' => 'Remove o balanço do mês selecionado',
                                'class' => 'fa fa-trash-o bg-red',
                                'fntype' => 'selectedAjaxClick',
                                'hideOnSuccess' => true,
                            ),
                        ),
                    ),
                    array(
                        'label' => 'Add Cash Flow',
                        'route' => 'financial-management/cash-flow',
                        'action' => 'add-cash-flow',
                        'resource' => 'FinancialManagement\Controller\CashFlow',
                        'privilege' => 'add-cash-flow',
                        'icon' => 'fa fa-usd',
                    ),
                    array(
                        'label' => 'Show Cash Flows',
                        'route' => 'financial-management/cash-flow',
                        'action' => 'cash-flows',
                        'resource' => 'FinancialManagement\Controller\CashFlow',
                        'privilege' => 'cash-flows',
                        'icon' => 'fa fa-th-list',
                        'toolbar' => array(
                            array(
                                'url' => '/financial-management/cash-flow/delete-cash-flow/$id',
                                'id' => 'cash-flow-delete',
                                'title' => 'Remover',
                                'description' => 'Remove o fluxo de caixa selecionado',
                                'class' => 'fa fa-trash-o bg-red',
                                'fntype' => 'selectedAjaxClick',
                                'hideOnSuccess' => true,
                            ),
                            array(
                                'url' => '/financial-management/cash-flow/edit-cash-flow/$id',
                                'title' => 'Editar fluxo de caixa',
                                'id' => 'cash-flow-edit',
                                'description' => 'Permite editar o fluxo de caixa selecionado',
                                'class' => 'fa fa-pencil-square-o bg-blue',
                                'fntype' => 'selectedHttpClick',
                            ),
                        ),
                        'pages' => array(
                            array(
                                'label' => 'Edit Cash Flow',
                                'route' => 'school-management/cash-flow',
                                'action' => 'edit-cash-flow',
                                'icon' => 'fa fa-pencil-square-o bg-blue',
                            ),
                        ),
                    ),
                    array(
                        'label' => 'Create Cash Flow Type',
                        'route' => 'financial-management/cash-flow',
                        'action' => 'create-cash-flow-type',
                        'resource' => 'FinancialManagement\Controller\CashFlow',
                        'privilege' => 'create-cash-flow-type',
                        'icon' => 'fa fa-plus',
                    ),
                    array(
                        'label' => 'Show Cash Flow Types',
                        'route' => 'financial-management/cash-flow',
                        'action' => 'cash-flow-types',
                        'resource' => 'FinancialManagement\Controller\CashFlow',
                        'privilege' => 'cash-flow-types',
                        'icon' => 'fa fa-th-list',
                        'toolbar' => array(
                            array(
                                'url' => '/financial-management/cash-flow/delete-cash-flow-type/$id',
                                'id' => 'cash-flow-type-delete',
                                'title' => 'Remover',
                                'description' => 'Remove o tipo de fluxo de caixa selecionado',
                                'class' => 'fa fa-trash-o bg-red',
                                'fntype' => 'selectedAjaxClick',
                                'hideOnSuccess' => true,
                            ),
                            array(
                                'url' => '/financial-management/cash-flow/edit-cash-flow-type/$id',
                                'title' => 'Editar tipo de fluxo de caixa',
                                'id' => 'cash-flow-type-edit',
                                'description' => 'Permite editar o tipo de fluxo de caixa selecionado',
                                'class' => 'fa fa-pencil-square-o bg-blue',
                                'fntype' => 'selectedHttpClick',
                            ),
                        ),
                        'pages' => array(
                            array(
                                'label' => 'Edit Cash Flow Type',
                                'route' => 'school-management/cash-flow',
                                'action' => 'edit-cash-flow-type',
                                'icon' => 'fa fa-pencil-square-o bg-blue',
                            ),
                        ),
                    ),
                ),
            ),
        ),
    ),
);
