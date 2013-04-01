<?php
/**
 * State Log entry
 * @package com\izylab\reader
 */
class State extends Entity {
    protected static $meta_data;
}
State::init('post_state',
array(
    array('name'=>'id',      'type'=>Entity::TYPE_INT),
    array('name'=>'read',    'type'=>Entity::TYPE_BOOL),
    array('name'=>'stared',  'type'=>Entity::TYPE_BOOL),
    array('name'=>'post_id', 'type'=>Entity::TYPE_INT),
    array('name'=>'user_id', 'type'=>Entity::TYPE_INT),
));
