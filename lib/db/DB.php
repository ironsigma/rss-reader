<?php
class DB {
    protected static $connection;

    public static function connection() {
        if ( !is_null(static::$connection) ) {
            return static::$connection;
        }

        $driver = Config::get('database.driver2');
        switch ( $driver ) {
        case 'sqlite3':
            $pdo = new PDO('sqlite:'.Config::get('database.sqlite3.file'));
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $grammar = new SQLite3Grammar();
            break;

        case 'mysql':
            $pdo = new PDO('mysql:host='. Config::get('database.mysql.host')
                .';port='. Config::get('database.mysql.port')
                .';dbname='. Config::get('database.mysql.database'),
                    Config::get('database.mysql.username'),
                    Config::get('database.mysql.password'));

            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $grammar = new MySqlGrammar();
            break;

        default:
            throw new Exception("Unknown driver \"$driver\"");
        }

        return new Connection($pdo, $grammar);
    }

    public static function from($table) {
        return static::connection()->createQuery($table);
    }
}
