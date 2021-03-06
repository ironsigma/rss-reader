<?php
class MySqlGrammarTest extends PHPUnit_Framework_TestCase {

    protected function setUp() {
        Config::clear();
        Config::set('database.driver', 'mysql');
        Config::set('database.mysql.username', 'rss_user');
        Config::set('database.mysql.password', 'rss_pass');
        Config::set('database.mysql.database', 'rss_reader');
        Config::set('database.mysql.host', 'localhost');
        Config::set('database.mysql.port', '3306');
    }

    public function testSelect() {
        $sql = Database::table('post')->sql(array('type'=>'select'));
        $this->assertEquals('SELECT _p1.* FROM post _p1', $sql);

        $sql = Database::table('post')->select(array('name'))->sql(array('type'=>'select'));
        $this->assertEquals('SELECT _p1.`name` FROM post _p1', $sql);

        $sql = Database::table('post')->select(array(array('name', 'title')))->sql(array('type'=>'select'));
        $this->assertEquals('SELECT _p1.`name` AS `title` FROM post _p1', $sql);

        $sql = Database::table('post')->select(array('published', array('name', 'title')))->sql(array('type'=>'select'));
        $this->assertEquals('SELECT _p1.`published`, _p1.`name` AS `title` FROM post _p1', $sql);
    }

    public function testCount() {
        $sql = Database::table('post')->count('*', 'posts')->sql(array('type'=>'select'));
        $this->assertEquals('SELECT COUNT(*) AS `posts` FROM post _p1', $sql);
    }

    public function testJoin() {
        $sql = Database::table('post')
            ->join('feed', 'id', 'feed_id')
            ->sql(array('type'=>'select'));
        $this->assertEquals('SELECT _p1.* FROM post _p1 '
            .'INNER JOIN feed _f2 ON _f2.`id`=_p1.`feed_id`', $sql);

        $sql = Database::table('post')
            ->leftJoin('feed', 'id', 'feed_id')
            ->sql(array('type'=>'select'));
        $this->assertEquals('SELECT _p1.* FROM post _p1 '
            .'LEFT JOIN feed _f2 ON _f2.`id`=_p1.`feed_id`', $sql);

        $sql = Database::table('post')
            ->crossJoin('feed', 'id', 'feed_id')
            ->sql(array('type'=>'select'));
        $this->assertEquals('SELECT _p1.* FROM post _p1 '
            .'CROSS JOIN feed _f2 ON _f2.`id`=_p1.`feed_id`', $sql);

        $sql = Database::table('post')
            ->join('feed', 'id', 'feed_id')
            ->join('author', 'id', 'author_id')
            ->sql(array('type'=>'select'));
        $this->assertEquals('SELECT _p1.* FROM post _p1 '
            .'INNER JOIN feed _f2 ON _f2.`id`=_p1.`feed_id` '
            .'INNER JOIN author _a3 ON _a3.`id`=_p1.`author_id`', $sql);

        $sql = Database::table('post')
            ->join('feed', 'id', 'feed_id')
            ->join('folder', 'id', 'feed.folder_id')
            ->sql(array('type'=>'select'));
        $this->assertEquals('SELECT _p1.* FROM post _p1 '
            .'INNER JOIN feed _f2 ON _f2.`id`=_p1.`feed_id` '
            .'INNER JOIN folder _f3 ON _f3.`id`=_f2.`folder_id`', $sql);
    }

