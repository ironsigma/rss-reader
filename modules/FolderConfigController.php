<?php
/**
 * Folder config controller
 * @package com\izylab\reader
 */
class FolderConfigController extends JsonController {
    /*
     * Display folder list
     */
    public function handleRequest($args) {
        $template = new Template('folder_config');
        $template->folders = FolderDao::findAllWithCount();
        $template->page_title = " - Folder Configuration";
        $template->display();
    }

    public function handlePostRequest() {
        $request = $this->getJsonRequest();

        if ($request['op'] == 'new-folder') {
            $folder = new Folder(array(
                'name' => $request['name'],
                'newest_first' => false,
                'per_page' => 10,
                'user_id' => 2,
            ));
            FolderDao::insert($folder);
            $this->setJsonResponse(array('status' => 'success'));
            return;
        }

        $folder = FolderDao::findById($request['id']);
        if ($request['op'] == 'page') {
            $folder->per_page = $request['value'];
            FolderDao::update($folder, array('per_page'));
            $this->setJsonResponse(array('status' => 'success'));

        } elseif ($request['op'] == 'del-folder') {
            FolderDao::delete($folder);
            $this->setJsonResponse(array('status' => 'success'));

        } elseif ($request['op'] == 'sort') {
            $folder->newest_first = $request['value'];
            FolderDao::update($folder, array('newest_first'));
            $this->setJsonResponse(array('status' => 'success'));

        }

    }
}
