<?php
class DB {
    protected static $connection;

    public static function connection() {
        if ( !is_null(static::$connection) ) {
            return static::$connection;
        }

        $driver = Config::get('database.driver');
        switch ( $driver ) {
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

    public static function table($table) {
        return static::connection()->createQuery($table);
    }
}
