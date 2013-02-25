<?php
/**
 * Folder entry
 * @package com\izylab\reader
 */
class Folder extends Entity {
    protected static $meta_data;
}
Folder::init('folder', array('id', 'name', 'sort'));