    public function testWhere() {
        $query = Database::table('post')->equal('author', 'John', Entity::TYPE_STR);
        $bindings = $query->getBindings();
        $this->assertEquals('SELECT _p1.* FROM post _p1 WHERE _p1.`author`=?', $query->sql(array('type'=>'select')));
        $this->assertEquals('John', $bindings[0]['val']);

        $query = Database::table('post')->notEqual('author', 'John', Entity::TYPE_STR);
        $bindings = $query->getBindings();
        $this->assertEquals('SELECT _p1.* FROM post _p1 WHERE _p1.`author`!=?', $query->sql(array('type'=>'select')));
        $this->assertEquals('John', $bindings[0]['val']);

        $query = Database::table('post')->greaterThan('likes', 4);
        $bindings = $query->getBindings();
        $this->assertEquals('SELECT _p1.* FROM post _p1 WHERE _p1.`likes`>?', $query->sql(array('type'=>'select')));
        $this->assertEquals(4, $bindings[0]['val']);

        $query = Database::table('post')->greaterThanEqual('likes', 4);
        $bindings = $query->getBindings();
        $this->assertEquals('SELECT _p1.* FROM post _p1 WHERE _p1.`likes`>=?', $query->sql(array('type'=>'select')));
        $this->assertEquals(4, $bindings[0]['val']);

        $query = Database::table('post')->lessThan('likes', 4);
        $bindings = $query->getBindings();
        $this->assertEquals('SELECT _p1.* FROM post _p1 WHERE _p1.`likes`<?', $query->sql(array('type'=>'select')));
        $this->assertEquals(4, $bindings[0]['val']);

        $query = Database::table('post')->lessThanEqual('likes', 4);
        $bindings = $query->getBindings();
        $this->assertEquals('SELECT _p1.* FROM post _p1 WHERE _p1.`likes`<=?', $query->sql(array('type'=>'select')));
        $this->assertEquals(4, $bindings[0]['val']);

        $query = Database::table('post')->isNull('stared');
        $bindings = $query->getBindings();
        $this->assertEquals('SELECT _p1.* FROM post _p1 WHERE _p1.`stared` IS NULL', $query->sql(array('type'=>'select')));
        $this->assertEquals(0, count($bindings));

        $query = Database::table('post')->isNotNull('stared');
        $bindings = $query->getBindings();
        $this->assertEquals('SELECT _p1.* FROM post _p1 WHERE _p1.`stared` NOT NULL', $query->sql(array('type'=>'select')));
        $this->assertEquals(0, count($bindings));

        $query = Database::table('post')->true('stared');
        $bindings = $query->getBindings();
        $this->assertEquals('SELECT _p1.* FROM post _p1 WHERE _p1.`stared`=?', $query->sql(array('type'=>'select')));
        $this->assertEquals(true, $bindings[0]['val']);

        $query = Database::table('post')->false('stared');
        $bindings = $query->getBindings();
        $this->assertEquals('SELECT _p1.* FROM post _p1 WHERE _p1.`stared`=?', $query->sql(array('type'=>'select')));
        $this->assertEquals(false, $bindings[0]['val']);

        $query = Database::table('post')->in('id', array(1, 3, 4));
        $bindings = $query->getBindings();
        $this->assertEquals('SELECT _p1.* FROM post _p1 WHERE _p1.`id` IN(?,?,?)', $query->sql(array('type'=>'select')));
        $this->assertEquals(1, $bindings[0]['val']);
        $this->assertEquals(3, $bindings[1]['val']);
        $this->assertEquals(4, $bindings[2]['val']);
    }

    public function testBindings() {
        $query = Database::table('post')
            ->equal('author', 'John', Entity::TYPE_STR)
            ->notEqual('author', 'Janet', Entity::TYPE_STR)
            ->greaterThan('likes', 4)
            ->greaterThanEqual('likes', 8)
            ->lessThan('likes', 3)
            ->lessThanEqual('likes', 1)
            ->isNull('stared')
            ->isNotNull('feed_id')
            ->true('shared')
            ->false('read')
            ->in('id', array(1344, 3023, 2454));

        $bindings = $query->getBindings();

        $this->assertEquals('John', $bindings[0]['val']);
        $this->assertEquals('Janet', $bindings[1]['val']);
        $this->assertEquals(4, $bindings[2]['val']);
        $this->assertEquals(8, $bindings[3]['val']);
        $this->assertEquals(3, $bindings[4]['val']);
        $this->assertEquals(1, $bindings[5]['val']);
        $this->assertEquals(true, $bindings[6]['val']);
        $this->assertEquals(false, $bindings[7]['val']);
        $this->assertEquals(1344, $bindings[8]['val']);
        $this->assertEquals(3023, $bindings[9]['val']);
        $this->assertEquals(2454, $bindings[10]['val']);

        $query = Database::table('post')
            ->isNull('stared')
            ->lessThanEqual('likes', 1)
            ->lessThan('likes', 3)
            ->notEqual('author', 'Janet', Entity::TYPE_STR)
            ->equal('author', 'John', Entity::TYPE_STR)
            ->in('id', array(3023, 2454, 1344))
            ->true('shared')
            ->greaterThanEqual('likes', 8)
            ->isNotNull('feed_id')
            ->greaterThan('likes', 4)
            ->false('read');

        $bindings = $query->getBindings();

        $this->assertEquals(1, $bindings[0]['val']);
        $this->assertEquals(3, $bindings[1]['val']);
        $this->assertEquals('Janet', $bindings[2]['val']);
        $this->assertEquals('John', $bindings[3]['val']);
        $this->assertEquals(3023, $bindings[4]['val']);
        $this->assertEquals(2454, $bindings[5]['val']);
        $this->assertEquals(1344, $bindings[6]['val']);
        $this->assertEquals(true, $bindings[7]['val']);
        $this->assertEquals(8, $bindings[8]['val']);
        $this->assertEquals(4, $bindings[9]['val']);
        $this->assertEquals(false, $bindings[10]['val']);
    }

