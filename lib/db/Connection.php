<?php
class Connection {
    protected $grammar;
    protected $pdo;
    protected $log;

    public function __construct($pdo, $grammar) {
        $this->pdo = $pdo;
        $this->grammar = $grammar;
        $this->log = LogFacility::getLogger('Connection.class');
    }

    public function createQuery($table) {
        return new Query($this, $this->grammar, $table);
    }

    public function execute($sql, array $bindings=null) {
        $statement = $this->statement($sql, $bindings);
        $result = $statement->execute();
        return array($statement, $result);
    }
    protected function statement($sql, array $bindings=null) {
        $this->log->info($sql);
        $statement = $this->pdo->prepare($sql);
        if ( $bindings ) {
            for ( $i = 0; $i < count($bindings); $i ++ ) {
                $this->log->info( 'Param '. ($i+1) .': '. $bindings[$i]['val']);
                $statement->bindParam($i+1, $bindings[$i]['val'], $bindings[$i]['type']);
            }
        }
        return $statement;
    }

    public function insert($sql, $bindings) {
        $this->execute($sql, $bindings);
        return $this->pdo->lastInsertId();
    }

    public function update($sql, $bindings) {
        $this->execute($sql, $bindings);
        return true;
    }

    public function fetchAll($fetch_type, $sql, $bindings=array(), $class=null) {
        list($statement, $result) = $this->execute($sql, $bindings);
        if ( stripos($sql, 'select') === 0 ) {
            if ( $fetch_type === PDO::FETCH_CLASS ) {
                return $statement->fetchAll(PDO::FETCH_CLASS|PDO::FETCH_PROPS_LATE, $class);
            } else {
                return $statement->fetchAll($fetch_type);
            }
        } elseif ( stripos($sql, 'update' ) === 0 || stripos($sql, 'delete') === 0 ) {
            return $statement->rowCount();
        }
        return $result;
    }

    public function fetchFirst($fetch_type, $sql, $bindings=array(), $class=null) {
        if ( stripos($sql, 'update' ) === 0 || stripos($sql, 'delete') === 0 ) {
            throw new Exception('Unable to fetch first on UPDATE or DELETE');
        }
        $statement = $this->statement($sql, $bindings);
        if ( $fetch_type === PDO::FETCH_CLASS ) {
            $statement->setFetchMode(PDO::FETCH_CLASS|PDO::FETCH_PROPS_LATE, $class);
        } else {
            $statement->setFetchMode($fetch_type);
        }
        $statement->execute();
        return $statement->fetch();
    }
}
