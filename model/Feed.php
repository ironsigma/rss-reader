<?php
/**
 * Feed entry
 * @package com\izylab\reader
 */
class Feed extends Entity {
    protected static $meta_data;
}
Feed::init('feed', array('id', 'name', 'url', 'sort_dir', 'update_freq', 'per_page', 'folder_id'), array('unread'));
