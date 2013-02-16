<?php
class Feed {
    public $id;
    public $name;
    public $url;
    public $sort;
    public $update_freq;
    public $folder_id;

    // transient
    public $unread;

    public function __construct($name, $url, $sort, $update_freq, $folder_id=null, $id=null) {
        $this->name = $name;
        $this->url = $url;
        $this->folder_id = $folder_id;
        $this->id = $id;
        $this->update_freq = $update_freq;
        $this->sort = $sort;
    }
}
