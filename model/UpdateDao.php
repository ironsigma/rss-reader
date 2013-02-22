<?php
/**
 * Update Log DAO
 * @package com\izylab\reader
 */
class UpdateDao {
    public static function insert(Update $update) {
        $db = Database::getInstance();
        $st = $db->prepare('INSERT INTO update_log (ts, count, new, feed_id) VALUES (:ts, :count, :new, :id)');
        $st->bindValue(':ts', $update->updated, SQLITE3_INTEGER);
        $st->bindValue(':count', $update->count, SQLITE3_INTEGER);
        $st->bindValue(':new', $update->new, SQLITE3_INTEGER);
        $st->bindValue(':id', $update->feed_id, SQLITE3_INTEGER);
        $st->execute();
        $update->id = $db->lastInsertRowID();
    }
    public static function findLatestUpdates() {
        $db = Database::getInstance();
        $st = $db->prepare('SELECT u.id, u.ts, u.count, u.new, u.feed_id '.
           'FROM update_log u WHERE ts=(SELECT MAX(ts) FROM update_log WHERE feed_id=u.feed_id)');
        $results = $st->execute();
        $updates = array();
        while ( $row = $results->fetchArray(SQLITE3_ASSOC) ) {
            $updates[$row['feed_id']] = new Update($row['ts'], $row['count'], $row['new'], $row['feed_id'], $row['id']);
        }
        return $updates;
    }
}
