<?php
/**
 * Criteria
 * @package com\izylab\reader
 */
class QueryBuilder {
    private static $log;
    private static $prefix_count = 0;

    public static function init() {
        self::$log = LogFacility::getLogger('QueryBuilder.class');
        self::$prefix_count = 0;
    }

    public static function select($table, array $columns, Criteria $criteria) {
        // Create table prefixes
        self::$prefix_count = 0;
        $prefix_list = array('::' => self::generatePrefix($table));
        foreach ( $criteria->getJoins() as $join ) {
            $prefix_list[$join['table']] = self::generatePrefix($join['table']);
        }

        // append table prefixes to columns
        for( $i = 0, $c = count($columns); $i < $c; $i ++ ) {
            $columns[$i] = self::getPrefixedColumn($prefix_list, $columns[$i]);
        }

        // add counts
        foreach ( $criteria->getCounts() as $c ) {
            $columns[] = "COUNT(\"{$c['item']}\") AS \"{$c['name']}\"";
        }

        // convert column names to comma separated list
        $columns = implode(', ', $columns);

        // start SELECT statement
        $sql = "SELECT $columns FROM $table {$prefix_list['::']}";

        // append joins
        foreach ( $criteria->getJoins() as $join ) {
            $p = $prefix_list[$join['table']];
            $this_col = self::getPrefixedColumn($prefix_list, $join['this_col']);
            $sql .= " {$join['type']} JOIN {$join['table']} $p ON "
                ."$p.\"{$join['table_col']}\" = $this_col";
        }

        $where_clause = static::whereClause($criteria->getOperations(), $prefix_list);

        $sql .= ' '. $where_clause['sql'];

        $group = $criteria->getGroupBy();
        if ( $group ) {
            $sql .= ' GROUP BY '. self::getPrefixedColumn($prefix_list, $group['col']);
        }
        $order = $criteria->getOrder();
        if ( $order ) {
            $sql .= ' ORDER BY '. self::getPrefixedColumn($prefix_list, $order['col']) .' '. $order['sort'];
        }
        $page = $criteria->getPage();
        if ( $page ) {
            $sql .= ' LIMIT '. $page['limit'] .' OFFSET '. $page['offset'];
        }
        return self::getStatement(array('sql' => $sql, 'values' => $where_clause['values']));
    }

    public static function insert($table, array $columns, $entity, array $exclude=array()) {
        $cols = array();
        $values = array();
        $labels = array();
        $entity_values = $entity->getValues();
        foreach ( $columns as $col ) {
            if ( in_array($col, $exclude) ) {
                continue;
            }
            $cols[] = $col;
            $labels[] = ':'. $col;
            $value = $entity_values[$col];
            $type = gettype($value);
            switch ( $type ) {
            case 'boolean': $sql_type = SQLITE3_INTEGER; $value ? 1 : 0; break;
            case 'integer': $sql_type = SQLITE3_INTEGER; break;
            case 'double':  $sql_type = SQLITE3_FLOAT;   break;
            case 'string':  $sql_type = SQLITE3_TEXT; break;
            case 'NULL':    $sql_type = SQLITE3_NULL;    break;
            default:
                throw new Exception("Invalid type in column $col, value: $value, type: $type");
            }
            $values[':'.$col] = array('value' => $value, 'type' => $sql_type);
        }
        $sql = "INSERT INTO $table (". implode(', ', $cols) .') VALUES ('. join(', ', $labels) . ')';
        return self::getStatement(array('sql' => $sql, 'values' => $values));
    }

