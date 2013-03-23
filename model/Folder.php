<?php
/**
 * Folder entry
 * @package com\izylab\reader
 */
class Folder extends Entity {
    protected static $meta_data;
}
Folder::init('folder', array(
    array('name'=>'id', 'type'=>Entity::TYPE_INT),
    array('name'=>'name', 'type'=>Entity::TYPE_STR),
    array('name'=>'newest_first', 'type'=>Entity::TYPE_BOOL),
));
