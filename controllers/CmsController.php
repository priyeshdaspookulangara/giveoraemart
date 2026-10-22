<?php
// controllers/CmsController.php

require_once __DIR__ . '/../models/CmsPageModel.php';
require_once __DIR__ . '/../models/CmsPostModel.php';

class CmsController {

    public static function renderPage($slug) {
        $page = CmsPageModel::getBySlug($slug);
        if (!$page || $page['status'] !== 'published') {
            return null;
        }
        return $page;
    }

    public static function renderPost($slug) {
        $post = CmsPostModel::getBySlug($slug);
        if (!$post || $post['status'] !== 'published') {
            return null;
        }
        return $post;
    }

    public static function getRecentPosts($limit = 3) {
        return CmsPostModel::getAll('published', $limit);
    }
}
