<?php

class FeedController {
    /*
     * Display feed list
     */
    public function handleRequest($args) {
        $template = new Template('feed_list.php');
        $template->feeds = FeedDao::findAllWithUnreadCount();
        $template->display();
    }

    /*
     * Display article list
     */
    public function articles($args) {
        $template = new Template('article_list.php');

        $template->feed = FeedDao::findById($args[':id']);

        $template->article_count = PostDao::countAll(array(
            'feed_id' => $args[':id'],
            'read' => false,
        ));

        if ( $template->article_count === 0 ) {
            header('Location: /');
            return;
        }

        if ( $template->article_count != 0 ) {
            $template->page_count = ceil($template->article_count / $template->feed->per_page);
            $template->page = min($template->page_count, $args['page']);

            $template->articles = PostDao::findAll(array(
                'feed_id' => $args[':id'],
                'limit' => $template->feed->per_page,
                'offset' => $template->feed->per_page * ($template->page-1),
                'read' => false,
                'sort' => $template->feed->sort,
            ));

            $ids = array();
            foreach ( $template->articles as $article ) {
                $ids[] = $article->id;
            }
            $template->article_ids = join(',', $ids);
        } else {
            $template->page_count = 0;
            $template->articles = array();
            $template->page = 1;
        }

        $template->display();
    }

    /*
     * Mark selected articles read.
     */
    public function read($args) {
        PostDao::markRead(( $args['ids'] === 'all' ? null : $args['ids']), $args['feed']);
        header("Location: /feed/{$args[':id']}/articles?page={$args['page']}");
    }
}
