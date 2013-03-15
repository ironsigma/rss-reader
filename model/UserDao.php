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
        $users = DB::from(User::getTable())
            ->equal('username', $username, PDO::PARAM_STR)
            ->fetch('User');

        if ( !count($users) ) {
            return null;
        }
        return $users[0];
    }
}
