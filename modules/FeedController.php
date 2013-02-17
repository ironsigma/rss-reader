<?php

class FeedController {
    public function handleRequest($args) {
        $template = new Template('feed_list.php');
        $template->feeds = FeedDao::findAllWithUnreadCount();
        $template->display();
    }
    public function articles($args) {
        $per_page = 10;
        $template = new Template('article_list.php');

        $template->page = $args['page'];
        $template->feed = FeedDao::findById($args[':id']);

        $template->article_count = PostDao::countAll(array(
            'feed_id' => $args[':id'],
            'read' => false,
        ));

        $template->page_count = ceil($template->article_count / $per_page);

        $template->articles = PostDao::findAll(array(
            'feed_id' => $args[':id'],
            'limit' => $per_page,
            'offset' => $per_page * ($args['page']-1),
            'read' => false,
            'sort' => $template->feed->sort,
        ));

        $template->display();
    }
}
