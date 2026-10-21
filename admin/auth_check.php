<?php
// admin/auth_check.php
require_once __DIR__ . '/../includes/functions.php';

$currentUser = get_logged_in_user();
if (!$currentUser || $currentUser['role'] !== 'ADMIN') {
    header('Location: /admin/login.php');
    exit;
}
