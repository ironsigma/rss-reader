<?php
/**
 * Feed controller
 * @package com\izylab\reader
 */
class FolderController {

    public function articles($args) {
        $log = LogFacility::getLogger('FolderController.class');

        $folder = FolderDao::findById($args[':id']);

        $template = new Template('article_list');
        $template->container = 'folder';
        $template->feed_id = $args[':id'];
        $template->feed_name = $folder->name;
        $per_page = $folder->per_page;

        $template->article_count = PostDao::countUnreadInFolder($args[':id']);
        $log->info("Count: {$template->article_count}");

        if ( $template->article_count === 0 ) {
            header('Location: /'.(isset($args['mobi'])?'?mobi':''));
            return;
        }

        $template->page_count = ceil($template->article_count / $per_page);
        $template->page = min($template->page_count, $args['page']);

        $template->articles = PostDao::findUnreadArticlesInFolder(
            $args[':id'], $folder->newest_first ? 'DESC' : 'ASC',
            $per_page, max(0, $per_page * ($template->page-1)));

        $log->info("Articles: ". count($template->articles));

        if ( isset($args['mobi']) ) {
            foreach ( $template->articles as $article ) {
                $article->text = FeedController::filterHtml($article->text);
            }
        }

        $ids = array();
        foreach ( $template->articles as $article ) {
            $ids[] = $article->id;
        }
        $template->article_ids = join(',', $ids);

        $template->display();
    }
}
