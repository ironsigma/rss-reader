<?php
/**
 * Post DAO
 * @package com\izylab\reader
 */
class PostDao {
    public static function findAll($args) {
        $db = Database::getInstance();
        $st = $db->prepare('SELECT id, title, ts, text, link, read, stared, guid, feed_id '
            .'FROM post WHERE read=:read AND feed_id=:feed_id '
            .'ORDER BY ts '. $args['sort'] .' LIMIT :limit OFFSET :offset');
        $st->bindValue(':read', $args['read'], SQLITE3_INTEGER);
        $st->bindValue(':feed_id', $args['feed_id'], SQLITE3_INTEGER);
        $st->bindValue(':limit', $args['limit'], SQLITE3_INTEGER);
        $st->bindValue(':offset', $args['offset'], SQLITE3_INTEGER);
        $results = $st->execute();
        $posts = array();
        while ( $row = $results->fetchArray(SQLITE3_ASSOC) ) {
            $posts[] = new Post(
                $row['title'],
                $row['ts'],
                $row['link'],
                $row['guid'],
                $row['text'],
                $row['read'],
                $row['stared'],
                $row['feed_id'],
                $row['id']
            );
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
    public static function insert(Post $post) {
        $db = Database::getInstance();
        $st = $db->prepare('INSERT INTO post (title, ts, text, link, read, stared, guid, feed_id) '.
            'VALUES (:title, :ts, :text, :link, :read, :stared, :guid, :id)');

        $st->bindValue(':title', $post->title, SQLITE3_TEXT);
        $st->bindValue(':ts', $post->ts, SQLITE3_INTEGER);
        $st->bindValue(':text', $post->text, SQLITE3_TEXT);
        $st->bindValue(':link', $post->link, SQLITE3_TEXT);
        $st->bindValue(':read', $post->read, SQLITE3_INTEGER);
        $st->bindValue(':stared', $post->stared, SQLITE3_INTEGER);
        $st->bindValue(':guid', $post->guid, SQLITE3_TEXT);

        if ( $post->feed_id === null ) {
            $st->bindValue(':id', null, SQLITE3_NULL);
        } else {
            $st->bindValue(':id', $post->feed_id, SQLITE3_INTEGER);
        }

        $st->execute();
        $post->id = $db->lastInsertRowID();
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
        $st->bindValue(':read', true, SQLITE3_INTEGER);
        $st->bindValue(':stared', false, SQLITE3_INTEGER);
        $st->execute();
    }
    public static function markRead($ids, $feed_id) {
        $db = Database::getInstance();
        $sql = 'UPDATE post SET read=:read WHERE feed_id=:feed';
        if ( $ids !== null ) {
            $sql .= ' AND id IN('. $ids .')';
        }
        $st = $db->prepare($sql);
        $st->bindValue(':read', true, SQLITE3_INTEGER);
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
