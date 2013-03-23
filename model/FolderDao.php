<?php
/**
 * Folder DAO
 * @package com\izylab\reader
 */
class FolderDao {
    private static $log;

    public static function init() {
        self::$log = LogFacility::getLogger('FolderDao.class');
    }

    public static function findById($id) {
        $criteria = new Criteria();
        $criteria->equal('id', $id, SQLITE3_INTEGER);
        $st = QueryBuilder::select(Folder::getTable(), Folder::getColumnNames(), $criteria);
        $results = $st->execute();
        $row = $results->fetchArray(SQLITE3_ASSOC);
        return new Folder($row);
    }

}
FolderDao::init();
