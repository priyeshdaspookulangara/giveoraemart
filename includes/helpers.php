<?php
// includes/helpers.php - General Helper Functions

require_once __DIR__ . '/../config/db.php';

/**
 * Sanitize user input for display or safe string usage
 */
function sanitize_input($data) {
    if (is_array($data)) {
        return array_map('sanitize_input', $data);
    }
    return htmlspecialchars(trim((string)$data), ENT_QUOTES, 'UTF-8');
}

/**
 * Generate a URL-friendly slug from string
 */
function slugify($text) {
    $text = preg_replace('~[^\pL\d]+~u', '-', $text);
    $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);
    $text = preg_replace('~[^-\w]+~', '', $text);
    $text = trim($text, '-');
    $text = preg_replace('~-+~', '-', $text);
    $text = strtolower($text);
    return empty($text) ? 'n-a' : $text;
}

/**
 * Get site setting value from database with fallback
 */
function get_site_setting($key, $default = '') {
    static $settingsCache = null;
    if ($settingsCache === null) {
        $settingsCache = [];
        try {
            $rows = db_execute("SELECT setting_key, setting_value FROM site_settings");
            if (is_array($rows)) {
                foreach ($rows as $row) {
                    $settingsCache[$row['setting_key']] = $row['setting_value'];
                }
            }
        } catch (Exception $e) {
            // DB might not be initialized yet
        }
    }
    return $settingsCache[$key] ?? $default;
}

/**
 * Format currency amount
 */
function format_currency($amount) {
    $symbol = get_site_setting('currency_symbol', '$');
    return $symbol . number_format((float)$amount, 2);
}

/**
 * Calculate progress percentage
 */
function calculate_percentage($raised, $goal) {
    if ($goal <= 0) return 0;
    $pct = ($raised / $goal) * 100;
    return min(100, round($pct, 1));
}

/**
 * Flash message helper - set message
 */
function set_flash_message($type, $message) {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    $_SESSION['flash_' . $type] = $message;
}

/**
 * Flash message helper - display and clear
 */
function display_flash_messages() {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    $types = ['success' => 'success', 'error' => 'danger', 'info' => 'info', 'warning' => 'warning'];
    $html = '';
    foreach ($types as $key => $bsClass) {
        if (!empty($_SESSION['flash_' . $key])) {
            $msg = htmlspecialchars($_SESSION['flash_' . $key], ENT_QUOTES, 'UTF-8');
            $html .= "<div class='alert alert-{$bsClass} alert-dismissible fade show' role='alert'>
                {$msg}
                <button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>
            </div>";
            unset($_SESSION['flash_' . $key]);
        }
    }
    return $html;
}
