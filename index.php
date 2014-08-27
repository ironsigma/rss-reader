<?php
// constants
define('APP_PATH', __DIR__);
include('lib/AutoLoader.php');

Config::read(APP_PATH.'/config/reader.yaml');
Session::init();
Template::setTemplateDir(Config::get('templates.path'));

// helpers
function get_url() {
    $uri = urldecode($_SERVER['REQUEST_URI']);
    if ( strlen($uri) == 0 ) $uri = '/';
    return $uri;
}

function page_error($code, $message, $url) {
    header('Content-Type: application/json');
    header('Content-Version: 1.0');
    print(json_encode(array(
        'status' => 'error',
        'code' => $code,
        'error' => $message,
        'url' => $url,
    )));
}

// logging
LogFacility::setRootLoggerLevel(Logger::TRACE);
LogFacility::addAppender(new FileLogAppender(Config::get('logging.file')));
foreach ( Config::get('logging.loggers', array()) as $logger ) {
    LogFacility::setLoggerLevel($logger['label'], $logger['level']);
}

// routing
$dispatcher = new Dispatcher();
$dispatcher->setClassPath(Config::get('controllers.path'));
$dispatcher->setSuffix('Controller');

// default
$dispatcher->addRoute(Route::mapUrl('/')
    ->addController('Feed')
    ->addAction('handleRequest', Route::GET));

// class route
$dispatcher->addRoute(Route::mapUrl('/:class')
    ->addController(':class')
    ->addAction('handleRequest', Route::GET)
    ->addAction('handlePostRequest', Route::POST));

// feed/id/method route
$dispatcher->addRoute(Route::mapUrl('/:class/:id/:method')
    ->addController(':class')
    ->addAction(':method', Route::GET));

try {

    $url = get_url();
    $method = $_SERVER['REQUEST_METHOD'];
    $found_route = $dispatcher->findRoute($url);
    $controller = $found_route->getController($method);
    $user = true;
    if (
            $controller['class'] !== 'Login' &&
            $controller['class'] !== 'Updater'
       )
    {
        $user = Session::requireLogin();
    }

    if ( $user !== false ) {
        $dispatcher->dispatch($found_route, $method);
    }

} catch ( NoRouteFoundException $e ) {
    page_error('404', 'Invalid url: '. $e->getMessage(), $url);
} catch ( NoHandlerFoundException $e ) {
    page_error('400', 'Invalid resource: '. $e->getMessage(), $url);
} catch ( UnableToInvokeMethodException $e ) {
    page_error('500', 'Invalid operation: '. $e->getMessage(), $url);
}
