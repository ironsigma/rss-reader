<?php
/**
 * Post DAO
 * @package com\izylab\reader
 */
class PostDao {
    public static function findAll($criteria) {
        $st = QueryBuilder::select(Post::getTable(), Post::getColumns(), $criteria);
        $results = $st->execute();
        $posts = array();
        while ( $row = $results->fetchArray(SQLITE3_ASSOC) ) {
            $posts[] = new Post($row);
        }
        return $posts;
    }
    public static function countAll($criteria) {
        $criteria->count('*', 'count');
        $st = QueryBuilder::select(Post::getTable(), array(), $criteria);
        $row = $st->execute()->fetchArray(SQLITE3_NUM);
        return $row[0];
    }
    public static function insert(Post $post) {
        $st = QueryBuilder::insert(Post::getTable(), Post::getColumns(), $post, array('id'));
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
        $criteria->equal('feed_id', $feed_id, SQLITE3_INTEGER);
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