    public static function update($table, array $columns, $entity, Criteria $criteria) {
        $values = array();
        $update = array();
        $entity_values = $entity->getValues();
        foreach ( $columns as $col ) {
            $value = $entity_values[$col];
            $type = gettype($value);
            switch ( $type ) {
            case 'boolean': $sql_type = SQLITE3_INTEGER; $value ? 1 : 0; break;
            case 'integer': $sql_type = SQLITE3_INTEGER; break;
            case 'double':  $sql_type = SQLITE3_FLOAT;   break;
            case 'string':  $sql_type = SQLITE3_TEXT; break;
            case 'NULL':    $sql_type = SQLITE3_NULL;    break;
            default:
                throw new Exception("Invalid type in column $col, value: $value, type: $type");
            }
            $update[] = "\"$col\"=:$col";
            $values[':'.$col] = array('value' => $value, 'type' => $sql_type);
        }
        $where_clause = self::whereClause($criteria->getOperations(), array('::' => $table));
        $sql = "UPDATE $table SET ". join(', ', $update) .' '. $where_clause['sql'];
        return self::getStatement(array('sql' => $sql, 'values' => array_merge($values, $where_clause['values'])));
    }

    public static function delete($table, Criteria $criteria) {
        $where_clause = self::whereClause($criteria->getOperations(), array('::' => $table));
        return self::getStatement(array(
            'sql' => "DELETE FROM $table ". $where_clause['sql'],
            'values' => $where_clause['values'],
        ));
    }

    protected static function generatePrefix($name) {
        self::$prefix_count++;
        return "_{$name[0]}". self::$prefix_count;
    }

    protected static function getPrefixedColumn($joins, $column, $original=false) {
        $alias = '';
        if ( is_array($column) ) {
            $alias = " AS \"{$column[1]}\"";
            $column = $column[0];
        }
        if ( strpos($column, '.') !== false ) {
            list($table, $column) = explode('.', $column);
            $col = $joins[$table] .'."'. $column .'"'. $alias;
        } else {
            $col = $joins['::'] .'."'. $column .'"'. $alias;
        }
        if ( $original ) {
            return array($col, $column);
        }
        return $col;
    }

    protected static function whereClause($operations, $prefix_list) {
        if ( count($operations) == 0 ) {
            return array('sql' => '', 'values' => array());
        }
        $where_sql_components = array();
        $where_values = array();
        foreach ( $operations as $op ) {

            // prefix column names
            list($prefix_col, $label) = self::getPrefixedColumn($prefix_list, $op['col'], true);
            $label = ":$label";

            // handle each operation
            switch ( $op['op'] ) {

            // IS NULL
            case 'null':
                $where_sql_components[] = "$prefix_col IS NULL";
                break;

            // NOT NULL
            case 'notnull':
                $where_sql_components[] = "$prefix_col NOT NULL";
                break;

            // IN ( ... )
            case 'in':
                $idx = 0;
                $in_labels = array();
                foreach ( $op['val'] as $value ) {
                    $idx++;
                    $ilabel = "{$label}_in$idx";
                    $in_labels[] = $ilabel;
                    $where_values[$ilabel] = array('value' => $value, 'type' => $op['type']);
                }
                $where_sql_components[] = "$prefix_col IN(". implode(',', $in_labels) .")";
                break;

            // Boolean true false
            case 'true':
            case 'false':
                $where_sql_components[] = "$prefix_col=$label";
                $where_values[$label] = array('value' => $op['val'], 'type' => $op['type']);
                break;

            // Compare
            case '=':
            case '!=':
            case '>':
            case '<':
            case '<=':
            case '>=':
                $where_sql_components[] = "$prefix_col{$op['op']}$label";
                $where_values[$label] = array('value' => $op['val'], 'type' => $op['type']);
                break;

            // Error
            default:
                throw new Exception('Invalid SQL operation');
            }
        }
        return array(
            'sql' => 'WHERE '. implode(' AND ', $where_sql_components),
            'values' => $where_values
        );
    }

    protected static function getStatement($sql_and_values) {
        $db = Database::getInstance();
        self::$log->trace($sql_and_values['sql']);
        $statement = $db->prepare($sql_and_values['sql']);
        foreach ( $sql_and_values['values'] as $label => $val ) {
            self::$log->trace("$label = ". (
                gettype($val['value']) == 'boolean' ?
                    ( $val['value'] ? 'true' : 'false' ) :
                    $val['value']
            ));
            $statement->bindValue($label, $val['value'], $val['type']);
        }
        return $statement;
    }

}
QueryBuilder::init();
