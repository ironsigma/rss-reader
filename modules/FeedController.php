<?php

class FeedController {
    public function handleRequest($args) {
        $template = new Template('feed_list.php');
        $template->feeds = FeedDao::findAllWithUnreadCount();
        $template->display();
    }
    public function articles($args) {
        $template = new Template('article_list.php');

        $template->args = print_r($_GET, true);
        $template->feed = FeedDao::findById($args[':id']);

        $template->article_count = PostDao::countAll(array(
            'feed_id' => $args[':id'],
            'read' => false,
        ));

        $template->articles = PostDao::findAll(array(
            'feed_id' => $args[':id'],
            'limit' => 25,
            'offset' => 0,
            'read' => false,
            'sort' => $template->feed->sort,
        ));

        $template->display();
    }
}
