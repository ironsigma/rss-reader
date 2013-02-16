<?php
class Update {
    public $id;
    public $updated;
    public $count;
    public $new;
    public $feed_id;
    public function __construct($updated, $count, $new, $feed_id, $id=null) {
        $this->id = $id;
        $this->updated = $updated;
        $this->count = $count;
        $this->new = $new;
        $this->feed_id = $feed_id;
    }
}
