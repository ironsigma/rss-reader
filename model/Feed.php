<?php
/**
 * Feed entry
 * @package com\izylab\reader
 */
class Feed extends Entity {
    protected static $meta_data;
}
Feed::init('feed', array('id', 'name', 'url', 'newest_first', 'update_freq', 'per_page', 'folder_id'), array('folder', 'unread', 'folder_id'));
