<?php
/**
 * Folder DAO
 * @package com\izylab\reader
 */
class FolderDao {
    public static function findById($id) {
        return DB::table(Folder::getTable())
            ->equal('id', $id, Entity::TYPE_INT)
            ->first('Folder');
    }

}
