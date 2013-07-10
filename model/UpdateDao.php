<?php
/**
 * Update Log DAO
 * @package com\izylab\reader
 */
class UpdateDao {
    public static function insert(Update $update) {
        $update->id = Database::table(Update::getTable())->insert($update);
        return $update;
    }
    public static function findLatestUpdates() {
        $sql = 'SELECT u.id, u.updated, u.total_count, u.new_count, u.feed_id '.
           'FROM update_log u WHERE updated=(SELECT MAX(updated) FROM update_log WHERE feed_id=u.feed_id)';

        list($statement,) = Database::connection()->execute($sql);
        $updates = $statement->fetchAll(PDO::FETCH_CLASS|PDO::FETCH_PROPS_LATE, 'Update');
        $lookup = array();
        foreach ( $updates as $update ) {
            $lookup[$update->feed_id] = $update;
        }
        return $lookup;
    }
    public static function deleteOldUpdates() {
        $sql = 'DELETE FROM update_log WHERE updated < NOW() - INTERVAL 2 WEEK';
        Database::connection()->execute($sql);
    }
}
