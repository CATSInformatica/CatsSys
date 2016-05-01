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
                ),
            ),
        ),
    ),
);
