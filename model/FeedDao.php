<?php
/**
 * Feed DAO
 * @package com\izylab\reader
 */
class FeedDao {
    public static function findById($id) {
        $criteria = new Criteria();
        $criteria->equal('id', $id, SQLITE3_INTEGER);
        $st = $criteria->select(Feed::getTable(), Feed::getColumns());
        $results = $st->execute();
        $row = $results->fetchArray(SQLITE3_ASSOC);
        return new Feed($row);
    }

    public static function insert($feed){
        $db = Database::getInstance();
        $st = $db->prepare('INSERT INTO feed (name, url, sort_dir, update_freq, folder_id) '.
            'VALUES (:name, :url, :sort, :update, :freq, :id)');
        $st->bindValue(':name', $feed->name, SQLITE3_TEXT);
        $st->bindValue(':url', $feed->url, SQLITE3_TEXT);
        $st->bindValue(':sort', $feed->sort_dir, SQLITE3_TEXT);
        $st->bindValue(':freq', $feed->update_freq, SQLITE3_INTEGER);

        if ( $feed->folder_id === null ) {
            $st->bindValue(':id', null, SQLITE3_NULL);
        } else {
            $st->bindValue(':id', $feed->folder_id, SQLITE3_INTEGER);
        }

        $st->execute();
        $feed->id = $db->lastInsertRowID();
    }

    public static function findAll() {
        $criteria = new Criteria();
        $st = $criteria->select(Feed::getTable(), Feed::getColumns());
        $results = $st->execute();
        $feeds = array();
        while ( $row = $results->fetchArray(SQLITE3_ASSOC) ) {
            $feeds[] = new Feed($row);
        }
        return $feeds;
    }

    public static function findAllWithUnreadCount() {
        $db = Database::getInstance();
        $sql = 'SELECT f.id, f.name, f.url, f.sort_dir, f.update_freq, f.per_page, f.folder_id, COUNT(*) AS unread '
            .'FROM FEED f LEFT JOIN post p ON p.feed_id = f.id '
            .'WHERE p.read=:read GROUP BY f.id ORDER BY f.name';
        $st = $db->prepare($sql);
        $st->bindValue(':read', false, SQLITE3_INTEGER);
        $results = $st->execute();
        $feeds = array();
        while ( $row = $results->fetchArray(SQLITE3_ASSOC) ) {
            $feeds[] = new Feed($row);
        }
        return $feeds;
    }
}
