<?php
/**
 * Criteria
 * @package com\izylab\reader
 */
class Criteria {
    private $operations = array();
    private $joins = array();
    private $counts = array();
    private $page = null;
    private $order = null;
    private $group = null;

    public function getOperations() {
        return $this->operations;
    }

    public function getPage() {
        return $this->page;
    }

    public function getOrder() {
        return $this->order;
    }

    public function getGroupBy() {
        return $this->group;
    }

    public function getJoins() {
        return $this->joins;
    }
    public function getCounts() {
        return $this->counts;
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
    public function in($column, array $array, $type=SQLITE3_INTEGER) {
        $this->operation('in', $column, $array, $type);
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
    public function orderBy($column, $sort='ASC') {
        $this->order = array('col' => $column, 'sort' => $sort);
    }
    public function leftJoin($table, $table_col, $this_col) {
        $this->joins[] = array(
            'type' => 'LEFT',
            'table' => $table,
            'table_col' => $table_col,
            'this_col' => $this_col
        );
    }
    public function crossJoin($table, $table_col, $this_col) {
        $this->joins[] = array(
            'type' => 'CROSS',
            'table' => $table,
            'table_col' => $table_col,
            'this_col' => $this_col
        );
    }
    public function join($table, $table_col, $this_col) {
        $this->joins[] = array(
            'type' => 'INNER',
            'table' => $table,
            'table_col' => $table_col,
            'this_col' => $this_col
        );
    }
    public function count($item, $name) {
        $this->counts[] = array(
            'item' => $item,
            'name' => $name,
        );
    }
    public function groupBy($column) {
        $this->group = array('col' => $column);
    }
    public function page($limit, $offset) {
        $this->page = array('limit' => $limit, 'offset' => $offset);
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