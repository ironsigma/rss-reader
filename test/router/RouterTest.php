<?php
class RouterTest extends PHPUnit_Framework_TestCase {

    public function testEmptyRoutes() {
        $d = new Dispatcher();
        try {
            $r = $d->findRoute('/');
            $this->fail('Unexpected route found');
        } catch ( NoRouteFoundException $ex ) {
            /* expected */
        }
        //$this->assertEquals('', $path);
    }

    public function testRoutes() {
        $d = new Dispatcher();
        $d->addRoute(Route::mapUrl('/'));
        $r = $d->findRoute('/');
    }
}
