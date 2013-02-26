<?php
/**
 * User DAO
 * @package com\izylab\reader
 */
class UserDao {
    public static function insert(User $user) {
        $st = QueryBuilder::insert(User::getTable(), User::getColumns(), $user, array('id'));
        $st->execute();
        $user->id = Database::lastInsertRowID();
        return $user;
    }
    public static function findByUsername($username) {
        $criteria = new Criteria();
        $criteria->equal('username', $username, SQLITE3_TEXT);
        $st = QueryBuilder::select(User::getTable(), User::getColumns(), $criteria);
        $results = $st->execute();
        $row = $results->fetchArray(SQLITE3_ASSOC);
        if ( !isset($row['id']) ) {
            return null;
        }
        return new User($row);
    }
}
