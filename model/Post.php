<?php
/**
 * Post entry
 * @package com\izylab\reader
 */
class Post extends Entity {
    protected static $meta_data;
}
Post::init('post', array('id', 'title', 'ts', 'text', 'link', 'read', 'stared', 'guid', 'feed_id'));
