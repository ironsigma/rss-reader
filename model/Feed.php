<?php
/**
 * Feed entry
 * @package com\izylab\reader
 */
class Feed extends Entity {
    public function __construct($values=null) {
        $this->addProperty('unread');
        parent::__construct($values);
    }
}
Feed::init('feed', array('id', 'name', 'url', 'sort_dir', 'update_freq', 'per_page', 'folder_id'));
