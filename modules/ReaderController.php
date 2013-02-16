<?php

class ReaderController {
    public function handleRequest($args) {
        $template = new Template('folders.php');
        $template->date = date('c');
        $template->feeds = FeedDao::findAllWithUnreadCount();
        $template->display();
    }
}
