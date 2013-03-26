<?php
/**
 * Update Log DAO
 * @package com\izylab\reader
 */
class UpdateDao {
    public static function insert(Update $update) {
        $update->id = DB::table(Update::getTable())->insert($update);
        return $update;
    }
    public static function findLatestUpdates() {
        $sql = 'SELECT u.id, u.updated, u.total_count, u.new_count, u.feed_id '.
           'FROM update_log u WHERE updated=(SELECT MAX(updated) FROM update_log WHERE feed_id=u.feed_id)';

        list($statement,) = DB::connection()->execute($sql);
        $updates = $statement->fetchAll(PDO::FETCH_CLASS|PDO::FETCH_PROPS_LATE, 'Update');
        $lookup = array();
        foreach ( $updates as $update ) {
            $lookup[$update->feed_id] = $update;
        }
        return $lookup;
    }
}
