<?php

namespace FinancialManagement;

return array(
    'controllers' => array(
        'factories' => array(
            'FinancialManagement\Controller\CashFlow' => Factory\Controller\CashFlowControllerFactory::class,
            'FinancialManagement\Controller\MonthlyPayment' => Factory\Controller\MonthlyPaymentControllerFactory::class
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
                    'monthly-payment' => array(
                        'type' => 'Segment',
                        'options' => array(
                            'route' => '/monthly-payment[/:action]',
                            'constraints' => array(
                                'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                            ),
                            'defaults' => array(
                                'controller' => 'FinancialManagement\Controller\MonthlyPayment',
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
                    //'toolbar' => array(
                    //    array( 
                    //        'url' => '/financial-management/cash-flow/add-expense',
                    //        'id' => 'expense-add',
                    //        'title' => 'Adicionar despesa',
                    //        'description' => 'Adiciona uma despesa ao balanço financeiro do CATS',
                    //        'class' => 'fa fa-plus bg-green',
                    //        'fntype' => 'httpClick',
                    //    )
                    //),                        
                    ),
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
                ),
            ),
        ),
    ),
);
