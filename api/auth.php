<?php
// api/auth.php
header('Content-Type: application/json');
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/matrix_engine.php';

$action = $_POST['action'] ?? $_GET['action'] ?? '';

if ($action === 'login') {
    $email = sanitize($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if (empty($email) || empty($password)) {
        echo json_encode(['success' => false, 'error' => 'Please fill in all fields.']);
        exit;
    }

    $pdo = get_db_connection();
    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user'] = [
            'id' => $user['id'],
            'name' => $user['name'],
            'email' => $user['email'],
            'role' => $user['role']
        ];

        $redirect = ($user['role'] === 'ADMIN') ? '/admin/index.php' : '/account.php';
        echo json_encode(['success' => true, 'redirect' => $redirect]);
        exit;
    } else {
        echo json_encode(['success' => false, 'error' => 'Invalid email or password.']);
        exit;
    }
} elseif ($action === 'register') {
    $name = sanitize($_POST['name'] ?? '');
    $email = sanitize($_POST['email'] ?? '');
    $phone = sanitize($_POST['phone'] ?? '');
    $password = $_POST['password'] ?? '';
    $sponsorId = !empty($_POST['sponsor_id']) ? (int)$_POST['sponsor_id'] : null;

    if (empty($name) || empty($email) || empty($password)) {
        echo json_encode(['success' => false, 'error' => 'Please fill in all required fields.']);
        exit;
    }

    $pdo = get_db_connection();
    $checkStmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
    $checkStmt->execute([$email]);

    if ($checkStmt->fetch()) {
        echo json_encode(['success' => false, 'error' => 'Email address is already registered.']);
        exit;
    }

    $hashedPass = password_hash($password, PASSWORD_BCRYPT);
    $insStmt = $pdo->prepare("INSERT INTO users (name, email, phone, password, role, sponsor_id) VALUES (?, ?, ?, ?, 'CUSTOMER', ?)");
    $insStmt->execute([$name, $email, $phone, $hashedPass, $sponsorId ?: null]);

    $newId = $pdo->lastInsertId();

    // Matrix placement & commission processing
    place_user_in_3_matrix($newId, $sponsorId);
    distribute_level_commissions($newId);
    distribute_matrix_commissions($newId);

    $_SESSION['user'] = [
        'id' => $newId,
        'name' => $name,
        'email' => $email,
        'role' => 'CUSTOMER'
    ];

    echo json_encode(['success' => true, 'redirect' => '/account.php']);
    exit;
} else {
    echo json_encode(['success' => false, 'error' => 'Invalid request action.']);
    exit;
}
