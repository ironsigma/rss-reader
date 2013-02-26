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
    public static function countAll($args) {
        $db = Database::getInstance();
        $st = $db->prepare('SELECT count(*) FROM post WHERE read=:read AND feed_id=:feed_id');
        $st->bindValue(':read', $args['read'], SQLITE3_INTEGER);
        $st->bindValue(':feed_id', $args['feed_id'], SQLITE3_INTEGER);
        $row = $st->execute()->fetchArray(SQLITE3_NUM);
        return $row[0];
    }
    public static function countStared() {
        $db = Database::getInstance();
        $st = $db->prepare('SELECT count(*) FROM post WHERE stared=:stared');
        $st->bindValue(':stared', 1, SQLITE3_INTEGER);
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
        $db = Database::getInstance();
        $st = $db->prepare('SELECT count(guid) FROM post WHERE guid=:guid');
        $st->bindValue(':guid', $post->guid, SQLITE3_TEXT);
        $results = $st->execute();
        $row = $results->fetchArray(SQLITE3_NUM);
        return $row[0] !== 0;
    }
    public static function deleteReadPostBefore($date) {
        $db = Database::getInstance();
        $st = $db->prepare('DELETE FROM post WHERE ts <= :ts AND read = :read AND stared = :stared');
        $st->bindValue(':ts', $date, SQLITE3_INTEGER);
        $st->bindValue(':read', 1, SQLITE3_INTEGER);
        $st->bindValue(':stared', 0, SQLITE3_INTEGER);
        $st->execute();
    }
    public static function markRead($ids, $feed_id) {
        $db = Database::getInstance();
        $sql = 'UPDATE post SET read=:read WHERE feed_id=:feed';
        if ( $ids !== null ) {
            $sql .= ' AND id IN('. $ids .')';
        }
        $st = $db->prepare($sql);
        $st->bindValue(':read', 1, SQLITE3_INTEGER);
        $st->bindValue(':feed', $feed_id, SQLITE3_INTEGER);
        $st->execute();
    }
    public static function updateStar($star, $id) {
        $db = Database::getInstance();
        $st = $db->prepare('UPDATE post SET stared=:star WHERE id=:id');
        $st->bindValue(':star', $star, SQLITE3_INTEGER);
        $st->bindValue(':id', $id, SQLITE3_INTEGER);
        $st->execute();
    }
}
