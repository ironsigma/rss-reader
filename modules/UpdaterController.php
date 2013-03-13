<?php
/**
 * Automatic update controller
 * @package com\izylab\reader
 */
class UpdaterController {
    private static $log;

    public function __construct() {
        if ( self::$log === null ) {
            self::$log = LogFacility::getLogger('UpdaterController.class');
            LogFacility::setLoggerLevel('QueryBuilder.class', Logger::WARN);
        }
    }

    public function handleRequest($args) {
        self::$log->info('Starting RSS Update');
        $feedUpdates = UpdateDao::findLatestUpdates();
        $now = time();
        self::$log->debug("Current time: ". date('c', $now));
        $feeds = FeedDao::findAll();
        foreach ( $feeds as $feed ) {
            self::$log->debug("Checking {$feed->name}");
            // if there is a last update
            if ( array_key_exists($feed->id, $feedUpdates) ) {
                // calculate next update and check to see if it's time
                self::$log->trace('Last update '. date('c', $feedUpdates[$feed->id]->updated));
                $next_update =  $feedUpdates[$feed->id]->updated + $feed->update_freq * 60;
                self::$log->trace('Next update scheduled for '. date('c', $next_update));
                if ( $now <= $next_update ) {
                    self::$log->trace('No update needed');
                    continue;
                }
            } else {
                self::$log->trace('Not previously updated');
            }

            self::$log->info("Updating {$feed->name}");
            $count = 0;
            $new = 0;
            $parser = new FeedParser($feed->url);
            $posts = $parser->getPosts();

            if ( $posts === null ) {
                self::$log->error("Parsing feed '{$feed->name}' Failed.");

            } else {
                self::$log->debug('Loading posts...');
                foreach ( $posts as $post_data ) {
                    $post_data['feed_id'] = $feed->id;
                    $post = new Post($post_data);
                    $count ++;
                    if ( !PostDao::postExists($post) ) {
                        $post->read = false;
                        $post->stared = false;
                        PostDao::insert($post);
                        $new ++;
                    }
                }

                self::$log->info(sprintf('Found %d posts, %d where new', $count, $new));
            }

            UpdateDao::insert(new Update(array(
                'updated' => time(),
                'total_count' => $count,
                'new_count' => $new,
                'feed_id' => $feed->id,
            )));
        }

        // Cleanup posts
        //...$post_cutoff = $now - 864000; // 10 days * 86400
        //...self::$log->debug('Deleting read, unstared posts older than '. date('c', $post_cutoff));
        //...PostDao::deleteReadPostBefore($post_cutoff);

        self::$log->info('RSS Update Completed');
    }
}
