<?php
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
    public function __construct($title, $ts, $link, $guid, $text, $feed_id, $id=null) {
        $this->id = $id;
        $this->title = $title;
        $this->ts = $ts;
        $this->text = $text;
        $this->link = $link;
        $this->feed_id = $feed_id;
        $this->guid = $guid;
        $this->read = false;
        $this->stared = false;
    }
}
