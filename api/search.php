<?php
// api/search.php
require_once __DIR__ . '/../includes/functions.php';

$pdo = get_db_connection();
$query = sanitize($_GET['q'] ?? '');

if (strlen($query) < 2) {
    send_json_response(['success' => true, 'results' => []]);
}

$results = [];

// Search Products
$pStmt = $pdo->prepare("SELECT id, name, price, image_url, purchase_type FROM products WHERE name LIKE ? OR description LIKE ? LIMIT 5");
$pStmt->execute(["%$query%", "%$query%"]);
while ($p = $pStmt->fetch()) {
    $results[] = [
        'id' => $p['id'],
        'title' => $p['name'],
        'price' => number_format((float)$p['price'], 2),
        'image' => $p['image_url'] ?: 'https://via.placeholder.com/80',
        'type_badge' => $p['purchase_type'] === 'REPURCHASE' ? 'Repurchase Grocery' : 'One-time Buy',
        'url' => '/product.php?id=' . $p['id']
    ];
}

// Search Packages
$pkgStmt = $pdo->prepare("SELECT id, name, price, discount_price, image_url FROM packages WHERE name LIKE ? OR description LIKE ? LIMIT 3");
$pkgStmt->execute(["%$query%", "%$query%"]);
while ($pkg = $pkgStmt->fetch()) {
    $effectivePrice = ($pkg['discount_price'] > 0) ? $pkg['discount_price'] : $pkg['price'];
    $results[] = [
        'id' => $pkg['id'],
        'title' => $pkg['name'],
        'price' => number_format((float)$effectivePrice, 2),
        'image' => $pkg['image_url'] ?: 'https://via.placeholder.com/80',
        'type_badge' => 'Package Bundle',
        'url' => '/package.php?id=' . $pkg['id']
    ];
}

send_json_response([
    'success' => true,
    'results' => $results
]);
