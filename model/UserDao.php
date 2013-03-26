<?php
/**
 * User DAO
 * @package com\izylab\reader
 */
class UserDao {
    public static function insert(User $user) {
        $user->id = DB::table(User::getTable())->insert($user);
        return $user;
    }
    public static function findByUsername($username) {
        $users = DB::table(User::getTable())
            ->equal('username', $username, Entity::TYPE_STR)
            ->fetch('User');

        if ( !count($users) ) {
            return null;
        }
        return $users[0];
    }
}
