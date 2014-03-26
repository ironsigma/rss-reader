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
    public static function delete($folder) {
        $sql = 'DELETE FROM folder WHERE id='. $folder->id;
        Database::connection()->execute($sql);
    }
    public static function insert(Folder $folder) {
        $folder->id = Database::table(Folder::getTable())->insert($folder);
        return $folder;
    }
    public static function update($folder, $columns=null) {
        Database::table(Folder::getTable())
            ->equal('id', $folder->id, Entity::TYPE_INT)
            ->update($folder, $columns);
    }
}
