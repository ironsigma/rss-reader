<?php
class Folder {
    public $id;
    public $name;
    public function __construct($name, $id=null) {
        $this->name = $name;
        $this->id = $id;
    }
}
class Feed {
    public $id;
    public $name;
    public $url;
    public $update_freq;
    public $folder_id;
    public function __construct($name, $url, $update_freq, $folder_id=null, $id=null) {
        $this->name = $name;
        $this->url = $url;
        $this->folder_id = $folder_id;
        $this->id = $id;
        $this->update_freq = $update_freq;
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
    // Static
    private static $instance;
    public function getInstance() {
        if ( self::$instance !== null ) {
            return self::$instance;
        }
        self::$instance = new DbService();
        if ( self::$instance->db === null ) {
            return null;
        }
        return self::$instance;
    }

    // Instance
    private $db;
    private function __construct() {
        try {
            $this->db = new SQLite3('reader.sqlite3', SQLITE3_OPEN_READWRITE);
        } catch ( Exception $ex ) {
            print("Can't open DB: ". $ex->getMessage() ."\n");
        }
    }

    // Create folder
    public function createFolder(Folder $folder) {
        $st = $this->db->prepare('INSERT INTO folder (name) VALUES (:name)');
        $st->bindValue(':name', $folder->name, SQLITE3_TEXT);
        $st->execute();
        $folder->id = $this->db->lastInsertRowID();
    }

    public function createFeed(Feed $feed) {
        $st = $this->db->prepare('INSERT INTO feed (name, url, update_freq, folder_id) VALUES (:name, :url, :update, :freq, :id)');
        $st->bindValue(':name', $feed->name, SQLITE3_TEXT);
        $st->bindValue(':url', $feed->url, SQLITE3_TEXT);
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
        $st = $this->db->prepare('INSERT INTO "update" (updated, count, new, feed_id) VALUES (:ts, :count, :new, :id)');
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
        $st = $this->db->prepare('SELECT id, name, url, update_freq, folder_id FROM feed');
        $results = $st->execute();
        $feeds = array();
        while ( $row = $results->fetchArray(SQLITE3_ASSOC) ) {
            $feeds[] = new Feed($row['name'], $row['url'], $row['update_freq'], $row['folder_id'], $row['id']);
        }
        return $feeds;
    }

    // Hashmap of updates by feed id
    public function findLatestUpdates() {
        $st = $this->db->prepare('SELECT u.id, u.updated, u.count, u.new, u.feed_id '.
           'FROM "update" u WHERE updated=(SELECT MAX(updated) FROM "update" WHERE feed_id=u.feed_id)');
        $results = $st->execute();
        $updates = array();
        while ( $row = $results->fetchArray(SQLITE3_ASSOC) ) {
            $updates[$row['feed_id']] = new Update($row['updated'], $row['count'], $row['new'], $row['feed_id'], $row['id']);
        }
        return $updates;
    }
}
