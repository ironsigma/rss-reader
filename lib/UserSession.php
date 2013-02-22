<?php
/**
 * User Session
 * @package com\izylab\reader
 */
class UserSession {
    public static function init() {
        session_start();
    }

    public static function validate($userid) {
        session_regenerate_id();
        $_SESSION['valid'] = true;
        $_SESSION['userid'] = $userid;
    }

    public static function isLoggedin() {
        return isset($_SESSION['valid']) && $_SESSION['valid'];
    }

    public static function logout() {
        $_SESSION = array();
        session_destroy();
    }

    public static function requireLogin() {
        if ( !self::isLoggedin() ) {
            header("Location: /login");
        }
    }
}
