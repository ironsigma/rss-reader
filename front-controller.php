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
set_include_path(get_include_path() . PATH_SEPARATOR . APP_PATH . '/lib/php-router/');

require 'php-router.php';

$dispatcher = new Dispatcher();
$dispatcher->setSuffix('Controller');
$dispatcher->setClassPath(APP_PATH.'/controllers');

// class route
$action_route = new Route('/:class');
$action_route->addDynamicElement(':class', ':class')
    ->setMapMethod('handleRequest')
    ->setMapMethod('handlePostRequest', 'POST')
    ->setMapMethod('handlePutRequest', 'PUT')
    ->setMapMethod('handleDeleteRequest', 'DELETE');

// class-id route
$action_id_route = new Route('/:class/:id');
$action_id_route->addDynamicElement(':class', ':class')
    ->addDynamicElement(':id', ':id')
    ->setMapMethod('handleRequest')
    ->setMapMethod('handlePostRequest', 'POST')
    ->setMapMethod('handlePutRequest', 'PUT')
    ->setMapMethod('handleDeleteRequest', 'DELETE');

$router = new Router();
$router->addRoute('action', $action_route);
$router->addRoute('action-id', $action_id_route);


try {

    $url = get_url();
    $found_route = $router->findRoute($url, $_SERVER['REQUEST_METHOD']);
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
