<?php
/**
 * User entry
 * @package com\izylab\reader
 */
class User {
    public $id;
    public $username;
    public $password;
    public $salt;
    public function __construct($username, $password, $salt, $id=null) {
        $this->id = $id;
        $this->username = $username;
        $this->password = $password;
        $this->salt = $salt;
    }
}
