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
    public static function findAllWithCount() {
        $sql = 'SELECT f.*, COUNT(d.id) AS feed_count '
            . 'FROM folder f LEFT JOIN feed d ON d.folder_id = f.id '
            . 'GROUP BY f.id '
            . 'ORDER BY f.name';
        list($statement,) = Database::connection()->execute($sql);
        return $statement->fetchAll(PDO::FETCH_CLASS|PDO::FETCH_PROPS_LATE, 'Folder');
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
