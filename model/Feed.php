<?php
class Feed {
    public $id;
    public $name;
    public $url;
    public $sort;
    public $update_freq;
    public $per_page;
    public $folder_id;

    // transient
    public $unread;

    public function __construct($name, $url, $sort, $update_freq, $per_page, $folder_id=null, $id=null) {
        $this->name = $name;
        $this->url = $url;
        $this->folder_id = $folder_id;
        $this->id = $id;
        $this->update_freq = $update_freq;
        $this->per_page = $per_page;
        $this->sort = $sort;
    }
}
