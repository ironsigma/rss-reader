<?php
class Config {
    protected static $valueList;

    public static function clear() {
        self::$valueList = array();
    }

    public static function all() {
        return self::$valueList;
    }

    public static function get($key, $default=null) {
        return self::scanArray(self::$valueList, explode('.', $key), $default);
    }

    public static function set($key, $value) {
        if ( $key == null ) return;
        $ref = &self::$valueList;
        $keys = explode('.', $key);
        $count = count($keys) - 1;
        for ( $i = 0; $i < $count; $i ++ ) {
            $k = $keys[$i];
            if ( array_key_exists($k, $ref) ) {
                if ( !is_array($ref[$k]) ) {
                    $ref[$k] = array('_' => $ref[$k]);
                }
            } else {
                $ref[$k] = array();
            }
            $ref = &$ref[$k];
        }
        $ref[$keys[$count]] = $value;
    }

    protected static function scanArray($values, $key_list, $default) {
        $key = $key_list[0];
        if ( $values == null || !array_key_exists($key, $values) ) {
            return $default;
        }
        if ( count($key_list) == 1 ) {
            if ( is_array($values[$key]) &&  array_key_exists('_', $values[$key]) ) {
                return $values[$key]['_'];
            }
            return $values[$key];
        }
        $new_values = $values[$key];
        if ( !is_array($new_values) ) {
            return $default;
        }
        return self::scanArray($new_values, array_slice($key_list, 1), $default);
    }

    public static function read($file) {
        self::$valueList = Spyc::YAMLLoad($file);
    }
}
