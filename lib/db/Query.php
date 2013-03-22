<?php
class Query {
    protected $connection;
    protected $conditions;
    protected $bindings;
    protected $grammar;
    protected $table;
    protected $columns;
    protected $joins;
    protected $order;
    protected $group;
    protected $page;
    protected $counts;

    public function __construct($connection, $grammar, $table) {
        $this->connection = $connection;
        $this->grammar = $grammar;
        $this->table = $table;
        $this->columns = array();
        $this->joins = array();
        $this->conditions = array();
        $this->counts = array();
        $this->bindings = array();
    }

    // Select
    public function fetch($class=null) {
        $sql = $this->grammar->generateSelectSql($this);
        return $this->connection->fetch(
            $class===null?PDO::FETCH_BOTH:PDO::FETCH_CLASS,
            $sql, $this->bindings, $class);
    }

    // Insert
    public function insert($entity) {
        $this->bindings = array();
        $sql = $this->grammar->generateInsertSql($this, $entity);
        $values = $entity->getValues();
        foreach ( $entity->getColumns() as $col ) {
            if ( $col === 'id' ) {
                continue;
            }
            $value = $values[$col];
            if ( is_null($value) ) {
                $type = PDO::PARAM_NULL;
            } elseif ( is_string($value) ) {
                $type = PDO::PARAM_STR;
            } elseif ( is_bool($value) ) {
                $type = PDO::PARAM_INT;
                $value = $value ? 1 : 0;
            } elseif ( is_int($value) || is_long($value) ) {
                $type = PDO::PARAM_INT;
            } elseif ( is_float($value) || is_double($value) ) {
                $type = PDO::PARAM_STR;
                $value = strval($value);
            } else {
                throw new Exception('Invalid insert type');
            }
            $this->bindings[] = array('val'=>$value, 'type'=>$type);
        }
        return $this->connection->insert($sql, $this->bindings);
    }

    // Columns
    public function select(array $columns) {
        $this->columns = $columns;
        return $this;
    }

    // Joins
    public function leftJoin($table, $table_col, $this_col) {
        $type = 'LEFT';
        $this->joins[] = compact('type', 'table', 'this_col', 'table_col');
        return $this;
    }
    public function crossJoin($table, $table_col, $this_col) {
        $type = 'CROSS';
        $this->joins[] = compact('type', 'table', 'this_col', 'table_col');
        return $this;
    }
    public function join($table, $table_col, $this_col) {
        $type = 'INNER';
        $this->joins[] = compact('type', 'table', 'this_col', 'table_col');
        return $this;
    }

    // Where
    public function equal($col, $val, $type) {
        $op = '=';
        $this->conditions[] = compact('op', 'col', 'val', 'type');
        $this->bindings[] = compact('val', 'type');
        return $this;
    }
    public function notEqual($col, $val, $type) {
        $op = '!=';
        $this->conditions[] = compact('op', 'col', 'val', 'type');
        $this->bindings[] = compact('val', 'type');
        return $this;
    }
    public function greaterThan($col, $val, $type=PDO::PARAM_INT) {
        $op = '>';
        $this->conditions[] = compact('op', 'col', 'val', 'type');
        $this->bindings[] = compact('val', 'type');
        return $this;
    }
    public function lessThan($col, $val, $type=PDO::PARAM_INT) {
        $op = '<';
        $this->conditions[] = compact('op', 'col', 'val', 'type');
        $this->bindings[] = compact('val', 'type');
        return $this;
    }
    public function greaterThanEqual($col, $val, $type=PDO::PARAM_INT) {
        $op = '>=';
        $this->conditions[] = compact('op', 'col', 'val', 'type');
        $this->bindings[] = compact('val', 'type');
        return $this;
    }
    public function lessThanEqual($col, $val, $type=PDO::PARAM_INT) {
        $op = '<=';
        $this->conditions[] = compact('op', 'col', 'val', 'type');
        $this->bindings[] = compact('val', 'type');
        return $this;
    }
    public function in($col, array $val, $type=PDO::PARAM_INT) {
        $op = 'in';
        $this->conditions[] = compact('op', 'col', 'val', 'type');
        foreach ( $val as $v ) {
            $this->bindings[] = array('val' => $v, 'type' => $type);
        }
        return $this;
    }
    public function isNull($col) {
        $op = 'null';
        $this->conditions[] = compact('op', 'col');
        return $this;
    }
    public function isNotNull($col) {
        $op = 'notnull';
        $this->conditions[] = compact('op', 'col');
        return $this;
    }
    public function true($col) {
        $op = 'true';
        $val = true;
        $type = PDO::PARAM_BOOL;
        $this->conditions[] = compact('op', 'col', 'val', 'type');
        $this->bindings[] = compact('val', 'type');
        return $this;
    }
    public function false($col) {
        $op = 'false';
        $val = false;
        $type = PDO::PARAM_BOOL;
        $this->conditions[] = compact('op', 'col', 'val', 'type');
        $this->bindings[] = compact('val', 'type');
        return $this;
    }

    // Order, Count, Group, Page
    public function orderBy($col, $sort='ASC') {
        $this->order = compact('col', 'sort');
        return $this;
    }
    public function count($item, $name) {
        $this->counts[] = compact('item', 'name');
        return $this;
    }
    public function groupBy($col) {
        $this->group = compact('col');
        return $this;
    }
    public function page($limit, $offset) {
        $this->page = compact('limit', 'offset');
        return $this;
    }

    // For Grammar
    public function getTable() {
        return $this->table;
    }
    public function getColumns() {
        return $this->columns;
    }

    public function getJoins() {
        return $this->joins;
    }
    public function getConditions() {
        return $this->conditions;
    }
    public function getOrderBy() {
        return $this->order;
    }
    public function getCounts() {
        return $this->counts;
    }
    public function getGroupBy() {
        return $this->group;
    }
    public function getPage() {
        return $this->page;
    }

    // For Unit Testing
    public function sql(array $args) {
        switch ( $args['type'] ) {
        case 'select':
            return $this->grammar->generateSelectSql($this);
            break;
        case 'insert':
            return $this->grammar->generateInsertSql($this, $args['entity']);
            break;
        }
    }
    public function getBindings() {
        return $this->bindings;
    }

}
