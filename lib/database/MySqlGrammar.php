<?php
class MySqlGrammar {
    protected $prefix_index;
    protected $prefix_list;

    public function bind($bind) {
        $value = $bind['val'];
        switch ( $bind['type'] ) {
        case Entity::TYPE_NULL:
            $type = PDO::PARAM_NULL;
            break;

        case Entity::TYPE_STR:
            $type = PDO::PARAM_STR;
            break;

        case Entity::TYPE_BOOL:
            $type = PDO::PARAM_INT;
            $value = $value ? 1 : 0;
            break;

        case Entity::TYPE_INT:
            $type = PDO::PARAM_INT;
            break;

        case Entity::TYPE_REAL:
            $type = PDO::PARAM_STR;
            $value = strval($value);
            break;

        case Entity::TYPE_DATE:
            $type = PDO::PARAM_STR;
            $value = date('Y-m-d', $value);
            break;

        case Entity::TYPE_TIME:
            $type = PDO::PARAM_STR;
            $value = date('H:i:s', $value);
            break;

        case Entity::TYPE_DATETIME:
            $type = PDO::PARAM_STR;
            $value = date('Y-m-d H:i:s', $value);
            break;

        case Entity::TYPE_BLOB:
            $type = PDO::PARAM_LOB;
            break;

        default:
            throw new Exception('Invalid insert type');
        }
        return array('val' => $value, 'type' => $type);
    }

    public function generateInsertSql($query, $entity, $columns) {
        $sql = $this->generateEntityInsert($query, $entity, $columns);
        return $sql;
    }

    public function generateUpdateSql($query, $entity, $columns) {
        $this->prefix_list = array();
        $this->prefix_index = 0;
        $this->generateTablePrefixes($query);

        $sql = 'UPDATE '. $query->getTable()
            ." {$this->prefix_list['_FROM_']}";

        $sql .= $this->generateJoins($query);
        $sql .= $this->generateEntityUpdate($query, $entity, $columns);
        $sql .= $this->generateWhere($query->getConditions());
        return $sql;
    }

    public function generateSelectSql($query) {
        $this->prefix_list = array();
        $this->prefix_index = 0;
        $this->generateTablePrefixes($query);

        $sql = $this->generateSelect($query);
        $sql .= $this->generateFrom($query);
        $sql .= $this->generateJoins($query);
        $sql .= $this->generateWhere($query->getConditions());
        $sql .= $this->generateGroup($query->getGroupBy());
        $sql .= $this->generateOrder($query->getOrderBy());
        $sql .= $this->generatePage($query->getPage());
        return $sql;
    }

    protected function generateGroup($group) {
        if ( $group ) {
            return ' GROUP BY '. $this->prefixColumn($group['col']);
        }
        return '';
    }

    protected function generateOrder($order) {
        if ( $order ) {
            return ' ORDER BY '. $this->prefixColumn($order['col']) .' '. $order['sort'];
        }
        return '';
    }

    protected function generatePage($page) {
        if ( $page ) {
            return ' LIMIT '. $page['limit'] .' OFFSET '. $page['offset'];
        }
        return '';
    }

    protected function generateTablePrefixes($query) {
        $this->prefix_list['_FROM_'] = $this->generatePrefix($query->getTable());
        foreach ( $query->getJoins() as $join ) {
            $this->prefix_list[$join['table']] = $this->generatePrefix($join['table']);
        }
    }

    protected function generateSelect($query) {
        $columns = array_map(array(&$this, 'prefixColumn'), $query->getColumns());
        foreach ( $query->getCounts() as $count ) {
            if ( $count['item'] != '*' ) {
                $count['item'] = $this->prefixColumn($count['item']);
            }
            $columns[] = "COUNT({$count['item']}) AS `{$count['name']}`";
        }
        if ( count($columns) == 0 ) {
            $columns[] = $this->prefixColumn('*');
        }
        return 'SELECT '. join(', ', $columns);
    }

    protected function generateEntityInsert($query, $entity, $columns=null) {
        $cols = array();
        $values = array();
        $columns = $columns ? $columns : $entity->getColumnNames();
        foreach ( $columns as $col ) {
            if ( $col === 'id' ) {
                continue;
            }
            $cols[] = "`$col`";
            $values[] = '?';
        }
        return 'INSERT INTO '. $query->getTable()
            .' ('. join(',', $cols) .') VALUES ('
            . join(',', $values) .')';
    }

    protected function generateEntityUpdate($query, $entity, $columns=null) {
        $cols = array();
        $columns = $columns ? $columns : $entity->getColumnNames();
        foreach ( $columns as $col ) {
            $cols[] = "`$col`=?";
        }
        return ' SET '. join(',', $cols);
    }

    protected function generateFrom($query) {
        return ' FROM '. $query->getTable()
            ." {$this->prefix_list['_FROM_']}";
    }

    protected function generateJoins($query) {
        $sql = '';
        foreach ( $query->getJoins() as $join ) {
            $prefix = $this->prefix_list[$join['table']];
            $this_col = $this->prefixColumn($join['this_col']);
            $sql .= " {$join['type']} JOIN {$join['table']} $prefix ON "
                ."$prefix.`{$join['table_col']}`=$this_col";
        }
        return $sql;
    }

    protected function generateWhere($conditions) {
        if ( count($conditions) == 0 ) {
            return '';
        }
        $where_sql_components = array();
        foreach ( $conditions as $op ) {

            // prefix column names
            $prefix_col = $this->prefixColumn($op['col']);

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
                    $in_labels[] = '?';
                }
                $where_sql_components[] = "$prefix_col IN(". implode(',', $in_labels) .")";
                break;

            // Boolean true false
            case 'true':
            case 'false':
                $where_sql_components[] = "$prefix_col=?";
                break;

            // Compare
            case '=':
            case '!=':
            case '>':
            case '<':
            case '<=':
            case '>=':
                $where_sql_components[] = "$prefix_col{$op['op']}?";
                break;

            // Error
            default:
                throw new Exception('Invalid SQL operation');
            }
        }
        return ' WHERE '. implode(' AND ', $where_sql_components);
    }

    protected function prefixColumn($column) {
        $alias = '';
        $prefix = '';
        if ( is_array($column) ) {
            $alias = " AS `{$column[1]}`";
            $column = $column[0];
        }
        if ( strpos($column, '.') !== false ) {
            list($table, $column) = explode('.', $column);
            if ( isset($this->prefix_list[$table]) ) {
                $prefix = $this->prefix_list[$table].'.';
            }
        } else {
            if ( isset($this->prefix_list['_FROM_']) ) {
                $prefix = $this->prefix_list['_FROM_'].'.';
            }
        }
        if ( $column != '*' ) {
            $column = "`$column`";
        }
        return "$prefix$column$alias";
    }

    protected function generatePrefix($name) {
        $this->prefix_index++;
        return "_{$name[0]}{$this->prefix_index}";
    }

}
