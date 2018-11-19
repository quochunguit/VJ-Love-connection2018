<?php
if (file_exists(__DIR__ . '/autoload/env.php')){
    $env = include __DIR__ . '/autoload/env.php';
}

if (!$env){
    $env = "development";
}

$modules = include __DIR__ . '/autoload/module.php';
$mconfig = array(
    'module_listener_options' => array(
        'config_glob_paths'        => array(
            WEB_ROOT . '/application/config/autoload/{,*.}{global,mail,database,social,layout,site,position}.php',
            sprintf(WEB_ROOT . '/application/config/autoload/{,*.}{%s}.php', $env),
            /*WEB_ROOT . '/application/config/autoload/{*.}{local,global}.php',*/
        ),
        'module_paths'             => array(
            WEB_ROOT . '/application/module',
            WEB_ROOT . '/vendor',
        ),
        'config_cache_enabled'     => false,
        'config_cache_key'         => "appconfig",
        'module_map_cache_enabled' => false,
        'module_map_cache_key'     => 'module_map',
        'cache_dir'                => WEB_ROOT . "/var/cache",
        'check_dependencies'       => ($env != 'production')

    )
);
return array_merge($modules, $mconfig);