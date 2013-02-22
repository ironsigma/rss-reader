<?php
/**
 * Simple ATOM and RSS feed parser.
 * @package com\izylab\reader
 */
class FeedParser {
    private static $log;
    protected $url;

    public function __construct($url) {
        $this->url = $url;
        if ( self::$log === null ) {
            self::$log = LogFacility::getLogger('FeedParser.class');
        }
    }

    public function getPosts() {
        $xml_source = $this->fetchFeed();

        self::$log->trace('loading XML feed');
        $x = simplexml_load_string($xml_source);
        self::$log->trace('Feed loaded');

        $posts = array();
        if ( $x === false || count($x) == 0) {
            self::$log->warn('There doesn\'t seem to be any records in the feed');
            return $posts;
        }

        // RSS
        if ( count($x->channel) !== 0 ) {
            self::$log->trace('Parsing as RSS feed');
            foreach( $x->channel->item as $item ) {
                $ts = strtotime($item->pubDate);
                $posts[] = array(
                    'title' => (string) $item->title,
                    'published' => $ts,
                    'link' => (string) $item->link,
                    'guid' => md5("{$item->title}$ts"),
                    'text' => $item->description
                );
            }

        // ATOM
        } elseif ( count($x->entry) !== 0 ) {
            self::$log->trace('Parsing as Atom feed');
            foreach( $x->entry as $entry ) {
                $ts = strtotime((string)$entry->published);
                $posts[] = array (
                    'title' => (string) $entry->title,
                    'published' => $ts,
                    'link' => (string) $entry->link->attributes()->href,
                    'guid' => md5(((string)$entry->title).$ts),
                    'text' => (string)$entry->content
                );
            }
        }
        return $posts;
    }

    protected function fetchFeed() {
        self::$log->debug('Fetching feed...');
        $curl = curl_init($this->url);
        curl_setopt($curl, CURLOPT_USERAGENT, "MagpieRSS/0.7 (http://magpierss.sf.net)");
        curl_setopt($curl, CURLOPT_HEADER, 0);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_TIMEOUT, 10);
        $data = curl_exec($curl);
        curl_close($curl);
        return $data;
    }

}
