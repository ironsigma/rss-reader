<?php
/**
 * Feed controller
 * @package com\izylab\reader
 */
class FolderController {

    public function articles($args) {
        $log = LogFacility::getLogger('FolderController.class');

        $folder = FolderDao::findById($args[':id']);

        $template = new Template((isset($args['mobi']) ? 'mobile_' : '').'article_list.php');
        $template->container = 'folder';
        $template->feed_id = $args[':id'];
        $template->feed_name = $folder->name;
        $per_page = 10; // TODO: folder count

        $count_criteria = new Criteria();
        $count_criteria->false('read');
        $count_criteria->join('feed', 'id', 'feed_id');
        $count_criteria->join('folder', 'id', 'feed.folder_id');
        $count_criteria->equal('folder.id', $args[':id'], SQLITE3_INTEGER);
        $template->article_count = PostDao::countAll($count_criteria);
        $log->info("Count: {$template->article_count}");

        if ( $template->article_count === 0 ) {
            header('Location: /'.(isset($args['mobi'])?'?mobi':''));
            return;
        }

        $criteria = new Criteria();
        $criteria->false('read');
        $criteria->join('feed', 'id', 'feed_id');
        $criteria->join('folder', 'id', 'feed.folder_id');
        $criteria->equal('folder.id', $args[':id'], SQLITE3_INTEGER);
        $criteria->orderBy('published', $folder->newest_first ? 'DESC' : 'ASC');

        $template->page_count = ceil($template->article_count / $per_page);
        $template->page = min($template->page_count, $args['page']);
        $criteria->page($per_page, $per_page * ($template->page-1));

        $template->articles = PostDao::findAll($criteria, array(array('feed.name', 'feed')));
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
