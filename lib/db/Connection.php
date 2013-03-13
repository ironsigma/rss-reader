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

    protected function execute($sql, array $bindings) {
        $this->log->info($sql);
        $statement = $this->pdo->prepare($sql);
        for ( $i = 0; $i < count($bindings); $i ++ ) {
            $this->log->info( 'Param '. ($i+1) .': '. $bindings[$i]['val']);
            $statement->bindParam($i+1, $bindings[$i]['val'], $bindings[$i]['type']);
        }
        $result = $statement->execute();
        return array($statement, $result);
    }

    public function fetch($fetch_type, $sql, $bindings=array(), $class=null) {
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

}
