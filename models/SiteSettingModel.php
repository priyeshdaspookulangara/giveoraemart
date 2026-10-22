<?php
// models/SiteSettingModel.php

require_once __DIR__ . '/../config/db.php';

class SiteSettingModel {

    public static function getAll() {
        $rows = db_execute("SELECT setting_key, setting_value FROM site_settings");
        $settings = [];
        if (is_array($rows)) {
            foreach ($rows as $row) {
                $settings[$row['setting_key']] = $row['setting_value'];
            }
        }
        return $settings;
    }

    public static function get($key, $default = '') {
        $row = db_fetch_one("SELECT setting_value FROM site_settings WHERE setting_key = ?", [$key], "s");
        return $row ? $row['setting_value'] : $default;
    }

    public static function update($key, $value) {
        $sql = "INSERT INTO site_settings (setting_key, setting_value) VALUES (?, ?)
                ON DUPLICATE KEY UPDATE setting_value = VALUES(setting_value)";
        return db_execute($sql, [$key, $value], "ss");
    }

    public static function updateMultiple(array $settings) {
        foreach ($settings as $key => $value) {
            self::update($key, (string)$value);
        }
        return true;
    }
}
