<?php
// constants
define('APP_PATH', __DIR__);

// helpers
function get_url() {
    $uri = urldecode($_SERVER['REQUEST_URI']);
    if ( strlen($uri) == 0 ) $uri = '/';
    return $uri;
}

function page_error($code, $message, $url) {
    header('Content-Type: application/json');
    header('Content-Version: $version');
    print(json_encode(array(
        'status' => 'error',
        'code' => $code,
        'error' => $message,
        'url' => $url,
    )));
}

// include path
set_include_path(get_include_path()
    . PATH_SEPARATOR . APP_PATH . '/modules/'
    . PATH_SEPARATOR . APP_PATH . '/model/'
    . PATH_SEPARATOR . APP_PATH . '/lib/'
    . PATH_SEPARATOR . APP_PATH . '/lib/logger/'
    . PATH_SEPARATOR . APP_PATH . '/lib/database/'
    . PATH_SEPARATOR . APP_PATH . '/lib/php-router/'
);

function __autoload($class_name) {
    include "$class_name.php";
}

// Session
UserSession::init();

// logging
LogFacility::setRootLoggerLevel(Logger::TRACE);
LogFacility::addAppender(new FileLogAppender(APP_PATH .'/log/reader.log'));
LogFacility::setLoggerLevel('Database.class', Logger::WARN);
LogFacility::setLoggerLevel('UpdaterController.class', Logger::WARN);

// database
Database::setDatabase(APP_PATH .'/db/reader.sqlite3');

// routing
$dispatcher = new Dispatcher();
$dispatcher->setSuffix('Controller');
$dispatcher->setClassPath(APP_PATH.'/modules');

// default
$default_route = new Route('/');
$default_route->setMapClass('Feed')
    ->setMapMethod('handleRequest');

// class route
$action_route = new Route('/:class');
$action_route->addDynamicElement(':class', ':class')
    ->setMapMethod('handleRequest')
    ->setMapMethod('handlePostRequest', 'POST');

// feed/id/method route
$action_id_route = new Route('/feed/:id/:method');
$action_id_route->setMapClass('Feed')
    ->addDynamicElement(':id', ':id')
    ->addDynamicElement(':method', ':method');

$router = new Router();
$router->addRoute('default', $default_route);
$router->addRoute('action', $action_route);
$router->addRoute('action-id', $action_id_route);

try {

    $url = get_url();
    $found_route = $router->findRoute($url, $_SERVER['REQUEST_METHOD']);
    if (
            $found_route->getMapClass() !== 'login' &&
            $found_route->getMapClass() !== 'updater'
       )
    {
        UserSession::requireLogin();
    }
    $dispatcher->dispatch($found_route, null, $_SERVER['REQUEST_METHOD']);

} catch ( RouteNotFoundException $e ) {
    page_error('404', 'Invalid url: '. $e->getMessage(), $url);
} catch ( BadClassNameException $e ) {
    page_error('400', 'Invalid resource name: '. $e->getMessage(), $url);
} catch ( ClassFileNotFoundException $e ) {
    page_error('500', 'Resource does not exist: '. $e->getMessage(), $url);
} catch ( ClassNameNotFoundException $e ) {
    page_error('500', 'Resource not found: '. $e->getMessage(), $url);
} catch ( ClassMethodNotFoundException $e ) {
    page_error('500', 'Invalid operation: '. $e->getMessage(), $url);
} catch ( ClassNotSpecifiedException $e ) {
    page_error('500', 'No resource specified: '. $e->getMessage(), $url);
} catch ( MethodNotSpecifiedException $e ) {
    page_error('500', 'No operation specified: '. $e->getMessage(), $url);
}