    public function testGroupOrderPage() {
        $sql = Database::table('post')->groupBy('feed_id')->sql(array('type'=>'select'));
        $this->assertEquals('SELECT _p1.* FROM post _p1 GROUP BY _p1.`feed_id`', $sql);

        $sql = Database::table('post')->equal('author', 'john', Entity::TYPE_STR)->groupBy('feed_id')->sql(array('type'=>'select'));
        $this->assertEquals('SELECT _p1.* FROM post _p1 WHERE _p1.`author`=? GROUP BY _p1.`feed_id`', $sql);

        $sql = Database::table('post')->orderBy('published')->sql(array('type'=>'select'));
        $this->assertEquals('SELECT _p1.* FROM post _p1 ORDER BY _p1.`published` ASC', $sql);

        $sql = Database::table('post')->orderBy('published', 'DESC')->sql(array('type'=>'select'));
        $this->assertEquals('SELECT _p1.* FROM post _p1 ORDER BY _p1.`published` DESC', $sql);

        $sql = Database::table('post')->equal('author', 'john', Entity::TYPE_STR)->orderBy('published')->sql(array('type'=>'select'));
        $this->assertEquals('SELECT _p1.* FROM post _p1 WHERE _p1.`author`=? ORDER BY _p1.`published` ASC', $sql);

        $sql = Database::table('post')->page(10, 20)->sql(array('type'=>'select'));
        $this->assertEquals('SELECT _p1.* FROM post _p1 LIMIT 10 OFFSET 20', $sql);

        $sql = Database::table('post')->equal('author', 'john', Entity::TYPE_STR)->page(5, 50)->sql(array('type'=>'select'));
        $this->assertEquals('SELECT _p1.* FROM post _p1 WHERE _p1.`author`=? LIMIT 5 OFFSET 50', $sql);

        $sql = Database::table('post')
            ->equal('author', 'john', Entity::TYPE_STR)
            ->orderBy('published', 'DESC')
            ->groupBy('feed_id')
            ->page(5, 50)
            ->sql(array('type'=>'select'));
        $this->assertEquals('SELECT _p1.* FROM post _p1 '
            .'WHERE _p1.`author`=? '
            .'GROUP BY _p1.`feed_id` '
            .'ORDER BY _p1.`published` DESC '
            .'LIMIT 5 OFFSET 50', $sql);

        $sql = Database::table('post')
            ->equal('author', 'john', Entity::TYPE_STR)
            ->join('feed', 'id', 'feed_id')
            ->join('folder', 'id', 'feed.folder_id')
            ->orderBy('folder.name', 'DESC')
            ->groupBy('feed.id')
            ->page(5, 50)
            ->sql(array('type'=>'select'));
        $this->assertEquals('SELECT _p1.* FROM post _p1 '
            .'INNER JOIN feed _f2 ON _f2.`id`=_p1.`feed_id` '
            .'INNER JOIN folder _f3 ON _f3.`id`=_f2.`folder_id` '
            .'WHERE _p1.`author`=? '
            .'GROUP BY _f2.`id` '
            .'ORDER BY _f3.`name` DESC '
            .'LIMIT 5 OFFSET 50', $sql);
    }

    public function testInsert() {
        $post = new Post(array(
            'title' => 'New iPhone',
            'guid' => 'aaa888aaa',
            'published' => '2013-03-15 16:01:03',
            'text' => 'New Post',
            'link' => 'http://apple.com/',
            'read' => true,
            'stared' => false,
            'feed_id' => 23,
        ));

        $sql = Database::table(Post::getTable())
            ->sql(array('type'=>'insert', 'entity'=>$post));

        $this->assertEquals('INSERT INTO post (`title`,`published`,`text`,`link`,`read`,`stared`,`guid`,`feed_id`) VALUES (?,?,?,?,?,?,?,?)', $sql);

        $sql = Database::table(Post::getTable())
            ->sql(array('type'=>'insert', 'entity'=>$post, 'columns'=>array('title','link','read')));

        $this->assertEquals('INSERT INTO post (`title`,`link`,`read`) VALUES (?,?,?)', $sql);
    }

    public function testUpdate() {
        $post = new Post(array(
            'id' => 10074,
            'title' => 'Updated iPhone',
            'guid' => 'aaa888aaa',
            'published' => '2013-03-15 16:01:03',
            'text' => 'New Post',
            'link' => 'http://apple.com/',
            'read' => true,
            'stared' => false,
            'feed_id' => 23,
        ));

        $sql = Database::table(Post::getTable())
            ->equal('id', $post->id, Entity::TYPE_INT)
            ->sql(array('type'=>'update', 'entity'=>$post));

        // UPDATE post p
        //   INNER JOIN feed f ON p.feed_id=f.id
        // SET p.read=1
        // WHERE p.read=0
        //   AND f.folder_id=2;

        $post = new Post(array(
            'read' => false,
        ));

        $sql = Database::table(Post::getTable())
            ->join('feed', 'id', 'feed_id')
            ->equal('read', false, Entity::TYPE_BOOL)
            ->equal('feed.folder_id', 2, Entity::TYPE_INT)
            ->sql(array('type'=>'update', 'entity'=>$post, 'columns'=>array('read')));
    }
}
