<?php
/**
 * Post controller
 * @package com\izylab\reader
 */
class PostController extends JsonController {
    public function handlePostRequest($args) {
        $req = $this->getJsonRequest();
        PostDao::updateStar((boolean)$req['star'], $req['id']);
        $this->setJsonResponse(array('status' => 'success'));
    }
}
