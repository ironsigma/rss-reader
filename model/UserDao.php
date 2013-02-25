<?php
/**
 * User DAO
 * @package com\izylab\reader
 */
class UserDao {
    public static function insert(User $user) {
        $db = Database::getInstance();
        $st = $db->prepare('INSERT INTO user (username, password, salt) '.
            'VALUES (:uname, :pass, :salt)');

        $st->bindValue(':uname', $user->username, SQLITE3_TEXT);
        $st->bindValue(':pass', $user->password, SQLITE3_TEXT);
        $st->bindValue(':salt', $user->salt, SQLITE3_TEXT);
        $st->execute();
        $user->id = $db->lastInsertRowID();
    }
    public static function findByUsername($username) {
        $criteria = new Criteria();
        $criteria->equal('username', $username, SQLITE3_TEXT);
        $st = $criteria->select(User::getTable(), User::getColumns());
        $results = $st->execute();
        $row = $results->fetchArray(SQLITE3_ASSOC);
        if ( !isset($row['id']) ) {
            return null;
        }
        return new User($row);
    }
}
