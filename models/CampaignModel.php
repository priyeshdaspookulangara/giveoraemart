<?php
// models/CampaignModel.php

require_once __DIR__ . '/../config/db.php';

class CampaignModel {

    public static function getAll($status = null) {
        if ($status) {
            return db_execute("SELECT * FROM campaigns WHERE status = ? ORDER BY created_at DESC", [$status], "s");
        }
        return db_execute("SELECT * FROM campaigns ORDER BY created_at DESC");
    }

    public static function getBySlug($slug) {
        return db_fetch_one("SELECT * FROM campaigns WHERE slug = ?", [$slug], "s");
    }

    public static function getById($id) {
        return db_fetch_one("SELECT * FROM campaigns WHERE id = ?", [$id], "i");
    }

    public static function create($title, $slug, $description, $goal_amount, $status = 'active', $image_url = null) {
        $sql = "INSERT INTO campaigns (title, slug, description, goal_amount, raised_amount, status, image_url) VALUES (?, ?, ?, ?, 0.00, ?, ?)";
        return db_execute($sql, [$title, $slug, $description, $goal_amount, $status, $image_url], "sssds");
    }

    public static function update($id, $title, $slug, $description, $goal_amount, $status, $image_url = null) {
        if ($image_url !== null) {
            $sql = "UPDATE campaigns SET title = ?, slug = ?, description = ?, goal_amount = ?, status = ?, image_url = ? WHERE id = ?";
            return db_execute($sql, [$title, $slug, $description, $goal_amount, $status, $image_url, $id], "sssdssi");
        } else {
            $sql = "UPDATE campaigns SET title = ?, slug = ?, description = ?, goal_amount = ?, status = ? WHERE id = ?";
            return db_execute($sql, [$title, $slug, $description, $goal_amount, $status, $id], "sssdsi");
        }
    }

    public static function updateRaisedAmount($campaign_id, $amount) {
        $sql = "UPDATE campaigns SET raised_amount = raised_amount + ? WHERE id = ?";
        return db_execute($sql, [$amount, $campaign_id], "di");
    }

    public static function delete($id) {
        return db_execute("DELETE FROM campaigns WHERE id = ?", [$id], "i");
    }

    public static function getStats() {
        $row = db_fetch_one("SELECT COUNT(*) as total_campaigns, SUM(goal_amount) as total_goal, SUM(raised_amount) as total_raised FROM campaigns");
        return [
            'total_campaigns' => $row['total_campaigns'] ?? 0,
            'total_goal' => $row['total_goal'] ?? 0.00,
            'total_raised' => $row['total_raised'] ?? 0.00
        ];
    }
}
