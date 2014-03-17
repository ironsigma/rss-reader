<?php
/**
 * Folder DAO
 * @package com\izylab\reader
 */
class FolderDao {
    public static function findAll() {
        return Database::table(Folder::getTable())
            ->fetch('Folder');
    }
    public static function findById($id) {
        return Database::table(Folder::getTable())
            ->equal('id', $id, Entity::TYPE_INT)
            ->first('Folder');
    }
}
