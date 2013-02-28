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

    protected static function generatePrefix($name) {
        self::$prefix_count++;
        return "_{$name[0]}". self::$prefix_count;
    }

    /**
     * Get select statement.
     * @param string $table Table name
     * @param array $columns Column names
     */
    public static function select($table, array $columns, Criteria $criteria) {
        $sql_and_values = self::generateSelectSqlAndValues($table, $columns, $criteria);

        $db = Database::getInstance();
        self::$log->trace($sql_and_values['sql']);
        $statement = $db->prepare($sql_and_values['sql']);
        foreach ( $sql_and_values['values'] as $label => $val ) {
            self::$log->trace("$label = {$val['value']}");
            $statement->bindValue($label, $val['value'], $val['type']);
        }
        return $statement;
    }

    /**
     * Add prefix to column.
     * @param $column column name with optional table prefix or name and alias array.
     */
    protected static function getPrefixedColumn($prefix, $joins, $column, $original=false) {
        $alias = '';
        if ( is_array($column) ) {
            $alias = " AS \"{$column[1]}\"";
            $column = $column[0];
        }
        if ( strpos($column, '.') !== false ) {
            list($table, $column) = explode('.', $column);
            $col = $joins[$table] .'."'. $column .'"'. $alias;
        } else {
            $col = $prefix .'."'. $column .'"'. $alias;
        }
        if ( $original ) {
            return array($col, $column);
        }
        return $col;
    }

    protected static function generateSelectSqlAndValues($table, array $columns, Criteria $criteria) {
        // Create table prefixes
        self::$prefix_count = 0;
        $prefix = self::generatePrefix($table);
        $join_prefixes = array();
        foreach ( $criteria->getJoins() as $join ) {
            $join_prefixes[$join['table']] = self::generatePrefix($join['table']);
        }

        // append table prefixes to columns
        for( $i = 0, $c = count($columns); $i < $c; $i ++ ) {
            $columns[$i] = self::getPrefixedColumn($prefix, $join_prefixes, $columns[$i]);
            self::$log->info('Adding col: '. $columns[$i]);
        }

        // add counts
        foreach ( $criteria->getCounts() as $c ) {
            $columns[] = "COUNT(\"{$c['item']}\") AS \"{$c['name']}\"";
        }

        // convert column names to comma separated list
        $columns = implode(', ', $columns);

        // start SELECT statement
        $sql = "SELECT $columns FROM $table $prefix";

        // append joins
        foreach ( $criteria->getJoins() as $join ) {
            $p = $join_prefixes[$join['table']];
            $sql .= " {$join['type']} JOIN {$join['table']} $p ON "
                ."$p.\"{$join['table_col']}\" = $prefix.\"{$join['this_col']}\"";
        }

        // generate WHERE caluse
        $operations = $criteria->getOperations();
        $where_sql_components = array();
        $where_values = array();
        foreach ( $operations as $op ) {

            // prefix column names
            list($prefix_col, $label) = self::getPrefixedColumn($prefix, $join_prefixes, $op['col'], true);
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
        if ( count($operations) != 0 ) {
            $sql .= ' WHERE '. implode(' AND ', $where_sql_components);
        }
        $group = $criteria->getGroupBy();
        if ( $group ) {
            $sql .= ' GROUP BY '. self::getPrefixedColumn($prefix, $join_prefixes, $group['col']);
        }
        $order = $criteria->getOrder();
        if ( $order ) {
            $sql .= ' ORDER BY '. self::getPrefixedColumn($prefix, $join_prefixes, $order['col']) .' '. $order['sort'];
        }
        $page = $criteria->getPage();
        if ( $page ) {
            $sql .= ' LIMIT '. $page['limit'] .' OFFSET '. $page['offset'];
        }
        return array('sql' => $sql, 'values' => $where_values);
    }

    public static function insert($table, array $columns, $entity, array $exclude=array()) {
        $sql_and_values = self::generateInsertSqlAndValues($table, $columns, $entity->getValues(), $exclude);
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
        return array('sql' => $sql, 'values' => $values);
    }
}
QueryBuilder::init();
