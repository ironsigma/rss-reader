<?php
/**
 * Post entry
 * @package com\izylab\reader
 */
class Post {
    public $id;
    public $title;
    public $ts;
    public $text;
    public $link;
    public $read;
    public $stared;
    public $guid;
    public $feed_id;
    public function __construct($title, $ts, $link, $guid, $text, $read, $stared, $feed_id=null, $id=null) {
        $this->id = $id;
        $this->title = $title;
        $this->ts = $ts;
        $this->text = $text;
        $this->link = $link;
        $this->feed_id = $feed_id;
        $this->guid = $guid;
        $this->read = $read;
        $this->stared = $stared;
    }
    public static function fromArray($data) {
        return new Post(
            $data['title'],
            $data['published'],
            $data['link'],
            $data['guid'],
            $data['text'],
            false, false,
            $data['feed_id']
        );
    }
}
