<?php
/**
 * Feed DAO
 * @package com\izylab\reader
 */
class FeedDao {
    public static function findById($id) {
        return DB::table(Feed::getTable())
            ->equal('id', $id, Entity::TYPE_INT)
            ->first('Feed');
    }

    public static function findAll() {
        return DB::table(Feed::getTable())
            ->fetch('Feed');
    }

    public static function findAllWithUnreadCount() {
        return DB::table(Feed::getTable())
            ->count('*', 'unread')
            ->leftJoin('post', 'feed_id', 'id')
            ->leftJoin('folder', 'id', 'folder_id')
            ->false('post.read')
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
