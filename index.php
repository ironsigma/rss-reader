<?php
// constants
define('APP_PATH', __DIR__);
include('lib/AutoLoader.php');

Config::read(APP_PATH.'/config/reader.yaml');
Session::init();
Template::setTemplateDir(Config::get('templates.path'));

Template::setTheme(isset($_GET['mobi'])?
    Config::get('templates.themes.mobile'):
    Config::get('templates.themes.desktop'));

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
$dispatcher->add(new Route('/', array(
    'controller' => 'Feed',
    'methods' => array(
        array('name' => 'handleRequest', 'type' => 'get'),
    ),
)));

// class route
$dispatcher->add(new Route('/:class', array(
    'controller' => ':class',
    'methods' => array(
        array('name' => 'handleRequest', 'type' => 'get'),
        array('name' => 'handlePostRequest', 'type' => 'post'),
    ),
)));

// feed/id/method route
$dispatcher->add(new Route('/:class/:id/:method', array(
    'controller' => ':class',
    'methods' => array(
        array('name' => ':method', 'type' => 'get'),
    ),
)));

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
