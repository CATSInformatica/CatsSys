<?php

namespace Database;

return array(
    // 'service_manager' => array(
    //     'factories' => array(
    //         'doctrine.cache.apc' => 'Database\Factory\Cache\ApcCacheFactory',
    //     ),
    //     'abstract_factories' => array(
    //         'Zend\Cache\Service\StorageCacheAbstractServiceFactory',
    //     ),
    // ),
    // Doctrine configuration
    'doctrine' => array(
        'configuration' => array(
            'orm_default' => array(
                'query_cache'       => 'filesystem',
                'result_cache'      => 'array',
                'metadata_cache'    => 'apc',
                'hydration_cache'   => 'apc',
                'generate_proxies' => getenv('APP_ENV') === 'development',
            ),
        ),
    ),
);


