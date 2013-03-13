<?php
// constants
define('APP_PATH', __DIR__);
include('lib/AutoLoader.php');

Config::read(APP_PATH.'/config/reader.yaml');
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
    header('Content-Version: $version');
    print(json_encode(array(
        'status' => 'error',
        'code' => $code,
        'error' => $message,
        'url' => $url,
    )));
}

// Session
UserSession::init();

// logging
LogFacility::setRootLoggerLevel(Logger::TRACE);
LogFacility::addAppender(new FileLogAppender(Config::get('logging.file')));
foreach ( Config::get('logging.loggers', array()) as $logger ) {
    LogFacility::setLoggerLevel($logger['label'], $logger['level']);
}

$log = LogFacility::getLogger('index.html');
$log->info('Config: '. print_r(Config::all(), true));

// database
Database::setDatabase(Config::get('database.file'));

// routing
$dispatcher = new Dispatcher();
$dispatcher->setSuffix('Controller');
$dispatcher->setClassPath(Config::get('controllers.path'));

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
$action_id_route = new Route('/:class/:id/:method');
$action_id_route->addDynamicElement(':class', ':class')
    ->addDynamicElement(':id', ':id')
    ->addDynamicElement(':method', ':method');

$router = new Router();
$router->addRoute('default', $default_route);
$router->addRoute('action', $action_route);
$router->addRoute('action-id', $action_id_route);

try {

    $url = get_url();
    $found_route = $router->findRoute($url, $_SERVER['REQUEST_METHOD']);
    $user = true;
    if (
            $found_route->getMapClass() !== 'login' &&
            $found_route->getMapClass() !== 'updater'
       )
    {
        $user = UserSession::requireLogin();
    }

    if ( $user !== false ) {
        $dispatcher->dispatch($found_route, null, $_SERVER['REQUEST_METHOD']);
    }

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
