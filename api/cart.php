<?php
// api/cart.php
require_once __DIR__ . '/../includes/functions.php';

init_cart();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $type = $_POST['type'] ?? 'PRODUCT'; // 'PRODUCT' or 'PACKAGE'
    $id = (int)($_POST['id'] ?? 0);
    $quantity = max(1, (int)($_POST['quantity'] ?? 1));

    $cartKey = strtolower($type) . '_' . $id;

    if ($action === 'add') {
        if (isset($_SESSION['cart'][$cartKey])) {
            $_SESSION['cart'][$cartKey]['quantity'] += $quantity;
        } else {
            $_SESSION['cart'][$cartKey] = [
                'type' => $type,
                'id' => $id,
                'quantity' => $quantity
            ];
        }

        send_json_response([
            'success' => true,
            'message' => 'Item added to shopping cart!',
            'cart_count' => get_cart_count(),
            'cart_details' => get_cart_details()
        ]);
    } elseif ($action === 'update') {
        if (isset($_SESSION['cart'][$cartKey])) {
            $_SESSION['cart'][$cartKey]['quantity'] = $quantity;
        }

        send_json_response([
            'success' => true,
            'message' => 'Cart updated.',
            'cart_count' => get_cart_count(),
            'cart_details' => get_cart_details()
        ]);
    } elseif ($action === 'remove') {
        if (isset($_SESSION['cart'][$cartKey])) {
            unset($_SESSION['cart'][$cartKey]);
        }

        send_json_response([
            'success' => true,
            'message' => 'Item removed from cart.',
            'cart_count' => get_cart_count(),
            'cart_details' => get_cart_details()
        ]);
    } elseif ($action === 'clear') {
        $_SESSION['cart'] = [];
        send_json_response([
            'success' => true,
            'message' => 'Cart cleared.',
            'cart_count' => 0,
            'cart_details' => get_cart_details()
        ]);
    }
}

send_json_response(['success' => false, 'message' => 'Invalid action.'], 400);
