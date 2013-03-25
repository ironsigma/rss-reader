<?php
/**
 * Folder DAO
 * @package com\izylab\reader
 */
class FolderDao {
    public static function findById($id) {
        return DB::table(Folder::getTable())
            ->equal('id', $id, PDO::PARAM_INT)
            ->first('Folder');
    }

}
