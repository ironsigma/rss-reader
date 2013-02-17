<?php
class FeedDao {
    public static function findById($id) {
        $db = Database::getInstance();
        $sql = 'SELECT id, name, url, sort_dir, update_freq, folder_id FROM feed WHERE id=:id';
        $st = $db->prepare($sql);
        $st->bindValue(':id', $id, SQLITE3_INTEGER);
        $results = $st->execute();
        $row = $results->fetchArray(SQLITE3_ASSOC);
        return new Feed($row['name'], $row['url'], $row['sort_dir'], $row['update_freq'], $row['folder_id'], $row['id']);
    }

    public static function insert($feed){
        $db = Database::getInstance();
        $st = $db->prepare('INSERT INTO feed (name, url, sort_dir, update_freq, folder_id) '.
            'VALUES (:name, :url, :sort, :update, :freq, :id)');
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
        $feed->id = $db->lastInsertRowID();
    }

    public static function findAll() {
        $db = Database::getInstance();
        $st = $db->prepare('SELECT id, name, url, sort_dir, update_freq, folder_id FROM feed');
        $results = $st->execute();
        $feeds = array();
        while ( $row = $results->fetchArray(SQLITE3_ASSOC) ) {
            $feeds[] = new Feed($row['name'], $row['url'], $row['sort_dir'], $row['update_freq'], $row['folder_id'], $row['id']);
        }
        return $feeds;
    }

    public static function findAllWithUnreadCount() {
        $db = Database::getInstance();
        $sql = 'SELECT f.id, f.name, f.url, f.sort_dir, f.update_freq, f.folder_id, COUNT(*) AS unread '
            .'FROM FEED f LEFT JOIN post p ON p.feed_id = f.id GROUP BY f.id';
        $st = $db->prepare($sql);
        $results = $st->execute();
        $feeds = array();
        while ( $row = $results->fetchArray(SQLITE3_ASSOC) ) {
            $feed = new Feed($row['name'], $row['url'], $row['sort_dir'],
                $row['update_freq'], $row['folder_id'], $row['id']);
            $feed->unread = $row['unread'];
            $feeds[] = $feed;
        }
        return $feeds;
    }
}
