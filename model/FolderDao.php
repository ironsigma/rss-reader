<?php
/**
 * Folder DAO
 * @package com\izylab\reader
 */
class FolderDao {
    public static function findById($id) {
        return Database::table(Folder::getTable())
            ->equal('id', $id, Entity::TYPE_INT)
            ->first('Folder');
    }
}
