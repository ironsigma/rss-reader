<?php
/**
 * Post entry
 * @package com\izylab\reader
 */
class Post extends Entity {
    protected static $meta_data;
}
Post::init('post',
array(
    'id',
    'title',
    'published',
    'text',
    'link',
    'read',
    'stared',
    'guid',
    'feed_id'
), array(
    'feed'
));
