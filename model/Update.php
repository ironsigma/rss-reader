<?php
/**
 * Update Log entry
 * @package com\hawkprime\reader
 */
class Update extends Entity {
    protected static $meta_data;
}
Update::init('update_log',
array(
    array('name'=>'id', 'type'=>Entity::TYPE_INT),
    array('name'=>'updated', 'type'=>Entity::TYPE_DATETIME),
    array('name'=>'total_count', 'type'=>Entity::TYPE_INT),
    array('name'=>'new_count', 'type'=>Entity::TYPE_INT),
    array('name'=>'feed_id', 'type'=>Entity::TYPE_INT),
));
