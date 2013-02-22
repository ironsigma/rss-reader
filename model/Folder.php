<?php
/**
 * Folder entry
 * @package com\izylab\reader
 */
class Folder {
    public $id;
    public $name;
    public $sort;
    public function __construct($name, $sort, $id=null) {
        $this->name = $name;
        $this->id = $id;
        $this->sort = $sort;
    }
}
