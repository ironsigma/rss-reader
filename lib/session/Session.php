<?php
/**
 * Session
 * @package com\izylab\reader
 */
class Session {
    public static function init() {
        ini_set('session.save_path', Config::get('session.path'));
        ini_set('session.cookie_lifetime', 1210000);
        ini_set('session.gc_maxlifetime', 1210000);
        session_start();
    }

    public static function validate($userid) {
        session_regenerate_id();
        $_SESSION['valid'] = true;
        $_SESSION['user_data'] = array('id' => $userid);
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
            $mobi = isset($_GET['mobi']) ? '?mobi' : '';
            header("Location: /login$mobi");
            return false;
        }
        return $_SESSION['user_data'];
    }

    public static function getUserId() {
        return $_SESSION['user_data']['id'];
    }
}
