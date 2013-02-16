<?php
class PostDao {
    public static function insert(Post $post) {
        $db = Database::getInstance();
        $st = $db->prepare('INSERT INTO post (title, ts, text, link, read, stared, guid, feed_id) '.
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
        $post->id = $db->lastInsertRowID();
    }
    public static function postExists($post) {
        $db = Database::getInstance();
        $st = $db->prepare('SELECT count(guid) FROM post WHERE guid=:guid');
        $st->bindValue(':guid', $post->guid, SQLITE3_TEXT);
        $results = $st->execute();
        $row = $results->fetchArray(SQLITE3_NUM);
        return $row[0] !== 0;
    }
    public static function deleteReadPostBefore($date) {
        $db = Database::getInstance();
        $st = $db->prepare('DELETE FROM post WHERE ts <= :ts AND read = :read AND stared = :stared');
        $st->bindValue(':ts', $date, SQLITE3_INTEGER);
        $st->bindValue(':read', true, SQLITE3_INTEGER);
        $st->bindValue(':stared', false, SQLITE3_INTEGER);
        $st->execute();
    }
}
