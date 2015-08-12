<?php
/**
 * Feed entry
 * @package com\hawkprime\reader
 */
class Feed extends Entity {
    protected static $meta_data;
}
Feed::init('feed', array(
    array('name'=>'id', 'type'=>Entity::TYPE_INT),
    array('name'=>'name', 'type'=>Entity::TYPE_STR),
    array('name'=>'url', 'type'=>Entity::TYPE_STR),
    array('name'=>'active', 'type'=>Entity::TYPE_BOOL),
    array('name'=>'newest_first', 'type'=>Entity::TYPE_BOOL),
    array('name'=>'update_freq', 'type'=>Entity::TYPE_INT),
    array('name'=>'per_page', 'type'=>Entity::TYPE_INT),
    array('name'=>'folder_id', 'type'=>Entity::TYPE_INT),
    array('name'=>'user_id', 'type'=>Entity::TYPE_INT),
), array('folder', 'unread', 'folder_id'));
