<?php
/**
 * Feed controller
 * @package com\izylab\reader
 */
class FeedController {
    /*
     * Display feed list
     */
    public function handleRequest($args) {
        $template = new Template('feed_list.php');
        $template->feeds = FeedDao::findAllWithUnreadCount();
        $template->stared_count = PostDao::countStared();
        $template->display();
    }

    /*
     * Display article list
     */
    public function articles($args) {
        $template = new Template('article_list.php');
        $template->feed_id = $args[':id'];

        $criteria = new Criteria();
        if ( $args[':id'] === 'stared' ) {
            $template->feed_name = 'Stared articles';
            $per_page = 10;
            $template->article_count = PostDao::countStared();

            $criteria->true('stared');
            $criteria->orderBy('ts', 'ASC');
        } else {
            $feed = FeedDao::findById($args[':id']);
            $template->feed_name = $feed->name;
            $per_page = $feed->per_page;
            $template->article_count = PostDao::countAll(array(
                'feed_id' => $args[':id'],
                'read' => 0,
            ));
            $criteria->equal('feed_id', $args[':id'], SQLITE3_INTEGER);
            $criteria->false('read');
            $criteria->orderBy('ts', $feed->sort_dir);
        }

        if ( $template->article_count === 0 ) {
            header('Location: /');
            return;
        }

        if ( $template->article_count != 0 ) {
            $template->page_count = ceil($template->article_count / $per_page);
            $template->page = min($template->page_count, $args['page']);
            $criteria->page($per_page, $per_page * ($template->page-1));
            $template->articles = PostDao::findAll($criteria);

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
