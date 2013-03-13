<?php
class ConfigTest extends PHPUnit_Framework_TestCase {
    public function testRead() {
        Config::clear();
        $this->assertNull(Config::get(null));
        $this->assertNull(Config::get(''));
        $this->assertNull(Config::get('databaseX.driver'));

        Config::read(__DIR__.'/reader.yaml');
        $this->assertNull(Config::get(null));
        $this->assertNull(Config::get(''));
        $this->assertNull(Config::get('databaseX.driver'));
        $this->assertNull(Config::get('database.driverX'));
        $this->assertNull(Config::get('database.driver.INVALID_KEY'));
        $this->assertEquals('sqlite3', Config::get('database.driver'));

        $db = Config::get('database');
        $this->assertEquals('sqlite3', $db['driver']['_']);
    }

    public function testSet() {
        Config::clear();

        Config::set(null, null);
        $this->assertEquals(0, count(Config::all()));

        Config::set(null, 'foo');
        $this->assertEquals(0, count(Config::all()));

        Config::set('foo', null);
        $all = Config::all();
        $this->assertEquals(1, count($all));
        $this->assertNull($all['foo']);

        Config::set('foo', 'bar');
        $all = Config::all();
        $this->assertEquals(1, count($all));
        $this->assertEquals('bar', $all['foo']);

        Config::set('foo.myfoo', 'bar');
        $all = Config::all();

        $this->assertEquals(1, count($all));
        $this->assertEquals('bar', $all['foo']['_']);
        $this->assertEquals(1, count($all['foo']['myfoo']));
        $this->assertEquals('bar', $all['foo']['myfoo']);

    }
}
