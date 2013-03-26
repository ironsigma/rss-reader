<?php
/**
 * Post DAO
 * @package com\izylab\reader
 */
class PostDao {
    public static function findUnreadArticlesInFolder($folder_id, $order, $limit, $offset) {
        return DB::table(Post::getTable())
            ->false('read')
            ->join('feed', 'id', 'feed_id')
            ->join('folder', 'id', 'feed.folder_id')
            ->equal('folder.id', $folder_id, Entity::TYPE_INT)
            ->orderBy('published', $order)
            ->page($limit, $offset)
            ->select(array_merge(Post::getColumnNames(), array(array('feed.name', 'feed'))))
            ->fetch('Post');
    }
    public static function findStaredArticles($limit, $offset) {
        return DB::table(Post::getTable())
            ->join('feed', 'id', 'feed_id')
            ->true('stared')
            ->orderBy('published', 'ASC')
            ->page($limit, $offset)
            ->select(array_merge(Post::getColumnNames(), array(array('feed.name', 'feed'))))
            ->fetch('Post');
    }
    public static function findUnreadArticlesInFeed($feed_id, $order, $limit, $offset) {
        return DB::table(Post::getTable())
            ->equal('feed_id', $feed_id, Entity::TYPE_INT)
            ->false('read')
            ->orderBy('published', $order)
            ->page($limit, $offset)
            ->fetch('Post');
    }
    public static function postFolderCount() {
        $results = DB::table(Post::getTable())
                ->count('*', 'count')
                ->false('read')
                ->join('feed', 'id', 'feed_id')
                ->join('folder', 'id', 'feed.folder_id')
                ->groupBy('folder.id')
                ->select(array('folder.id'))
                ->fetch();

        $counts = array();
        foreach ( $results as $row ) {
            $counts[$row['id']] = intval($row['count']);
        }
        return $counts;
    }
    public static function staredCount() {
        $result = DB::table(Post::getTable())
                ->count('*', 'count')
                ->true('stared')
                ->first();
        return intval($result['count']);
    }
    public static function countUnreadInFeed($feed_id) {
        $result = DB::table(Post::getTable())
                ->count('*', 'count')
                ->false('read')
                ->equal('feed_id', $feed_id, Entity::TYPE_INT)
                ->first();
        return intval($result['count']);
    }
    public static function countUnreadInFolder($folder_id) {
        $result = DB::table(Post::getTable())
                ->count('*', 'count')
                ->false('read')
                ->join('feed', 'id', 'feed_id')
                ->join('folder', 'id', 'feed.folder_id')
                ->equal('folder.id', $folder_id, Entity::TYPE_INT)
                ->first();
        return intval($result['count']);
    }
    public static function insert(Post $post) {
        $post->id = DB::table(Post::getTable())
            ->insert($post);
        return $post;
    }
    public static function postExists($post) {
        $result = DB::table(Post::getTable())
            ->count('*', 'count')
            ->equal('guid', $post->guid, Entity::TYPE_INT)
            ->first();

        return intval($result['count']) !== 0;
    }
    public static function markRead($ids, $feed_id) {
        $query = DB::table(Post::getTable());

        if ( $feed_id !== null ) {
            $query->equal('feed_id', $feed_id, Entity::TYPE_INT);
        }
        if ( $ids !== null ) {
            $query->in('id', $ids, Entity::TYPE_INT);
        }

        $entity = new Post(array('read' => true));
        $query->update($entity, array('read'));
    }
    public static function updateStar($star, $id) {
        $entity = new Post(array('stared' => $star));
        DB::table(Post::getTable())
            ->equal('id', $id, Entity::TYPE_INT)
            ->update($entity, array('stared'));
    }
}
