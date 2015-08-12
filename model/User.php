<?php
/**
 * User entry
 * @package com\hawkprime\reader
 */
class User extends Entity {
    protected static $meta_data;
}
User::init('user', array(
    array('name'=>'id', 'type'=>Entity::TYPE_INT),
    array('name'=>'username', 'type'=>Entity::TYPE_STR),
    array('name'=>'password', 'type'=>Entity::TYPE_STR),
    array('name'=>'salt', 'type'=>Entity::TYPE_STR),
));
