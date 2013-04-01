<?php
class Elian {
    private static $alphabet;

    public static function init() {
        static::$alphabet = array(
            'a' => '&#x14A3;', 'b' => '&#x1450;', 'c' => '&#x14A7;', 'd' => '&#x144E;',
            'e' => '&#x1610;', 'f' => '&#x144C;', 'g' => '&#x14A5;', 'h' => '&#x1455;',
            'i' => '&#x14AA;', 'j' => '&#x14ED;', 'k' => '&#x14D7;', 'l' => '&#x14F1;',
            'm' => '&#x1489;', 'n' => '&#x1566;', 'o' => '&#x148D;', 'p' => '&#x14EF;',
            'q' => '&#x14DA;', 'r' => '&#x14F4;', 's' => '&#x14F6;', 't' => '&#x14D8;',
            'u' => '&#x14F2;', 'v' => '&#x1493;', 'w' => '&#x1567;', 'x' => '&#x1499;',
            'y' => '&#x14F9;', 'z' => '&#x14DB;',/* '0' => '&#x1431;', '1' => '&#x1433;',
            '2' => '&#x142f;', '3' => '&#x1438;', '4' => '&#x15d1;', '5' => '&#x15d2;',
            '6' => '&#x15d0;', '7' => '&#x15d5;', '8' => '&#x15d7;', '9' => '&#x15d6;', */
        );
    }

    public static function encode($str) {
        $elian = '<span>';
        $curr_cls='';
        for ( $i = 0, $len = strlen($str); $i < $len; $i ++ ) {
            $char = strtolower($str[$i]);
            if ( !array_key_exists($char, static::$alphabet) ) {
                $cls = 'el0';
                $el = $str[$i];
            } else {
                $el = static::$alphabet[$char];
                if ( ord($char) >= ord('a') && ord($char) <= ord('i') ) {
                    $cls = 'el1';
                } elseif ( ord($char) >= ord('j') && ord($char) <= ord('r') ) {
                    $cls = 'el2';
                } elseif ( ord($char) >= ord('s') && ord($char) <= ord('z') ) {
                    $cls = 'el3';
                }
            }
            if ( $curr_cls != $cls ) {
                $elian .= "</span><span class='$cls'>";
                $curr_cls = $cls;
            }
            $elian .= $el;
        }
        $elian .= '</span>';
        return $elian;
    }
}
Elian::init();
