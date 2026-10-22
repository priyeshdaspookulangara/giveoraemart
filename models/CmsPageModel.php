<?php
// models/CmsPageModel.php

require_once __DIR__ . '/../config/db.php';

class CmsPageModel {

    public static function getAll($status = null) {
        if ($status) {
            return db_execute("SELECT * FROM cms_pages WHERE status = ? ORDER BY title ASC", [$status], "s");
        }
        return db_execute("SELECT * FROM cms_pages ORDER BY title ASC");
    }

    public static function getBySlug($slug) {
        return db_fetch_one("SELECT * FROM cms_pages WHERE slug = ?", [$slug], "s");
    }

    public static function getById($id) {
        return db_fetch_one("SELECT * FROM cms_pages WHERE id = ?", [$id], "i");
    }

    public static function create($title, $slug, $content, $status = 'published') {
        $sql = "INSERT INTO cms_pages (title, slug, content, status) VALUES (?, ?, ?, ?)";
        return db_execute($sql, [$title, $slug, $content, $status], "ssss");
    }

    public static function update($id, $title, $slug, $content, $status) {
        $sql = "UPDATE cms_pages SET title = ?, slug = ?, content = ?, status = ? WHERE id = ?";
        return db_execute($sql, [$title, $slug, $content, $status, $id], "ssssi");
    }

    public static function delete($id) {
        return db_execute("DELETE FROM cms_pages WHERE id = ?", [$id], "i");
    }
}
