<?php
abstract class Entity {
    const TYPE_BOOL = 1;
    const TYPE_INT = 2;
    const TYPE_STR = 3;
    const TYPE_REAL = 4;
    const TYPE_DATE = 5;
    const TYPE_TIME = 6;
    const TYPE_DATETIME = 7;
    const TYPE_BLOB = 8;
    protected $values = array();

    public function __construct(array $values=null) {
        foreach ( static::$meta_data['column_names'] as $col ) {
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
        $col_names = array();
        foreach ( $columns as $col ) {
            $col_names[] = $col['name'];
        }
        static::$meta_data = array(
            'table' => $table,
            'column_names' => $col_names,
            'columns' => $columns,
            'properties' => $properties,
        );
    }

    public static function getColumnNames() {
        return static::$meta_data['column_names'];
    }

    public static function getColumns() {
        return static::$meta_data['columns'];
    }

    public static function getTable() {
        return static::$meta_data['table'];
    }

    public function getValues() {
        return $this->values;
    }
}
