<?php
/**
 * Link controller
 * @package com\hawkprime\reader
 */
class LinkController {
//    private static $log;
//
//    public function __construct() {
//        if ( self::$log === null ) {
//            self::$log = LogFacility::getLogger('LinkController.class');
//        }
//    }

    public function handleRequest($args) {
        $post_id = $args['post'];
        $feed_id = $args['feed'];
        $url = Base64::decode($args['url']);
        //self::$log->debug("Link post[$post_id] feed[$feed_id]: $url");
        header("Location: $url");
    }
}
