<?php
include 'db_service.php';

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

function parsePosts($xml_source, $feed_id) {
    $x = simplexml_load_string($xml_source);

    $posts = array();
    if ( $x === false || count($x) == 0) {
        return $posts;
    }

    // RSS
    if ( count($x->channel) !== 0 ) {
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
$feeds = $service->findFeeds();
foreach ( $feeds as $feed ) {
    if ( array_key_exists($feed->id, $feedUpdates) ) {
        $next_update =  $feedUpdates[$feed->id]->updated + $feed->update_freq * 60;
        if ( $now <= $next_update ) {
            continue;
        }
    }

    $data = fetch_feed($feed->url);
    $posts = parsePosts($data, $feed->id);
    $count = 0;
    $new = 0;
    foreach ( $posts as $post ) {
        $count ++;
        if ( !$service->postExists($post) ) {
            $service->createPost($post);
            $new ++;
        }
    }

    $service->createUpdate(new Update(time(), $count, $new, $feed->id));

}
