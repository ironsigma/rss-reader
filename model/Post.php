<?php
/**
 * Post entry
 * @package com\hawkprime\reader
 */
class Post extends Entity {
    protected static $meta_data;
}
Post::init('post',
array(
    array('name'=>'id', 'type'=>Entity::TYPE_INT),
    array('name'=>'title', 'type'=>Entity::TYPE_STR),
    array('name'=>'published', 'type'=>Entity::TYPE_DATETIME),
    array('name'=>'text', 'type'=>Entity::TYPE_STR),
    array('name'=>'link', 'type'=>Entity::TYPE_STR),
    array('name'=>'read', 'type'=>Entity::TYPE_BOOL),
    array('name'=>'stared', 'type'=>Entity::TYPE_BOOL),
    array('name'=>'guid', 'type'=>Entity::TYPE_STR),
    array('name'=>'feed_id', 'type'=>Entity::TYPE_INT),
), array('feed'));
