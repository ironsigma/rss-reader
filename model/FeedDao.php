<?php
/**
 * Feed DAO
 * @package com\izylab\reader
 */
class FeedDao {
    private static $log;

    public static function init() {
        self::$log = LogFacility::getLogger('FeedDao.class');
    }

    public static function findById($id) {
        $criteria = new Criteria();
        $criteria->equal('id', $id, SQLITE3_INTEGER);
        $st = QueryBuilder::select(Feed::getTable(), Feed::getColumns(), $criteria);
        $results = $st->execute();
        $row = $results->fetchArray(SQLITE3_ASSOC);
        return new Feed($row);
    }

    public static function insert($feed){
        $st = QueryBuilder::insert(Feed::getTable(), Feed::getColumns(), $feed, array('id'));
        $st->execute();
        $feed->id = Database::lastInsertRowID();
        return $feed;
    }

    public static function findAll() {
        $st = QueryBuilder::select(Feed::getTable(), Feed::getColumns(), new Criteria());
        $results = $st->execute();
        $feeds = array();
        while ( $row = $results->fetchArray(SQLITE3_ASSOC) ) {
            $feeds[] = new Feed($row);
        }
        return $feeds;
    }

    public static function findAllWithUnreadCount() {
        $criteria = new Criteria();
        $criteria->count('*', 'unread');
        $criteria->leftJoin('post', 'feed_id', 'id');
        $criteria->leftJoin('folder', 'id', 'folder_id');
        $criteria->false('post.read');
        $criteria->groupBy('id');
        $criteria->orderBy('folder.name');

        $st = QueryBuilder::select(Feed::getTable(), array_merge(
                array(array('folder.name', 'folder'), array('folder.id', 'folder_id')),
                Feed::getColumns()
            ), $criteria);

        $results = $st->execute();
        $feeds = array();
        while ( $row = $results->fetchArray(SQLITE3_ASSOC) ) {
            $feeds[] = new Feed($row);
        }
        return $feeds;
    }
}
FeedDao::init();
