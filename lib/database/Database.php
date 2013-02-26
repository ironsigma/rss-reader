<?php
/**
 * Database
 * @package com\izylab\reader
 */
class Database {
    private static $log;
    private static $database;
    private static $dbName;

    public static function init() {
        self::$log = LogFacility::getLogger('Database.class');
        self::$dbName = 'reader.sqlite3';
    }

    public static function getInstance() {
        if ( self::$database !== null ) {
            self::$log->trace('Returning existing service instance');
            return self::$database;
        }
        try {
            self::$log->debug('Creating new service instance');
            self::$database = new SQLite3(self::$dbName, SQLITE3_OPEN_READWRITE);
            if ( self::$database === null ) {
                throw new Exception('Error creating DB connection');
            }
            self::$log->trace('Database instance created');
        } catch ( Exception $ex ) {
            self::$log->error("Can't open DB: ". $ex->getMessage() ."\n");
        }
        return self::$database;
    }

    public static function setDatabase($dbName) {
        self::$dbName = $dbName;
    }

    public static function lastInsertRowID() {
        return self::getInstance()->lastInsertRowID();
    }

}
Database::init();
