<?php
include_once 'logger.php';

class Folder {
    public $id;
    public $name;
    public $sort;
    public function __construct($name, $sort, $id=null) {
        $this->name = $name;
        $this->id = $id;
        $this->sort = $sort;
    }
}
class Feed {
    public $id;
    public $name;
    public $url;
    public $sort;
    public $update_freq;
    public $folder_id;
    public function __construct($name, $url, $sort, $update_freq, $folder_id=null, $id=null) {
        $this->name = $name;
        $this->url = $url;
        $this->folder_id = $folder_id;
        $this->id = $id;
        $this->update_freq = $update_freq;
        $this->sort = $sort;
    }
}
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
class DbService {
    private static $log;
    private static $instance;
    private static $database;

    public static function init() {
        self::$log = LogFacility::getLogger('DbService.class');
        self::$database = 'reader.sqlite3';
    }

    public static function getInstance() {
        if ( self::$instance !== null ) {
            self::$log->debug('Returning existing service instance');
            return self::$instance;
        }
        self::$log->debug('Creating new service instance');
        self::$instance = new DbService();
        if ( self::$instance->db === null ) {
            self::$log->debug('Error creating DB connection');
            return null;
        }
        self::$log->debug('Service instance created');
        return self::$instance;
    }

    public static function setDatabase($database) {
        self::$database = $database;
    }

    // Instance
    private $db;
    private function __construct() {
        try {
            $this->db = new SQLite3(self::$database, SQLITE3_OPEN_READWRITE);
        } catch ( Exception $ex ) {
            self::$log->error("Can't open DB: ". $ex->getMessage() ."\n");
        }
    }

    // Create folder
    public function createFolder(Folder $folder) {
        $st = $this->db->prepare('INSERT INTO folder (name, sort_dir) VALUES (:name, :sort)');
        $st->bindValue(':name', $folder->name, SQLITE3_TEXT);
        $st->bindValue(':sort', $folder->sort, SQLITE3_TEXT);
        $st->execute();
        $folder->id = $this->db->lastInsertRowID();
    }

    public function createFeed(Feed $feed) {
        $st = $this->db->prepare('INSERT INTO feed (name, url, sort_dir, update_freq, folder_id) VALUES (:name, :url, :sort, :update, :freq, :id)');
        $st->bindValue(':name', $feed->name, SQLITE3_TEXT);
        $st->bindValue(':url', $feed->url, SQLITE3_TEXT);
        $st->bindValue(':sort', $feed->sort, SQLITE3_TEXT);
        $st->bindValue(':freq', $feed->update_freq, SQLITE3_INTEGER);

        if ( $feed->folder_id === null ) {
            $st->bindValue(':id', null, SQLITE3_NULL);
        } else {
            $st->bindValue(':id', $feed->folder_id, SQLITE3_INTEGER);
        }

        $st->execute();
        $feed->id = $this->db->lastInsertRowID();
    }

    public function createUpdate(Update $update) {
        $st = $this->db->prepare('INSERT INTO update_log (ts, count, new, feed_id) VALUES (:ts, :count, :new, :id)');
        $st->bindValue(':ts', $update->updated, SQLITE3_INTEGER);
        $st->bindValue(':count', $update->count, SQLITE3_INTEGER);
        $st->bindValue(':new', $update->new, SQLITE3_INTEGER);
        $st->bindValue(':id', $update->feed_id, SQLITE3_INTEGER);
        $st->execute();
        $update->id = $this->db->lastInsertRowID();
    }

    public function createPost(Post $post) {
        $st = $this->db->prepare('INSERT INTO post (title, ts, text, link, read, stared, guid, feed_id) '.
            'VALUES (:title, :ts, :text, :link, :read, :stared, :guid, :id)');

        $st->bindValue(':title', $post->title, SQLITE3_TEXT);
        $st->bindValue(':ts', $post->ts, SQLITE3_INTEGER);
        $st->bindValue(':text', $post->text, SQLITE3_TEXT);
        $st->bindValue(':link', $post->link, SQLITE3_TEXT);
        $st->bindValue(':read', $post->read, SQLITE3_INTEGER);
        $st->bindValue(':stared', $post->stared, SQLITE3_INTEGER);
        $st->bindValue(':guid', $post->guid, SQLITE3_TEXT);

        if ( $post->feed_id === null ) {
            $st->bindValue(':id', null, SQLITE3_NULL);
        } else {
            $st->bindValue(':id', $post->feed_id, SQLITE3_INTEGER);
        }

        $st->execute();
        $post->id = $this->db->lastInsertRowID();
    }

    public function postExists(Post $post) {
        $st = $this->db->prepare('SELECT count(guid) FROM post WHERE guid=:guid');
        $st->bindValue(':guid', $post->guid, SQLITE3_TEXT);
        $results = $st->execute();
        $row = $results->fetchArray(SQLITE3_NUM);
        return $row[0] !== 0;
    }

    public function findFeeds() {
        $st = $this->db->prepare('SELECT id, name, url, sort_dir, update_freq, folder_id FROM feed');
        $results = $st->execute();
        $feeds = array();
        while ( $row = $results->fetchArray(SQLITE3_ASSOC) ) {
            $feeds[] = new Feed($row['name'], $row['url'], $row['sort_dir'], $row['update_freq'], $row['folder_id'], $row['id']);
        }
        return $feeds;
    }

    public function findLatestUpdates() {
        $st = $this->db->prepare('SELECT u.id, u.ts, u.count, u.new, u.feed_id '.
           'FROM update_log u WHERE ts=(SELECT MAX(ts) FROM update_log WHERE feed_id=u.feed_id)');
        $results = $st->execute();
        $updates = array();
        while ( $row = $results->fetchArray(SQLITE3_ASSOC) ) {
            $updates[$row['feed_id']] = new Update($row['ts'], $row['count'], $row['new'], $row['feed_id'], $row['id']);
        }
        return $updates;
    }

    public function findPosts() {
        $st = $this->db->prepare('SELECT id, title, ts, text, link, read, stared, guid, feed_id '.
            'FROM post ORDER by ts DESC');
        $results = $st->execute();
        $posts = array();
        while ( $row = $results->fetchArray(SQLITE3_ASSOC) ) {
            $posts[] = new Post($row['title'], $row['ts'], $row['link'], $row['guid'], $row['text'], $row['feed_id'], $row['id']);
        }
        return $posts;
    }

    public function deleteReadPostBefore($date) {
        $st = $this->db->prepare('DELETE FROM post WHERE ts <= :ts AND read = :read AND stared = :stared');
        $st->bindValue(':ts', $date, SQLITE3_INTEGER);
        $st->bindValue(':read', true, SQLITE3_INTEGER);
        $st->bindValue(':stared', false, SQLITE3_INTEGER);
        $st->execute();
    }
}
DbService::init();
