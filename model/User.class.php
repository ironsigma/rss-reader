<?php
/**
 * User entry
 * @package com\izylab\reader
 */
class User extends Entity {
    protected static $meta_data;
}
User::init('user', array('id', 'username', 'password', 'salt'));
