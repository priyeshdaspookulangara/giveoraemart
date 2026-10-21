<?php
// includes/functions.php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../config/database.php';

// Helper to sanitize text input
function sanitize($input) {
    return htmlspecialchars(trim($input ?? ''), ENT_QUOTES, 'UTF-8');
}

// Helper to generate URL slugs
function create_slug($text) {
    $text = preg_replace('~[^\pL\d]+~u', '-', $text);
    $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);
    $text = preg_replace('~[^-\w]+~', '', $text);
    $text = trim($text, '-');
    $text = preg_replace('~-+~', '-', $text);
    $text = strtolower($text);
    return empty($text) ? 'n-a-' . time() : $text;
}

// Get guest or user wishlist identifier
function get_guest_session_id() {
    if (!isset($_SESSION['guest_session_id'])) {
        $_SESSION['guest_session_id'] = session_id() ?: bin2hex(random_bytes(16));
    }
    return $_SESSION['guest_session_id'];
}

// Get current logged in user
function get_logged_in_user() {
    if (isset($_SESSION['user'])) {
        return $_SESSION['user'];
    }
    return null;
}

// Auth check for admin
function require_admin() {
    $user = get_logged_in_user();
    if (!$user || $user['role'] !== 'ADMIN') {
        header('Location: /admin/login.php');
        exit;
    }
}

// Format price currency
function format_price($amount) {
    return '$' . number_format((float)$amount, 2);
}

// JSON Response helper for API routes
function send_json_response($data, $statusCode = 200) {
    header('Content-Type: application/json');
    http_response_code($statusCode);
    echo json_encode($data);
    exit;
}

// Handle image uploads
function handle_image_upload($fileKey, $targetDir = 'uploads/') {
    if (!isset($_FILES[$fileKey]) || $_FILES[$fileKey]['error'] !== UPLOAD_ERR_OK) {
        return null;
    }

    $file = $_FILES[$fileKey];
    $allowedExts = ['jpg', 'jpeg', 'png', 'webp', 'gif'];
    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

    if (!in_array($ext, $allowedExts)) {
        return null;
    }

    $uploadDir = __DIR__ . '/../' . $targetDir;
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }

    $filename = uniqid('img_', true) . '.' . $ext;
    $targetPath = $uploadDir . $filename;

    if (move_uploaded_file($file['tmp_name'], $targetPath)) {
        return '/' . $targetDir . $filename;
    }

    return null;
}

// Initialize Cart Session if not set
function init_cart() {
    if (!isset($_SESSION['cart']) || !is_array($_SESSION['cart'])) {
        $_SESSION['cart'] = [];
    }
}

// Get Total Items Count in Cart
function get_cart_count() {
    init_cart();
    $count = 0;
    foreach ($_SESSION['cart'] as $item) {
        $count += (int)($item['quantity'] ?? 1);
    }
    return $count;
}

// Get Cart items with calculated totals
function get_cart_details() {
    init_cart();
    $pdo = get_db_connection();
    $items = [];
    $totalAmount = 0.00;

    foreach ($_SESSION['cart'] as $key => $cartItem) {
        $type = $cartItem['type']; // 'PRODUCT' or 'PACKAGE'
        $id = $cartItem['id'];
        $qty = max(1, (int)$cartItem['quantity']);

        if ($type === 'PRODUCT') {
            $stmt = $pdo->prepare("SELECT id, name, price, image_url, stock, purchase_type FROM products WHERE id = ?");
            $stmt->execute([$id]);
            $product = $stmt->fetch();
            if ($product) {
                $subtotal = $product['price'] * $qty;
                $totalAmount += $subtotal;
                $items[] = [
                    'cart_key' => $key,
                    'type' => 'PRODUCT',
                    'id' => $product['id'],
                    'name' => $product['name'],
                    'price' => (float)$product['price'],
                    'image_url' => $product['image_url'],
                    'quantity' => $qty,
                    'subtotal' => $subtotal,
                    'purchase_type' => $product['purchase_type'],
                    'stock' => $product['stock']
                ];
            }
        } elseif ($type === 'PACKAGE') {
            $stmt = $pdo->prepare("SELECT id, name, price, discount_price, image_url FROM packages WHERE id = ?");
            $stmt->execute([$id]);
            $pkg = $stmt->fetch();
            if ($pkg) {
                $effectivePrice = ($pkg['discount_price'] > 0) ? $pkg['discount_price'] : $pkg['price'];
                $subtotal = $effectivePrice * $qty;
                $totalAmount += $subtotal;
                $items[] = [
                    'cart_key' => $key,
                    'type' => 'PACKAGE',
                    'id' => $pkg['id'],
                    'name' => $pkg['name'],
                    'price' => (float)$effectivePrice,
                    'original_price' => (float)$pkg['price'],
                    'image_url' => $pkg['image_url'],
                    'quantity' => $qty,
                    'subtotal' => $subtotal,
                    'purchase_type' => 'BUNDLE'
                ];
            }
        }
    }

    return [
        'items' => $items,
        'total' => $totalAmount,
        'count' => get_cart_count()
    ];
}
