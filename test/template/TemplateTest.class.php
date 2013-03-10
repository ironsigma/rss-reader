<?php
class TemplateTest extends PHPUnit_Framework_TestCase {
    public function testJoinPaths() {
        $path = Template::joinPaths();
        $this->assertEquals('', $path);

        $path = Template::joinPaths('');
        $this->assertEquals('', $path);

        $path = Template::joinPaths('/');
        $this->assertEquals('/', $path);

        $path = Template::joinPaths('foo', '/');
        $this->assertEquals('foo', $path);

        $path = Template::joinPaths('foo', 'var');
        $this->assertEquals('foo/var', $path);

        $path = Template::joinPaths('/foo', 'var');
        $this->assertEquals('/foo/var', $path);

        $path = Template::joinPaths('foo', '/var');
        $this->assertEquals('foo/var', $path);

        $path = Template::joinPaths('foo', '', '/var/');
        $this->assertEquals('foo/var', $path);

        $path = Template::joinPaths('foo/', '//', '/var/');
        $this->assertEquals('foo/var', $path);

        $path = Template::joinPaths('foo/', '/////', '/var/');
        $this->assertEquals('foo/var', $path);

        $path = Template::joinPaths('foo/', '/var///foo/', '/var/');
        $this->assertEquals('foo/var///foo/var', $path);
    }
}
