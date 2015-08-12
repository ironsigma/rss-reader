<?php
/**
 * Post DAO
 * @package com\hawkprime\reader
 */
class PostDao {
    public static function findUnreadArticlesInFolder($folder_id, $order, $limit, $offset) {
        return Database::table(Post::getTable())
            ->false('read')
            ->join('feed', 'id', 'feed_id')
            ->join('folder', 'id', 'feed.folder_id')
            ->equal('folder.id', $folder_id, Entity::TYPE_INT)
            ->true('feed.active')
            ->orderBy('published', $order)
            ->page($limit, $offset)
            ->select(array_merge(Post::getColumnNames(), array(array('feed.name', 'feed'))))
            ->fetch('Post');
    }
    public static function findStaredArticles($limit, $offset) {
        return Database::table(Post::getTable())
            ->join('feed', 'id', 'feed_id')
            ->join('post_state', 'post_id', 'id')
            ->true('feed.active')
            ->true('post_state.stared')
            ->equal('post_state.user_id', Session::getUserId(), Entity::TYPE_INT)
            ->orderBy('published', 'ASC')
            ->page($limit, $offset)
            ->select(array_merge(Post::getColumnNames(), array(array('feed.name', 'feed'))))
            ->fetch('Post');
    }
    public static function findUnreadArticlesInFeed($feed_id, $order, $limit, $offset) {
        return Database::table(Post::getTable())
            ->equal('feed_id', $feed_id, Entity::TYPE_INT)
            ->false('read')
            ->orderBy('published', $order)
            ->page($limit, $offset)
            ->fetch('Post');
    }
    public static function postFolderCount() {
        $results = Database::table(Post::getTable())
                ->count('*', 'count')
                ->false('read')
                ->join('feed', 'id', 'feed_id')
                ->join('folder', 'id', 'feed.folder_id')
                ->true('feed.active')
                ->groupBy('folder.id')
                ->select(array('folder.id'))
                ->fetch();

        $counts = array();
        foreach ( $results as $row ) {
            $counts[$row['id']] = intval($row['count']);
        }
        return $counts;
    }
    public static function staredCount() {
        $result = Database::table(State::getTable())
                ->join('post', 'id', 'post_id')
                ->join('feed', 'id', 'post.feed_id')
                ->count('*', 'count')
                ->true('stared')
                ->true('feed.active')
                ->equal('user_id', Session::getUserId(), Entity::TYPE_INT)
                ->first();
        return intval($result['count']);
    }
    public static function countUnreadInFeed($feed_id) {
        $result = Database::table(Post::getTable())
                ->count('*', 'count')
                ->false('read')
                ->equal('feed_id', $feed_id, Entity::TYPE_INT)
                ->first();
        return intval($result['count']);
    }
    public static function countUnread() {
        $result = Database::table(Post::getTable())
                ->select(array('feed_id'))
                ->count('*', 'count')
                ->false('read')
                ->groupBy('feed_id')
                ->fetch();
        $map = array();
        foreach ($result as $feed) {
            $map[$feed['feed_id']] = $feed['count'];
        }
        return $map;
    }
    public static function countAll() {
        $result = Database::table(Post::getTable())
                ->select(array('feed_id'))
                ->count('*', 'count')
                ->groupBy('feed_id')
                ->fetch();
        $map = array();
        foreach ($result as $feed) {
            $map[$feed['feed_id']] = $feed['count'];
        }
        return $map;
    }
    public static function countUnreadInFolder($folder_id) {
        $result = Database::table(Post::getTable())
                ->count('*', 'count')
                ->false('read')
                ->join('feed', 'id', 'feed_id')
                ->join('folder', 'id', 'feed.folder_id')
                ->true('feed.active')
                ->equal('folder.id', $folder_id, Entity::TYPE_INT)
                ->first();
        return intval($result['count']);
    }
    public static function insert(Post $post) {
        $post->id = Database::table(Post::getTable())
            ->insert($post);
        return $post;
    }
    public static function postExists($post) {
        $result = Database::table(Post::getTable())
            ->count('*', 'count')
            ->equal('guid', $post->guid, Entity::TYPE_STR)
            ->first();

        return intval($result['count']) !== 0;
    }
    public static function markRead($ids, $feed_id) {
        $query = Database::table(Post::getTable());

        if ( $feed_id !== null ) {
            $query->equal('feed_id', $feed_id, Entity::TYPE_INT);
        }
        if ( $ids !== null ) {
            $query->in('id', $ids, Entity::TYPE_INT);
        }

        $entity = new Post(array('read' => true));
        $query->update($entity, array('read'));
    }
    public static function markFolderRead($folder_id) {
        $entity = new Post(array('read' => true));
        $query = Database::table(Post::getTable())
            ->join('feed', 'id', 'feed_id')
            ->equal('read', false, Entity::TYPE_BOOL)
            ->equal('feed.folder_id', $folder_id, Entity::TYPE_INT)
            ->true('feed.active')
            ->update($entity, array('read'));
    }
    public static function updateStar($star, $id) {
        $state = Database::table(State::getTable())
            ->equal('user_id', Session::getUserId(), Entity::TYPE_INT)
            ->equal('post_id', $id, Entity::TYPE_INT)
            ->first('State');

        if ( !$state ) {
            $state = new State(array(
                'read' => false,
                'stared' => $star,
                'post_id' => $id,
                'user_id' => Session::getUserId(),
            ));
            Database::table(State::getTable())
                ->insert($state);
        } else {
            $state->stared = $star;
            Database::table(State::getTable())
                ->equal('id', $state->id, Entity::TYPE_INT)
                ->update($state, array('stared'));
        }

        $entity = new Post(array('stared' => $star));
        Database::table(Post::getTable())
            ->equal('id', $id, Entity::TYPE_INT)
            ->update($entity, array('stared'));
    }
    public static function deleteOldPosts() {
        $sql = 'DELETE FROM post WHERE published < NOW() - INTERVAL 30 DAY';
        Database::connection()->execute($sql);
    }
}
