<?php
/**
 * User DAO
 * @package com\hawkprime\reader
 */
class UserDao {
    public static function insert(User $user) {
        $user->id = Database::table(User::getTable())->insert($user);
        return $user;
    }
    public static function findByUsername($username) {
        $users = Database::table(User::getTable())
            ->equal('username', $username, Entity::TYPE_STR)
            ->fetch('User');

        if ( !count($users) ) {
            return null;
        }
        return $users[0];
    }
}
