<?php

return array(
    'acl' => array(
        'roles' => array(
            'guest' => null,
            'member' => 'guest',
            'admin' => 'member',
        ),
        'resources' => array(
            'allow' => array(
                'Site\Controller\Index' => array(
                    'all' => 'guest',
                ),                
                'Authentication\Controller\Login' => array(
                    'login' => 'guest',
                    'all' => 'member',
                ),
                'Authorization\Controller\Index' => array(
                    'all' => 'guest',
                ),
                'Dashboard\Controller\Index' => array(
                    'index' => 'guest',
                ),
                'Dashboard\Controller\User' => array(
                    'index' => 'member',
                    'create' => 'admin',
                    'delete' => 'admin',
                    'edit' => 'admin',
                ),
            )
        )
    )
);
