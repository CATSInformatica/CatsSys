<?php

return [
   'doctrine' => [
       'connection' => [
           'orm_default' => [
               'params' => [
                   'host' => 'mysql',
                   'port' => '3306',
                   'user' => 'catssys',
                   'password' => 'catssys',
                   'dbname'=> 'catssys',
               ],
           ],
       ],
   ],
   'email' => [
        'recruitment' => [
            'from' => [
                'email' => 'name@yourdomain.com',
                'name' => 'Your Name',
            ],
            'replyTo' => [
                'email' => 'name@yourdomain.com',
                'name' => 'Your Name',
            ],
        ],
        'contact' => [
            'from' => [
                'email' => 'name@yourdomain.com',
                'name' => 'Your Name',
            ],
            'to' => [
                'email' => 'name@yourdomain.com',
                'name' => 'Your Name',
            ],
        ],
    ],
    'mailgun' => [
        'api_key' => 'key-example123456',
        'domain' => 'somedomain.com.br',
    ]
];
