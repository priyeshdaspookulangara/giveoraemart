<?php
// models/CmsPostModel.php

require_once __DIR__ . '/../config/db.php';

class CmsPostModel {

    public static function getAll($status = null, $limit = null) {
        $sql = "SELECT p.*, u.name as author_name
                FROM cms_posts p
                LEFT JOIN users u ON p.author_id = u.id";

        $params = [];
        $types = "";

        if ($status) {
            $sql .= " WHERE p.status = ?";
            $params[] = $status;
            $types .= "s";
        }

        $sql .= " ORDER BY p.created_at DESC";

        if ($limit) {
            $sql .= " LIMIT " . (int)$limit;
        }

        return db_execute($sql, $params, $types);
    }

    public static function getBySlug($slug) {
        $sql = "SELECT p.*, u.name as author_name
                FROM cms_posts p
                LEFT JOIN users u ON p.author_id = u.id
                WHERE p.slug = ?";
        return db_fetch_one($sql, [$slug], "s");
    }

    public static function getById($id) {
        return db_fetch_one("SELECT * FROM cms_posts WHERE id = ?", [$id], "i");
    }

    public static function create($title, $slug, $author_id, $content, $featured_image = null, $status = 'published') {
        $sql = "INSERT INTO cms_posts (title, slug, author_id, content, featured_image, status) VALUES (?, ?, ?, ?, ?, ?)";
        return db_execute($sql, [$title, $slug, $author_id, $content, $featured_image, $status], "ssisss");
    }

    public static function update($id, $title, $slug, $content, $featured_image = null, $status = 'published') {
        if ($featured_image !== null) {
            $sql = "UPDATE cms_posts SET title = ?, slug = ?, content = ?, featured_image = ?, status = ? WHERE id = ?";
            return db_execute($sql, [$title, $slug, $content, $featured_image, $status, $id], "sssssi");
        } else {
            $sql = "UPDATE cms_posts SET title = ?, slug = ?, content = ?, status = ? WHERE id = ?";
            return db_execute($sql, [$title, $slug, $content, $status, $id], "ssssi");
        }
    }

    public static function delete($id) {
        return db_execute("DELETE FROM cms_posts WHERE id = ?", [$id], "i");
    }
}
