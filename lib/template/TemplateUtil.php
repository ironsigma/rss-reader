<?php
class TemplateUtil {
    public static function abbr($string, $max_length=75) {
        if ($max_length >= strlen($string)) {
            return $string;
        }
        if ($max_length < 5) {
            return '&nbsp;&hellip;&nbsp;';
        }
        $end = -1 * intval(($max_length - 3) / 2);
        $start = $max_length - 3 + $end;
        return substr($string, 0, $start) . '&nbsp;&hellip;&nbsp;'
            . substr($string, $end);
    }
}
