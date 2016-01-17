<?php

/**
 * If you need an environment-specific system or application configuration,
 * there is an example in the documentation
 * @see http://framework.zend.com/manual/current/en/tutorials/config.advanced.html#environment-specific-system-configuration
 * @see http://framework.zend.com/manual/current/en/tutorials/config.advanced.html#environment-specific-application-configuration
 */
$env = getenv('APP_ENV');

$modules = array(
    // per-module layout
    'EdpModuleLayouts',
    // Twitter Bootstrap view helpers
    'TwbBundle',
    // ORM mappers
    'DoctrineModule',
    'DoctrineORMModule',
    // Database helper Module
    'Database',
    //First Application Module
    'Site',
    //Authentication Module
    'Authentication',
    //Authorizarion Module
    'Authorization',
    //User Management Space Module
    'UMS',
    // Recruitment Module
    'Recruitment',
    // School Management
    'SchoolManagement',
    // Documents
    'Documents',
);

if ($env === 'development') {
    // zend developer tools helper to see doctrine operations, database MER and other things
//    $modules[] = 'ZendDeveloperTools';
}


return array(
// This should be an array of module namespaces used in the application.
    'modules' => $modules,
    // These are various options for the listeners attached to the ModuleManager
    'module_listener_options' => array(
        // This should be an array of paths in which modules reside.
        // If a string key is provided, the listener will consider that a module
        // namespace, the value of that key the specific path to that module's
        // Module class.
        'module_paths' => array(
            './module',
            './../vendor',
        ),
        // An array of paths from which to glob configuration files after
        // modules are loaded. These effectively override configuration
        // provided by modules themselves. Paths may use GLOB_BRACE notation.
        'config_glob_paths' => array(
            'config/autoload/{{,*.}global,{,*.}local}.php',
        ),
        // Whether or not to enable a configuration cache.
        // If enabled, the merged configuration will be cached and used in
        // subsequent requests.
        'config_cache_enabled' => $env === 'production',
        // The key used to create the configuration cache file name.
        'config_cache_key' => 'app_config',
        // Whether or not to enable a module class map cache.
        // If enabled, creates a module class map cache which will be used
        // by in future requests, to reduce the autoloading process.
        'module_map_cache_enabled' => $env === 'production',
        // The key used to create the class map cache file name.
        'module_map_cache_key' => 'module_config',
        // The path in which to cache merged configuration.
        'cache_dir' => './data/cache/',
        // Whether or not to enable modules dependency checking.
        // Enabled by default, prevents usage of modules that depend on other modules
        // that weren't loaded.
        'check_dependencies' => $env === 'production',
    ),
    // Used to create an own service manager. May contain one or more child arrays.
    //'service_listener_options' => array(
    //     array(
    //         'service_manager' => $stringServiceManagerName,
    //         'config_key'      => $stringConfigKey,
    //         'interface'       => $stringOptionalInterface,
    //         'method'          => $stringRequiredMethodName,
    //     ),
    // ),
    // Initial configuration with which to seed the ServiceManager.
    // Should be compatible with Zend\ServiceManager\Config.
    // 'service_manager' => array(),
);
