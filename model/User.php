<?php
/**
 * User entry
 * @package com\izylab\reader
 */
class User extends Entity {}
User::init('user', array('id', 'username', 'password', 'salt'));
