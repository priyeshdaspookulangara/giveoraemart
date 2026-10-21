<?php
// logout.php
require_once __DIR__ . '/includes/functions.php';

unset($_SESSION['user']);
header('Location: /login.php');
exit;
