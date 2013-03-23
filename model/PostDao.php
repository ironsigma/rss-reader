<?php
/**
 * Post DAO
 * @package com\izylab\reader
 */
class PostDao {
    public static function findAll($criteria, $columns=null) {
        if ( $columns ) {
            $columns = array_merge($columns, Post::getColumnNames());
        } else {
            $columns = Post::getColumnNames();
        }
        $st = QueryBuilder::select(Post::getTable(), $columns, $criteria);
        $results = $st->execute();
        $posts = array();
        while ( $row = $results->fetchArray(SQLITE3_ASSOC) ) {
            $posts[] = new Post($row);
        }
        return $posts;
    }
    public static function postFolderCount() {
        $criteria = new Criteria();
        $criteria->false('read');
        $criteria->join('feed', 'id', 'feed_id');
        $criteria->join('folder', 'id', 'feed.folder_id');
        $criteria->count('*', 'count');
        $criteria->groupBy('folder.id');
        $st = QueryBuilder::select(Post::getTable(), array('folder.id'), $criteria);
        $results = $st->execute();

        $counts = array();
        while ( $row = $results->fetchArray(SQLITE3_ASSOC) ) {
            $counts[$row['id']] = $row['count'];
        }
        return $counts;
    }
    public static function countAll($criteria) {
        $criteria->count('*', 'count');
        $st = QueryBuilder::select(Post::getTable(), array(), $criteria);
        $row = $st->execute()->fetchArray(SQLITE3_NUM);
        return $row[0];
    }
    public static function insert(Post $post) {
        $st = QueryBuilder::insert(Post::getTable(), Post::getColumnNames(), $post, array('id'));
        $st->execute();
        $post->id = Database::lastInsertRowID();
        return $post;
    }
    public static function postExists($post) {
        $criteria = new Criteria();
        $criteria->count('guid', 'guid_count');
        $criteria->equal('guid', $post->guid, SQLITE3_TEXT);
        $st = QueryBuilder::select(Post::getTable(), array(), $criteria);
        $results = $st->execute();
        $row = $results->fetchArray(SQLITE3_NUM);
        return $row[0] !== 0;
    }
    public static function deleteReadPostBefore($date) {
        $criteria = new Criteria();
        $criteria->lessThanEqual('published', $date);
        $criteria->true('read');
        $criteria->false('stared');

        $st = QueryBuilder::delete(Post::getTable(), $criteria);
        $st->execute();
    }
    public static function markRead($ids, $feed_id) {
        $criteria = new Criteria();
        if ( $feed_id !== null ) {
            $criteria->equal('feed_id', $feed_id, SQLITE3_INTEGER);
        }
        if ( $ids !== null ) {
            $criteria->in('id', $ids, SQLITE3_INTEGER);
        }

        $entity = new Post(array('read' => true));
        $st = QueryBuilder::update(Post::getTable(), array('read'), $entity, $criteria);
        $st->execute();
    }
    public static function updateStar($star, $id) {
        $criteria = new Criteria();
        $criteria->equal('id', $id, SQLITE3_INTEGER);

        $entity = new Post(array('stared' => $star));
        $st = QueryBuilder::update(Post::getTable(), array('stared'), $entity, $criteria);
        $st->execute();
    }
}
