<?php
/**
 * Update Log DAO
 * @package com\izylab\reader
 */
class UpdateDao {
    public static function insert(Update $update) {
        $st = QueryBuilder::insert(Update::getTable(), Update::getColumns(), $update, array('id'));
        $st->execute();
        $update->id = Database::lastInsertRowID();
        return $update;
    }
    public static function findLatestUpdates() {
        $db = Database::getInstance();
        $st = $db->prepare('SELECT u.id, u.updated, u.total_count, u.new_count, u.feed_id '.
           'FROM update_log u WHERE updated=(SELECT MAX(updated) FROM update_log WHERE feed_id=u.feed_id)');
        $results = $st->execute();
        $updates = array();
        while ( $row = $results->fetchArray(SQLITE3_ASSOC) ) {
            $updates[$row['feed_id']] = new Update($row);
        }
        return $updates;
    }
}
