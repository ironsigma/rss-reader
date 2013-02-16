<?php

class UpdaterController {
    public function handleRequest($args) {
        $feeds = FeedDao::findAll();
    }
}
