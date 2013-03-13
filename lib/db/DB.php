<?php
class DB {
    protected static $connection;

    public static function connection() {
        if ( !is_null(static::$connection) ) {
            return static::$connection;
        }

        switch ( Config::get('database.driver') ) {
        case 'sqlite3':
            $pdo = new PDO('sqlite:'.Config::get('database.file'));
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $grammar = new SQLite3Grammar();
            break;

        default:
            throw new Exception('Unknown driver');
        }

        return new Connection($pdo, $grammar);
    }

    public static function from($table) {
        return static::connection()->createQuery($table);
    }
}
