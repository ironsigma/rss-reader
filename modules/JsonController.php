<?php
define('APP_VERSION', 'com.izylab.reader-v1');

class AbstractController {
    protected function getRequestMethod() {
        return $_SERVER['REQUEST_METHOD'];
    }
    protected function getJsonRequest() {
        return json_decode(file_get_contents('php://input'), true);
    }
    protected function setJsonResponse($response) {
        header('Content-Type: application/json');
        header('Content-Version: '. APP_VERSION);
        print(json_encode($response));
    }
}
