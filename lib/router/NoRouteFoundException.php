<?php
class NoRouteFoundException extends Exception {
    public function __construct($url) {
        parent::__construct('No route found for url: '. $url);
    }
}
