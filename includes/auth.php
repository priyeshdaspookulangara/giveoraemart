<?php
// includes/auth.php - Authentication and CSRF Middleware

if (session_status() === PHP_SESSION_NONE && !headers_sent()) {
    session_start();
}

/**
 * Generates or returns existing CSRF token for session
 */
function generate_csrf_token() {
    if (session_status() === PHP_SESSION_NONE && !headers_sent()) {
        session_start();
    }
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * Verifies submitted CSRF token against session token
 */
function verify_csrf_token($token) {
    if (session_status() === PHP_SESSION_NONE && !headers_sent()) {
        session_start();
    }
    if (empty($_SESSION['csrf_token']) || empty($token)) {
        return false;
    }
    return hash_equals($_SESSION['csrf_token'], $token);
}

/**
 * Returns HTML hidden input for CSRF token
 */
function csrf_input() {
    $token = generate_csrf_token();
    return '<input type="hidden" name="csrf_token" value="' . htmlspecialchars($token, ENT_QUOTES, 'UTF-8') . '">';
}

/**
 * Check if user is logged in
 */
function is_logged_in() {
    if (session_status() === PHP_SESSION_NONE && !headers_sent()) {
        session_start();
    }
    return !empty($_SESSION['user_id']);
}

/**
 * Get current logged in user details array
 */
function current_user() {
    if (!is_logged_in()) {
        return null;
    }
    return [
        'id' => $_SESSION['user_id'],
        'name' => $_SESSION['user_name'] ?? 'User',
        'email' => $_SESSION['user_email'] ?? '',
        'role' => $_SESSION['user_role'] ?? 'donor'
    ];
}

/**
 * Check if logged in user has specific role or one of given roles
 */
function has_role($roles) {
    if (!is_logged_in()) {
        return false;
    }
    $currentRole = $_SESSION['user_role'] ?? '';
    if (is_array($roles)) {
        return in_array($currentRole, $roles, true);
    }
    return $currentRole === $roles;
}

/**
 * Enforce role authorization or redirect to login
 */
function require_role($roles) {
    if (!is_logged_in()) {
        $_SESSION['flash_error'] = "Please log in to access this page.";
        header("Location: /login.php");
        exit;
    }
    if (!has_role($roles)) {
        $_SESSION['flash_error'] = "Access denied. You do not have permission to access this resource.";
        header("Location: /index.php");
        exit;
    }
}
