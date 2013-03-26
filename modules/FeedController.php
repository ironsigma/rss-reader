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
        $template = new Template('feed_list');
        $template->feeds = FeedDao::findAllWithUnreadCount();

        $template->stared_count = PostDao::staredCount();

        $template->folder_counts = PostDao::postFolderCount();
        $template->display();
    }

    /*
     * Display article list
     */
    public function articles($args) {
        $template = new Template('article_list');
        $template->container = 'feed';
        $template->feed_id = $args[':id'];

        if ( $args[':id'] === 'stared' ) {
            $template->feed_name = 'Stared articles';
            $per_page = 10;
            $template->article_count = PostDao::staredCount();

        } else {
            $feed = FeedDao::findById($args[':id']);
            $template->feed_name = $feed->name;
            $per_page = $feed->per_page;

            $template->article_count = PostDao::countUnreadInFeed($args[':id']);
        }

        if ( $template->article_count === 0 ) {
            header('Location: /'.(isset($args['mobi'])?'?mobi':''));
            return;
        }

        if ( $template->article_count != 0 ) {
            $template->page_count = ceil($template->article_count / $per_page);
            $template->page = min($template->page_count, $args['page']);

            if ( $args[':id'] === 'stared' ) {
                $template->articles = PostDao::findStaredArticles(
                    $per_page, max(0, $per_page * ($template->page-1)));
            } else {
                $template->articles = PostDao::findUnreadArticlesInFeed(
                    $args[':id'], $feed->newest_first ? 'DESC' : 'ASC',
                    $per_page, max(0, $per_page * ($template->page-1)));
            }

            if ( isset($args['mobi']) ) {
                foreach ( $template->articles as $article ) {
                    $article->text = self::filterHtml($article->text);
                }
            }

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
        if ( isset($args['feed']) ) {
            $url = 'feed';
            $feed_id = $args['feed'];
            $id = $feed_id;
        } else {
            $url = 'folder';
            $feed_id = null;
            $id = $args['folder'];
        }

        if ( $args['ids'] === 'all' ) {
            $ids = null;
        } else {
            $ids = explode(',', $args['ids']);
        }

        // need ID or Feed ID otherwise everything gets marked read
        if ( $ids == null && $feed_id == null ) {
            header("Location: /?err".(isset($args['mobi'])?'&mobi':''));
            exit;
        }

        PostDao::markRead($ids, $feed_id);

        header("Location: /$url/$id/articles?page={$args['page']}".(isset($args['mobi'])?'&mobi':''));
        exit;
    }

    public static function filterHtml($html){
       return preg_replace(array(
            '@<![\s\S]*?--[ \t\n\r]*>@',          // Strip multi-line comments including CDATA
            '@<script[^>]*?>.*?</script>@siU',
            '@<style[^>]*?>.*?</style>@siU',
            '@<iframe[^>]*?>.*?</iframe>@siU',
            '@<applet[^>]*?>.*?</applet>@siU',
            '@<body[^>]*?>.*?</body>@siU',
            '@<embed[^>]*?>.*?</embed>@siU',
            '@<frame[^>]*?>.*?</frame>@siU',
            '@<frameset[^>]*?>.*?</frameset>@siU',
            '@<html[^>]*?>.*?</html>@siU',
            '@<img[^>]*?>.*?</img>@siU',
            '@<layer[^>]*?>.*?</layer>@siU',
            '@<link[^>]*?>.*?</link>@siU',
            '@<ilayer[^>]*?>.*?</ilayer>@siU',
            '@<meta[^>]*?>.*?</meta>@siU',
            '@<object[^>]*?>.*?</object>@siU',
       ), '', $html);
    }
}
