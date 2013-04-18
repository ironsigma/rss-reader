<?php
class UnableToInvokeMethodException extends Exception {
    public function __construct($class, $method, $message) {
        parent::__construct("Unable to invoke method $class::$method(\$request): $message");
    }
}
