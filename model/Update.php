<?php
/**
 * Update Log entry
 * @package com\izylab\reader
 */
class Update extends Entity {
    protected static $meta_data;
}
Update::init('update_log', array('id', 'updated', 'total_count', 'new_count', 'feed_id'));
