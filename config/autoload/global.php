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
return array(
    'doctrine' => array(
        'connection' => array(
            // default connection name
            'orm_default' => array(
                'driverClass' => 'Doctrine\DBAL\Driver\PDOMySql\Driver',
                'params' => array(
                    'host' => 'localhost',
                    'port' => '3306',
                    'charset' => 'utf8', // extra
                    'driverOptions' => array(
                        1002 => 'SET NAMES utf8'
                    )
                )
            )
        )
    ),
    'module_layouts' => array(        
        'Dashboard' => 'layout/dashboard-layout.phtml',
        'Authorization' => 'layout/dashboard-layout.phtml',
        'Authentication' => 'layout/login-layout.phtml',
        'Site' => 'layout/layout.phtml',
    ),
);
