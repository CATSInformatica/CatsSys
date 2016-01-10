<?php

namespace Database;

return array(
    'service_manager' => array(
        'factories' => array(
            'doctrine.cache.appApc' => 'Database\Factory\ApcCacheFactory',
        ),
        'abstract_factories' => array(
            'Zend\Cache\Service\StorageCacheAbstractServiceFactory',
        ),
    ),
    // Doctrine configuration
    'doctrine' => array(
        'configuration' => array(
            'orm_default' => array(
                'generate_proxies' => false,
            ),
        ),
    ),
);


