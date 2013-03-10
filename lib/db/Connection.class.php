<?php
class Connection {
    protected $config = array();
    protected $grammar;

    public function __construct() {
        $this->config['driver'] = 'sqlite3';
    }

    public function createQuery($table) {
        return new Query($this, $this->grammar(), $table);
    }

    public function execute($sql, $bindings=array()) {
        echo "db->execute($sql)";
    }

    public function driver() {
        return $this->config['driver'];
    }

    protected function grammar() {
        if ( ! is_null($this->grammar) ) {
            return $this->grammar;
        }

        switch ( $this->driver() ) {
        case 'sqlite3':
            return new SQLite3Grammar($this);

        default:
            return null;
        }
    }
}
