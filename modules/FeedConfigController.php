<?php
/**
 * Feed config controller
 * @package com\izylab\reader
 */
class FeedConfigController extends JsonController {
    /*
     * Display feed list
     */
    public function handleRequest($args) {
        $template = new Template('feed_config');
        $template->feeds = FeedDao::findAll();
        $template->folders = FolderDao::findAll();
        $template->page_title = "RSS Feed Configuration";
        $template->display();
    }

    public function handlePostRequest() {
        $log = LogFacility::getLogger('FeedConfigController.class');
        $request = $this->getJsonRequest();
        $log->info('POST: '. print_r($request, true));

        $feed = FeedDao::findById($request['id']);
        if ($request['op'] == 'active') {
            $feed->active = $request['value'];
            FeedDao::update($feed, array('active'));
            $this->setJsonResponse(array('status' => 'success'));

        } elseif ($request['op'] == 'sort') {
            $feed->newest_first = $request['value'];
            FeedDao::update($feed, array('newest_first'));
            $this->setJsonResponse(array('status' => 'success'));

        } elseif ($request['op'] == 'update') {
            $feed->update_freq = $request['value'];
            FeedDao::update($feed, array('update_freq'));
            $this->setJsonResponse(array('status' => 'success'));

        } elseif ($request['op'] == 'page') {
            $feed->per_page = $request['value'];
            FeedDao::update($feed, array('per_page'));
            $this->setJsonResponse(array('status' => 'success'));

        } elseif ($request['op'] == 'folder') {
            if ($request['value'] == 'none') {
                $feed->folder_id = null;
            } else {
                $feed->folder_id = $request['value'];
            }
            FeedDao::update($feed, array('folder_id'));
            $this->setJsonResponse(array('status' => 'success'));
        }

    }

}
