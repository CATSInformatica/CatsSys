<?php

namespace FinancialManagement;

use Zend\Router\Http\Segment;
use Zend\Router\Http\Literal;

return [
    'controllers' => [
        'factories' => [
            'FinancialManagement\Controller\CashFlow' => Factory\Controller\CashFlowControllerFactory::class,
            'FinancialManagement\Controller\MonthlyPayment' => Factory\Controller\MonthlyPaymentControllerFactory::class
        ],
    ],
    'router' => [
        'routes' => [
            'financial-management' => [
                'type' => Literal::class,
                'options' => [
                    'route' => '/financial-management',
                ],
                'may_terminate' => true,
                'child_routes' => [
                    'cash-flow' => [
                        'type' => Segment::class,
                        'options' => [
                            'route' => '/cash-flow[/:action[/:id]]',
                            'constraints' => [
                                'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                                'id' => '[0-9]+',
                            ],
                            'defaults' => [
                                'controller' => 'FinancialManagement\Controller\CashFlow',
                                'action' => 'index',
                            ],
                        ],
                    ],
                    'monthly-payment' => [
                        'type' => Segment::class,
                        'options' => [
                            'route' => '/monthly-payment[/:action]',
                            'constraints' => [
                                'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                            ],
                            'defaults' => [
                                'controller' => 'FinancialManagement\Controller\MonthlyPayment',
                            ],
                        ],
                    ],
                ],
            ],
        ],
    ],
    // Doctrine configuration
    'doctrine' => [
        'driver' => [
            'financial_management_driver' => [
                'class' => 'Doctrine\ORM\Mapping\Driver\AnnotationDriver',
                'cache' => 'array',
                'paths' => [
                    __DIR__ . '/../src/Entity',
                ],
            ],
            'orm_default' => [
                'drivers' => [
                    'FinancialManagement\Entity' => 'financial_management_driver',
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
        ],
        'display_exceptions' => true,
    ],
    'navigation' => [
        'default' => [
            [
                'label' => 'Financial Management',
                'uri' => '#',
                'icon' => 'fa fa-money',
                'order' => 11,
                'pages' => [
                    [
                        'label' => 'Exp. and rev. analysis',
                        'route' => 'financial-management/cash-flow',
                        'action' => 'index',
                        'resource' => 'FinancialManagement\Controller\CashFlow',
                        'privilege' => 'index',
                        'icon' => 'fa fa-bar-chart',
                        'pages' => [
                            [
                                'label' => 'Get Month Balances',
                                'route' => 'financial-management/cash-flow',
                                'action' => 'get-month-balances',
                                'icon' => 'fa fa-list-alt',
                            ],
                            [
                                'label' => 'Get Filtered Cash Flows',
                                'route' => 'financial-management/cash-flow',
                                'action' => 'get-filtered-cash-flows',
                                'icon' => 'fa fa-list-alt',
                            ],
                        ],
                    ],
                    [
                        'label' => 'Open month balance',
                        'route' => 'financial-management/cash-flow',
                        'action' => 'open-month-balance',
                        'resource' => 'FinancialManagement\Controller\CashFlow',
                        'privilege' => 'open-month-balance',
                        'icon' => 'fa fa-calendar-plus-o',
                    ],
                    [
                        'label' => 'Close month balance',
                        'route' => 'financial-management/cash-flow',
                        'action' => 'close-month-balance',
                        'resource' => 'FinancialManagement\Controller\CashFlow',
                        'privilege' => 'close-month-balance',
                        'icon' => 'fa fa-calendar-times-o',
                    ],
                    [
                        'label' => 'Show month balances',
                        'route' => 'financial-management/cash-flow',
                        'action' => 'month-balances',
                        'resource' => 'FinancialManagement\Controller\CashFlow',
                        'privilege' => 'month-balances',
                        'icon' => 'fa fa-calendar',
                        'toolbar' => [
                            [
                                'url' => '/financial-management/cash-flow/delete-month-balance/$id',
                                'id' => 'month-balance-delete',
                                'title' => 'Remover',
                                'description' => 'Remove o balanço do mês selecionado',
                                'class' => 'fa fa-trash-o bg-red',
                                'fntype' => 'selectedAjaxClick',
                                'hideOnSuccess' => true,
                            ],
                        ],
                    ],
                    [
                        'label' => 'Add exp. and rev.',
                        'route' => 'financial-management/cash-flow',
                        'action' => 'add-cash-flow',
                        'resource' => 'FinancialManagement\Controller\CashFlow',
                        'privilege' => 'add-cash-flow',
                        'icon' => 'fa fa-usd',
                    ],
                    [
                        'label' => 'Show exp. and rev.',
                        'route' => 'financial-management/cash-flow',
                        'action' => 'cash-flows',
                        'resource' => 'FinancialManagement\Controller\CashFlow',
                        'privilege' => 'cash-flows',
                        'icon' => 'fa fa-th-list',
                        'toolbar' => [
                            [
                                'url' => '/financial-management/cash-flow/delete-cash-flow/$id',
                                'id' => 'cash-flow-delete',
                                'title' => 'Remover',
                                'description' => 'Remove o fluxo de caixa selecionado',
                                'class' => 'fa fa-trash-o bg-red',
                                'fntype' => 'selectedAjaxClick',
                                'hideOnSuccess' => true,
                            ],
                            [
                                'url' => '/financial-management/cash-flow/edit-cash-flow/$id',
                                'title' => 'Editar fluxo de caixa',
                                'id' => 'cash-flow-edit',
                                'description' => 'Permite editar o fluxo de caixa selecionado',
                                'class' => 'fa fa-pencil-square-o bg-blue',
                                'fntype' => 'selectedHttpClick',
                            ],
                        ],
                        'pages' => [
                            [
                                'label' => 'Edit cash flow',
                                'route' => 'school-management/cash-flow',
                                'action' => 'edit-cash-flow',
                                'icon' => 'fa fa-pencil-square-o bg-blue',
                            ],
                        ],
                    ],
                    [
                        'label' => 'Create exp. and rev. types',
                        'route' => 'financial-management/cash-flow',
                        'action' => 'create-cash-flow-type',
                        'resource' => 'FinancialManagement\Controller\CashFlow',
                        'privilege' => 'create-cash-flow-type',
                        'icon' => 'fa fa-plus',
                    ],
                    [
                        'label' => 'Show exp. and rev. types',
                        'route' => 'financial-management/cash-flow',
                        'action' => 'cash-flow-types',
                        'resource' => 'FinancialManagement\Controller\CashFlow',
                        'privilege' => 'cash-flow-types',
                        'icon' => 'fa fa-th-list',
                        'toolbar' => [
                            [
                                'url' => '/financial-management/cash-flow/delete-cash-flow-type/$id',
                                'id' => 'cash-flow-type-delete',
                                'title' => 'Remover',
                                'description' => 'Remove o tipo de fluxo de caixa selecionado',
                                'class' => 'fa fa-trash-o bg-red',
                                'fntype' => 'selectedAjaxClick',
                                'hideOnSuccess' => true,
                            ],
                            [
                                'url' => '/financial-management/cash-flow/edit-cash-flow-type/$id',
                                'title' => 'Editar tipo de fluxo de caixa',
                                'id' => 'cash-flow-type-edit',
                                'description' => 'Permite editar o tipo de fluxo de caixa selecionado',
                                'class' => 'fa fa-pencil-square-o bg-blue',
                                'fntype' => 'selectedHttpClick',
                            ],
                        ],
                        'pages' => [
                            [
                                'label' => 'Edit cash flow type',
                                'route' => 'school-management/cash-flow',
                                'action' => 'edit-cash-flow-type',
                                'icon' => 'fa fa-pencil-square-o bg-blue',
                            ],
                        ],
                    ],
                    [
                        'label' => 'Monthly payment',
                        'route' => 'financial-management/monthly-payment',
                        'action' => 'payment',
                        'resource' => 'FinancialManagement\Controller\MonthlyPayment',
                        'privilege' => 'payment',
                        'icon' => 'fa fa-users',
                        'toolbar' => [
                            [
                                'url' => '/financial-management/monthly-payment/savePayments',
                                'id' => 'save-payments',
                                'title' => 'Salvar',
                                'description' => 'Salva as mensalidades selecionadas',
                                'class' => 'fa fa-hdd-o bg-green',
                                'fntype' => 'ajaxPostClick',
                            ],
                            [
                                'url' => '/financial-management/monthly-payment/deletePayments',
                                'id' => 'delete-payments',
                                'title' => 'Remover',
                                'description' => 'Remove as mensalidades selecionadas',
                                'class' => 'fa fa-trash bg-red',
                                'fntype' => 'ajaxPostClick',
                            ]
                        ],
                    ]
                ],
            ],
        ],
    ],
];
