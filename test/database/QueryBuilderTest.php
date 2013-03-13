<?php
class QueryBuilderTest extends PHPUnit_Framework_TestCase {
    public function testSelect() {

        $sav = QueryBuilder::selectSqlAndValues('table');
        $this->assertEquals('SELECT _t1."*" FROM table _t1', $sav['sql']);

        //$sav = QueryBuilder::selectSqlAndValues('table', array());
        //$this->assertEquals('SELECT _t1."*" FROM table _t1', $sav['sql']);

        $criteria = new Criteria();
        //$sav = QueryBuilder::selectSqlAndValues('table', array(), $criteria);
        //$this->assertEquals('SELECT _t1."*" FROM table _t1', $sav['sql']);

        $sav = QueryBuilder::selectSqlAndValues('table', array('name','add','phone'), $criteria);
        $this->assertEquals('SELECT _t1."name", _t1."add", _t1."phone" FROM table _t1', $sav['sql']);
    }
}
