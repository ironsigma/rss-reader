<?php
include 'db_service.php';
include_once 'logger.php';

LogFacility::addAppender(new FileLogAppender(Logger::TRACE, 'update.log'));
LogFacility::setRootLoggerLevel(Logger::TRACE);
LogFacility::setLoggerLevel('DbService', Logger::TRACE);

$log = LogFacility::getLogger('update.php');
$log->info('Starting RSS Update');

$service = DbService::getInstance();

function fetch_feed($url) {
    $curl = curl_init($url);
    curl_setopt($curl, CURLOPT_USERAGENT, "MagpieRSS/0.7 (http://magpierss.sf.net)");
    curl_setopt($curl, CURLOPT_HEADER, 0);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_TIMEOUT, 10);
    $data = curl_exec($curl);
    curl_close($curl);
    return $data;
}

function parse_posts($xml_source, $feed_id) {
    $log = LogFacility::getLogger('update.php:parse_posts');
    $log->trace('loading XML feed');
    $x = simplexml_load_string($xml_source);
    $log->trace('Feed loaded');

    $posts = array();
    if ( $x === false || count($x) == 0) {
        $log->warn('There doesn\'t seem to be any records in the feed id: '. $feed_id);
        return $posts;
    }

    // RSS
    if ( count($x->channel) !== 0 ) {
        $log->trace('Parsing as RSS feed');
        foreach( $x->channel->item as $item ) {
            $ts = strtotime($item->pubDate);
            $posts[] = new Post(
                (string) $item->title,
                $ts,
                (string) $item->link,
                md5("{$item->title}$ts"),
                $item->description,
                $feed_id
            );
        }

    // ATOM
    } elseif ( count($x->entry) !== 0 ) {
        $log->trace('Parsing as Atom feed');
        foreach( $x->entry as $entry ) {
            $ts = strtotime($entry->published);
            $posts[] = new Post(
                (string) $entry->title,
                $ts,
                (string) $entry->link,
                md5("{$entry->title}$ts"),
                $entry->summary,
                $feed_id
            );
        }
    }
    return $posts;
}

$feedUpdates = $service->findLatestUpdates();
$now = time();
$log->debug("Current time: ". date('c', $now));
$feeds = $service->findFeeds();
foreach ( $feeds as $feed ) {
    $log->debug("Checking {$feed->name}");
    // if there is a last update
    if ( array_key_exists($feed->id, $feedUpdates) ) {
        // calculate next update and check to see if it's time
        $log->trace('Last update '. date('c', $feedUpdates[$feed->id]->updated));
        $next_update =  $feedUpdates[$feed->id]->updated + $feed->update_freq * 60;
        $log->trace('Next update scheduled for '. date('c', $next_update));
        if ( $now <= $next_update ) {
            $log->trace('No update needed');
            continue;
        }
    } else {
        $log->trace('Not previously updated');
    }

    $log->debug('Fetching feed...');
    $data = fetch_feed($feed->url);

    $log->debug('Parsing feed...');
    $posts = parse_posts($data, $feed->id);

    $count = 0;
    $new = 0;
    $log->debug('Loading posts...');
    foreach ( $posts as $post ) {
        $count ++;
        if ( !$service->postExists($post) ) {
            $service->createPost($post);
            $new ++;
        }
    }

    $log->debug(sprintf('Found %d posts, %d where new', $count, $new));
    $service->createUpdate(new Update(time(), $count, $new, $feed->id));
}

// Cleanup posts
$post_cutoff = $now - 864000; // 10 days * 86400
$log->debug('Deleting read, unstared posts older than '. date('c', $post_cutoff));
$service->deleteReadPostBefore($post_cutoff);

$log->info('RSS Update Completed');
