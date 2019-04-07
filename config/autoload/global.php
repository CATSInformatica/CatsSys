<?php

/**
 * Global Configuration Override
 *
 * You can use this file for overriding configuration values from modules, etc.
 * You would place values in here that are agnostic to the environment and not
 * sensitive to security.
 *
 * @NOTE: In practice, this file will typically be INCLUDED in your source
 * control, so do not include passwords or other sensitive information in this
 * file.
 */
return [
    'doctrine' => [
        'connection' => [
            // default connection name
            'orm_default' => [
                'driverClass' => 'Doctrine\DBAL\Driver\PDOMySql\Driver',
                'params' => [
                    'charset' => 'utf8', // extra
                    'driverOptions' => [
                        1002 => 'SET NAMES utf8'
                    ],
                ],
            ],
        ],
    ],
    'module_layouts' => [
        'Site' => 'layout/layout',
        'Authentication' => 'application/layout',
        'Authorization' => 'application/layout',
        'UMS' => 'application/layout',
        'Recruitment' => 'application/layout',
        'SchoolManagement' => 'application/layout',
        'Documents' => 'application/layout',
        'AdministrativeStructure' => 'application/layout',
        'Version' => 'application/layout',
        'FinancialManagement' => 'application/layout',
    ]
];
