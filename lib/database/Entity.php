<?php
abstract class Entity {
    protected $values = array();

    public function __construct(array $values=null) {
        foreach ( static::$meta_data['columns'] as $col ) {
            $this->values[$col] = null;
        }
        foreach ( static::$meta_data['properties'] as $prop ) {
            $this->values[$prop] = null;
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

    public static function init($table, array $columns, array $properties=array()) {
        static::$meta_data = array(
            'table' => $table,
            'columns' => $columns,
            'properties' => $properties,
        );
    }

    public static function getColumns() {
        return static::$meta_data['columns'];
    }

    public static function getTable() {
        return static::$meta_data['table'];
    }
}
