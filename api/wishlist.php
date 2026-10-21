<?php
// api/wishlist.php
require_once __DIR__ . '/../includes/functions.php';

$pdo = get_db_connection();
$user = get_logged_in_user();
$guestSession = get_guest_session_id();

if ($_SERVER['REQUEST_METHOD'] === 'GET' && ($_GET['action'] ?? '') === 'count') {
    if ($user) {
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM wishlists WHERE user_id = ?");
        $stmt->execute([$user['id']]);
    } else {
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM wishlists WHERE session_id = ? AND user_id IS NULL");
        $stmt->execute([$guestSession]);
    }
    $count = (int)$stmt->fetchColumn();
    send_json_response(['success' => true, 'count' => $count]);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $type = in_array($_POST['type'] ?? '', ['PRODUCT', 'PACKAGE']) ? $_POST['type'] : 'PRODUCT';
    $id = (int)($_POST['id'] ?? 0);

    if ($id <= 0) {
        send_json_response(['success' => false, 'message' => 'Invalid item.'], 400);
    }

    // Check if item already in wishlist
    if ($user) {
        $checkStmt = $pdo->prepare("SELECT id FROM wishlists WHERE user_id = ? AND item_type = ? AND item_id = ?");
        $checkStmt->execute([$user['id'], $type, $id]);
    } else {
        $checkStmt = $pdo->prepare("SELECT id FROM wishlists WHERE session_id = ? AND user_id IS NULL AND item_type = ? AND item_id = ?");
        $checkStmt->execute([$guestSession, $type, $id]);
    }

    $existing = $checkStmt->fetch();

    if ($existing) {
        // Remove from wishlist
        $delStmt = $pdo->prepare("DELETE FROM wishlists WHERE id = ?");
        $delStmt->execute([$existing['id']]);
        $liked = false;
        $msg = 'Item removed from wishlist.';
    } else {
        // Insert into wishlist
        if ($user) {
            $insStmt = $pdo->prepare("INSERT INTO wishlists (user_id, item_type, item_id) VALUES (?, ?, ?)");
            $insStmt->execute([$user['id'], $type, $id]);
        } else {
            $insStmt = $pdo->prepare("INSERT INTO wishlists (session_id, item_type, item_id) VALUES (?, ?, ?)");
            $insStmt->execute([$guestSession, $type, $id]);
        }
        $liked = true;
        $msg = 'Item saved to wishlist!';
    }

    // Recalculate total wishlist count
    if ($user) {
        $countStmt = $pdo->prepare("SELECT COUNT(*) FROM wishlists WHERE user_id = ?");
        $countStmt->execute([$user['id']]);
    } else {
        $countStmt = $pdo->prepare("SELECT COUNT(*) FROM wishlists WHERE session_id = ? AND user_id IS NULL");
        $countStmt->execute([$guestSession]);
    }
    $totalCount = (int)$countStmt->fetchColumn();

    send_json_response([
        'success' => true,
        'liked' => $liked,
        'message' => $msg,
        'wishlist_count' => $totalCount
    ]);
}

send_json_response(['success' => false, 'message' => 'Invalid request.'], 400);
