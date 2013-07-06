<?php
$app_inc_paths = array_merge(
    array(
        APP_PATH . '/modules',
        APP_PATH . '/model',
    ),
    glob(APP_PATH . '/lib/*', GLOB_ONLYDIR)
);

if ( defined('APP_ADDL_INC_PATHS') ) {
    $app_inc_paths = array_merge($app_inc_paths, preg_split('/,\s*/', APP_ADDL_INC_PATHS));
}

set_include_path(get_include_path() . PATH_SEPARATOR . implode(PATH_SEPARATOR, $app_inc_paths));
unset($app_inc_paths);

function autoLoadClass($class_name) {
    $c = explode('\\', $class_name);
    include end($c) .'.php';
}

spl_autoload_register('autoLoadClass', false);
