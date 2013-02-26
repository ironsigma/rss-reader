<?php
/**
 * Criteria
 * @package com\izylab\reader
 */
class QueryBuilder {
    private static $log;

    public static function init() {
        self::$log = LogFacility::getLogger('QueryBuilder.class');
    }

    /**
     * Get select statement.
     * @param string $table Table name
     * @param string|array $columns Column names, can be an array, star '*', or comma separated names.
     */
    public static function select($table, $columns='*', Criteria $criteria) {
        $sql_and_values = self::generateSelectSqlAndValues($table, $columns, 
            $criteria->getOperations(), $criteria->getPage(), $criteria->getOrder());

        $db = Database::getInstance();
        self::$log->trace($sql_and_values['sql']);
        $statement = $db->prepare($sql_and_values['sql']);
        foreach ( $sql_and_values['values'] as $label => $val ) {
            self::$log->trace("$label = {$val['value']}");
            $statement->bindValue($label, $val['value'], $val['type']);
        }
        return $statement;
    }

    protected static function generateSelectSqlAndValues($table, $columns, $operations, $page, $order) {
        if ( is_array($columns) ) {
            $columns = implode(', ', $columns);
        }
        $sql = "SELECT $columns FROM $table";
        $values = array();
        if ( count($operations ) == 0 ) {
            return array('sql' => $sql, 'values' => $values);
        }
        $labels = array();
        foreach ( $operations as $op ) {
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
        if ( $order ) {
            $sql .= ' ORDER BY "'. $order['col'] .'" '. $order['sort'];
        }
        if ( $page ) {
            $sql .= ' LIMIT '. $page['limit'] .' OFFSET '. $page['offset'];
        }
        return array('sql' => $sql, 'values' => $values);
    }

    public static function insert($table, array $columns, $entity, array $exclude=array()) {
        $sql_and_values = self::generateInsertSqlAndValues($table, $columns, $entity->getValues(), $exclude);
        $db = Database::getInstance();
        self::$log->trace($sql_and_values['sql']);
        $statement = $db->prepare($sql_and_values['sql']);
        foreach ( $sql_and_values['values'] as $label => $val ) {
            self::$log->trace("$label = {$val['value']}");
            $statement->bindValue($label, $val['value'], $val['type']);
        }
        return $statement;
    }

    protected static function generateInsertSqlAndValues($table, array $columns, array $entity_values, array $exclude) {
        $cols = array();
        $values = array();
        $labels = array();
        foreach ( $columns as $col ) {
            if ( in_array($col, $exclude) ) {
                continue;
            }
            $cols[] = $col;
            $labels[] = ':'. $col;
            $value = $entity_values[$col];
            switch ( gettype($value) ) {
            case 'boolean': $type = SQLITE3_INTEGER; $value ? 1 : 0; break;
            case 'integer': $type = SQLITE3_INTEGER; break;
            case 'double':  $type = SQLITE3_FLOAT;   break;
            case 'string':  $type = SQLITE3_TEXT; break;
            case 'NULL':    $type = SQLITE3_NULL;    break;
            default:
                throw new Exception('Invalid data type');
            }
            $values[':'.$col] = array('value' => $value, 'type' => $type);
        }
        $sql = "INSERT INTO $table (". implode(', ', $cols) .') VALUES ('. join(', ', $labels) . ')';
        return array('sql' => $sql, 'values' => $values);
    }
}
QueryBuilder::init();
