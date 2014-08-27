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
        $template->updates = UpdateDao::findLatestUpdates();
        $template->unreadPostCount = PostDao::countUnread();
        $template->totalPostCount = PostDao::countAll();
        $template->page_title = " - Feed Configuration";
        $template->display();
    }

    public function handlePostRequest() {
        $request = $this->getJsonRequest();
        if ($request['op'] == 'new-feed') {
            $feed = new Feed(array(
                'name' => $request['name'],
                'url' => $request['url'],
                'active' => false,
                'newest_first' => false,
                'update_freq' => 60,
                'per_page' => 10,
                'user_id' => 2,
            ));
            FeedDao::insert($feed);
            $this->setJsonResponse(array('status' => 'success'));
            return;
        }

        $feed = FeedDao::findById($request['id']);
        if ($request['op'] == 'active') {
            $feed->active = $request['value'];
            FeedDao::update($feed, array('active'));
            $this->setJsonResponse(array('status' => 'success'));

        } elseif ($request['op'] == 'del-feed') {
            FeedDao::delete($feed);
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
