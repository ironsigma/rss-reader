<?php
class DB {
    protected static $connection;

    public static function connection($driver='sqlite3') {
        if ( !is_null(static::$connection) ) {
            return static::$connection;
        }

        switch ( $driver ) {
        case 'sqlite3':
            $pdo = new PDO('sqlite:/var/www/reader/db/reader.sqlite3');
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            break;

        default:
            throw new Exception('Unknown driver');
        }

        return new Connection($pdo);
    }

    public static function from($table) {
        return static::connection()->createQuery($table);
    }
}
