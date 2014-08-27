<?php
class Base64 {
    public static function encode($string) {
        $enc = base64_encode($string);
        $enc = str_replace('=', '~', $enc);
        $enc = str_replace('+', '-', $enc);
        return str_replace('/', '_', $enc);
    }

    public static function decode($string) {
        $dec = str_replace('~', '=', $string);
        $dec = str_replace('-', '+', $dec);
        $dec = str_replace('_', '/', $dec);
        return base64_decode($dec);
    }
}
