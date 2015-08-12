<?php
/**
 * Simple ATOM and RSS feed parser.
 * @package com\hawkprime\reader
 */
class FeedParser {
    private static $log;
    protected $url;

    public function __construct($url=null) {
        $this->url = $url;
        if ( self::$log === null ) {
            self::$log = LogFacility::getLogger('FeedParser.class');
        }
    }

    public function getPosts() {
        return $this->parseFeed($this->fetchFeed());
    }

    public function parseFeed($xml_source) {
        self::$log->trace('loading XML feed');
        $x = simplexml_load_string($xml_source);
        if ( $x === false ) {
            self::$log->error('Error parsing feed');
            return null;
        }
        self::$log->trace('Feed loaded');

        $posts = array();
        if ( count($x) == 0) {
            self::$log->warn('There doesn\'t seem to be any records in the feed');
            return $posts;
        }

        // RSS
        if ( count($x->channel) !== 0 ) {
            if ( count($x->channel->item) !== 0 ) {
                self::$log->trace('Parsing as RSS feed');
                foreach( $x->channel->item as $item ) {
                    $ts = strtotime($item->pubDate);
                    $guid = (string)$item->guid;
                    $guid = !$guid ? ((string)$item->title).$ts : $guid;
                    $posts[] = array(
                        'title' => (string) $item->title,
                        'published' => $ts,
                        'link' => (string) $item->link,
                        'guid' => md5($guid),
                        'text' => (string)$item->description,
                    );
                }
            } else {
                self::$log->trace('Parsing as RDF RSS feed');
                foreach( $x->item as $item ) {
                    $ts = strtotime(FeedParser::getChild($item, 'dc', 'date'));
                    $guid = (string)$item->guid;
                    $guid = !$guid ? ((string)$item->title).$ts : $guid;
                    $posts[] = array(
                        'title' => (string) $item->title,
                        'published' => $ts,
                        'link' => (string) $item->link,
                        'guid' => md5($guid),
                        'text' => (string)$item->description,
                    );
                }
            }

        // ATOM
        } elseif ( count($x->entry) !== 0 ) {
            self::$log->trace('Parsing as Atom feed');
            foreach( $x->entry as $entry ) {
                $ts = strtotime((string)$entry->published);
                $guid = (string)$entry->id;
                $guid = !$guid ? ((string)$entry->title).$ts : $guid;
                $posts[] = array (
                    'title' => (string) $entry->title,
                    'published' => $ts,
                    'link' => (string) $entry->link->attributes()->href,
                    'guid' => md5($guid),
                    'text' => (string)$entry->content
                );
            }
        }
        return $posts;
    }

    protected function fetchFeed() {
        self::$log->debug('Fetching feed "'. $this->url .'" ...');
        $curl = curl_init($this->url);
        curl_setopt($curl, CURLOPT_USERAGENT, "MagpieRSS/0.7 (http://magpierss.sf.net)");
        curl_setopt($curl, CURLOPT_HEADER, 0);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_TIMEOUT, 10);
        $data = curl_exec($curl);
        curl_close($curl);
        return $data;
    }

    protected static function getChild($el, $ns, $name) {
        foreach ( $el->children($ns, true) as $child ) {
            if ( $child->getName() == $name ) {
                return $child;
            }
        }
        return null;
    }

}
