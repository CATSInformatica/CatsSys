<?php

namespace Database;

return array(
    // Doctrine configuration
    'doctrine' => array(
        'configuration' => array(
            'orm_default' => array(
                'query_cache'       => 'filesystem',
                'result_cache'      => 'array',
                'metadata_cache'    => 'array',
                'hydration_cache'   => 'array',
                'generate_proxies' => getenv('APP_ENV') === 'development',
            ),
        ),
    ),
);


