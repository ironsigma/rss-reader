<?php
class NoHandlerFoundException extends Exception {
    public function __construct($uri, $type, $route) {
        parent::__construct('No handler found for "'. strtoupper($type) . ':'. $uri . '" in route "'. $route .'"');
    }
}
