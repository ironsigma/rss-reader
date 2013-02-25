<?php
abstract class Entity {
    protected static $table;
    protected static $columns;
    protected $values = array();

    public function __construct(array $values=null) {
        foreach ( self::$columns as $key ) {
            $this->values[$key] = null;
        }
        if ( $values ) {
            foreach ( $values as $key => $value ) {
                $this->__set($key, $value);
            }
        }
    }

    public function __set($name, $value) {
        if ( !array_key_exists($name, $this->values) ) {
            throw new Exception('Undefined property named '. $name);
        }
        $this->values[$name] = $value;
    }

    public function __get($name) {
        if ( !array_key_exists($name, $this->values) ) {
            throw new Exception('Undefined property named '. $name);
        }
        return $this->values[$name];
    }

    protected function addProperty($property, $value=null) {
        $this->values[$property] = $value;
    }

    public static function init($table, $columns) {
        self::$table = $table;
        self::$columns = $columns;
    }

    public static function getColumns() {
        return self::$columns;
    }

    public static function getTable() {
        return self::$table;
    }
}
