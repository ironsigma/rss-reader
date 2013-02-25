<?php
/**
 * Criteria
 * @package com\izylab\reader
 */
class Criteria {
    private static $log;
    private $operations = array();
    private $page = null;
    private $order = null;

    public function __construct() {
        if ( !self::$log ) {
            self::$log = LogFacility::getLogger('Criteria.class');
        }
    }

    public function equal($column, $value, $type) {
        $this->operation('=', $column, $value, $type);
    }
    public function notEqual($column, $value, $type) {
        $this->operation('!=', $column, $value, $type);
    }
    public function greaterThan($column, $value, $type=SQLITE3_INTEGER) {
        $this->operation('>', $column, $value, $type);
    }
    public function lessThan($column, $value, $type=SQLITE3_INTEGER) {
        $this->operation('<', $column, $value, $type);
    }
    public function greaterThanEqual($column, $value, $type=SQLITE3_INTEGER) {
        $this->operation('<=', $column, $value, $type);
    }
    public function lessThanEqual($column, $value, $type=SQLITE3_INTEGER) {
        $this->operation('>=', $column, $value, $type);
    }
    public function in($column, $array, $type=SQLITE3_INTEGER) {
        $this->operation('in', $column, $value, $type);
    }
    public function isNull($column) {
        $this->operation('null', $column);
    }
    public function isNotNull($column) {
        $this->operation('notnull', $column);
    }
    public function true($column) {
        $this->operation('true', $column, 1, SQLITE3_INTEGER);
    }
    public function false($column) {
        $this->operation('false', $column, 0, SQLITE3_INTEGER);
    }
    public function orderBy($column, $sort) {
        $this->order = array('col' => $column, 'sort' => $sort);
    }
    public function page($limit, $offset) {
        $this->page = array('limit' => $limit, 'offset' => $offset);
    }

    /**
     * Get select statement.
     * @param string $table Table name
     * @param string|array $columns Column names, can be an array, star '*', or comma separated names.
     */
    public function select($table, $columns='*') {
        $db = Database::getInstance();
        $data = $this->generateSqlData($table, $columns);
        self::$log->trace($data['sql']);
        $statement = $db->prepare($data['sql']);
        foreach ( $data['values'] as $label => $val ) {
            self::$log->trace("$label = {$val['value']}");
            $statement->bindValue($label, $val['value'], $val['type']);
        }
        return $statement;
    }

    protected function generateSqlData($table, $columns) {
        if ( is_array($columns) ) {
            $columns = implode(', ', $columns);
        }
        $sql = "SELECT $columns FROM $table";
        $values = array();
        if ( count($this->operations ) == 0 ) {
            return array('sql' => $sql, 'values' => $values);
        }
        $labels = array();
        foreach ( $this->operations as $op ) {
            switch ( $op['op'] ) {
            case 'null':
                $labels[] = "{$op['col']} IS NULL";
                break;

            case 'notnull':
                $labels[] = "{$op['col']} NOT NULL";
                break;

            case 'in':
                $isStr = is_string($op['val'][0]);
                $glue = $isStr ? '\',\'' : ',';
                $prepost = $isStr ? '\'' : '';
                $labels[] = 'IN('. $prepost
                    . implode($glue, SQLite3::escapeString($op['val']))
                    . $prepost .')';
                break;

            default:
                $labels[] = "{$op['col']}=:{$op['col']}";
                $values[":{$op['col']}"] = array('value' => $op['val'], 'type' => $op['type']);
            }
        }
        $sql .= ' WHERE '. implode(' AND ', $labels);
        if ( $this->order ) {
            $sql .= ' ORDER BY "'. $this->order['col'] .'" '. $this->order['sort'];
        }
        if ( $this->page ) {
            $sql .= ' LIMIT '. $this->page['limit'] .' OFFSET '. $this->page['offset'];
        }
        return array('sql' => $sql, 'values' => $values);
    }
    protected function operation($operation, $column, $value=null, $type=null) {
        $this->operations[] = array(
            'op' => $operation,
            'col' => $column,
            'val' => $value,
            'type' => $type,
        );
    }
}
