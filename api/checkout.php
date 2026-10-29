<?php
// api/checkout.php
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/matrix_engine.php';

$cartDetails = get_cart_details();

if (empty($cartDetails['items'])) {
    header('Location: /cart.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $guestName = sanitize($_POST['guest_name'] ?? '');
    $guestEmail = sanitize($_POST['guest_email'] ?? '');
    $guestPhone = sanitize($_POST['guest_phone'] ?? '');
    $shippingAddress = sanitize($_POST['shipping_address'] ?? '');
    $paymentMethod = sanitize($_POST['payment_method'] ?? 'COD');

    if (empty($guestName) || empty($guestEmail) || empty($shippingAddress)) {
        die("Please fill in all required fields.");
    }

    if (!in_array($paymentMethod, ['COD', 'UPI'])) {
        $paymentMethod = 'COD';
    }

    $currentUser = get_logged_in_user();
    $userId = $currentUser ? $currentUser['id'] : null;

    $shippingFee = $cartDetails['total'] >= 50 ? 0.00 : 4.99;
    $grandTotal = $cartDetails['total'] + $shippingFee;

    $pdo = get_db_connection();
    $pdo->beginTransaction();

    try {
        // Insert Order Record
        $orderStmt = $pdo->prepare("INSERT INTO orders (user_id, guest_name, guest_email, guest_phone, shipping_address, total_amount, payment_method, status) VALUES (?, ?, ?, ?, ?, ?, ?, 'PENDING')");
        $orderStmt->execute([
            $userId,
            $guestName,
            $guestEmail,
            $guestPhone,
            $shippingAddress,
            $grandTotal,
            $paymentMethod
        ]);

        $orderId = $pdo->lastInsertId();

        // Insert Order Items & Deduct Stock
        $itemStmt = $pdo->prepare("INSERT INTO order_items (order_id, item_type, item_id, quantity, price) VALUES (?, ?, ?, ?, ?)");
        $stockStmt = $pdo->prepare("UPDATE products SET stock = GREATEST(0, stock - ?) WHERE id = ?");

        foreach ($cartDetails['items'] as $item) {
            $itemStmt->execute([
                $orderId,
                $item['type'],
                $item['id'],
                $item['quantity'],
                $item['price']
            ]);

            // If item is a single product, deduct inventory stock
            if ($item['type'] === 'PRODUCT') {
                $stockStmt->execute([$item['quantity'], $item['id']]);
            }
        }

        $pdo->commit();

        // Clear session cart
        $_SESSION['cart'] = [];

        // If payment method is UPI, store pending checkout order info and redirect to payment page
        if ($paymentMethod === 'UPI') {
            $_SESSION['pending_order_id'] = $orderId;
            $_SESSION['checkout_guest_info'] = [
                'name' => $guestName,
                'email' => $guestEmail,
                'phone' => $guestPhone
            ];
            header("Location: ../payment.php?order_id=" . $orderId);
            exit;
        }

        // If registered user, trigger matrix & level referral payouts for order
        if ($userId) {
            distribute_level_commissions($userId, $orderId);
            distribute_matrix_commissions($userId, $orderId);
        }

        header("Location: ../order-success.php?id=" . $orderId);
        exit;

    } catch (Exception $e) {
        $pdo->rollBack();
        die("Failed to process order: " . $e->getMessage());
    }
}

header('Location: /checkout.php');
exit;
