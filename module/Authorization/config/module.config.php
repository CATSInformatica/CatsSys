<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Authorization;

return array(
    'controllers' => array(
        'invokables' => array(
            'Authorization\Controller\Index' => 'Authorization\Controller\IndexController',
        )
    ),
    'router' => array(
        'routes' => array(
            'authorization' => array(
                'type' => 'Literal',
                'options' => array(
                    'route' => '/authorization',
                    'defaults' => array(
                        '__NAMESPACE__' => 'Authorization\Controller',
                        'controller' => 'index',
                        'action' => 'index',
                    ),
                ),
                'may_terminate' => true,
            ),
        ),
    ),
    'view_manager' => array(
        'template_path_stack' => array(
            'authorization' => __DIR__ . '/../view',
        ),
        'display_exceptions' => true,
    ),
);
