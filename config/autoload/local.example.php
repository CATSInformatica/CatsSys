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
            'from' => 'name@yourdomain.com',
            'from_name' => 'Your Name',
            'replyTo' => [
                'replyto@yourdomain.com' => 'Reply name',
            ],
        ],
        'contact' => [
            /* lista de pares do tipo: email => nome */
            'from' => 'name@yourdomain.com',
            'from_name' => 'Your Name',
            'to' => [
                'contact@exemple.com' => 'Recebedor de emails de contato',
            ],
        ],
    ],
];
