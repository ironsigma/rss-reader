<?php
/**
 * Feed DAO
 * @package com\hawkprime\reader
 */
class FeedDao {
    public static function insert(Feed $feed) {
        $feed->id = Database::table(Feed::getTable())->insert($feed);
        return $feed;
    }
    public static function update($feed, $columns=null) {
        Database::table(Feed::getTable())
            ->equal('id', $feed->id, Entity::TYPE_INT)
            ->update($feed, $columns);
    }
    public static function delete($feed) {
        $sql = 'DELETE FROM feed WHERE id='. $feed->id;
        Database::connection()->execute($sql);
    }
    public static function findById($id) {
        return Database::table(Feed::getTable())
            ->equal('id', $id, Entity::TYPE_INT)
            ->first('Feed');
    }

    public static function findAll() {
        return Database::table(Feed::getTable())
            ->orderBy('name')
            ->fetch('Feed');
    }

    public static function findAllWithUnreadCount() {
        return Database::table(Feed::getTable())
            ->count('*', 'unread')
            ->leftJoin('post', 'feed_id', 'id')
            ->leftJoin('folder', 'id', 'folder_id')
            ->false('post.read')
            ->true('active')
            ->groupBy('id')
            ->orderBy('folder.name')
            ->select(array_merge(
                array(array('folder.name', 'folder'),
                array('folder.id', 'folder_id')),
                Feed::getColumnNames()
            ))
            ->fetch('Feed');
    }
}
