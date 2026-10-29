<?php
// payment.php
require_once __DIR__ . '/includes/functions.php';

$pageTitle = 'UPI Payment - Giveoraemart';
$error = '';
$sessionPendingOrderId = (int)($_SESSION['pending_order_id'] ?? 0);
$urlOrderId = isset($_GET['order_id']) ? (int)$_GET['order_id'] : 0;

// Security check: Order ID must match pending session order or belong to logged in user
$orderId = ($sessionPendingOrderId > 0) ? $sessionPendingOrderId : $urlOrderId;

$order = null;
if ($orderId > 0) {
    $pdo = get_db_connection();
    $currentUser = get_logged_in_user();

    if ($currentUser) {
        $stmt = $pdo->prepare("SELECT * FROM orders WHERE id = ? AND (user_id = ? OR id = ?)");
        $stmt->execute([$orderId, $currentUser['id'], $sessionPendingOrderId]);
    } else {
        $stmt = $pdo->prepare("SELECT * FROM orders WHERE id = ?");
        $stmt->execute([$sessionPendingOrderId]);
    }
    $order = $stmt->fetch();
}

$amount = $order ? $order['total_amount'] : 0.00;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $txnId = trim($_POST['txn_id'] ?? '');

    if (empty($txnId)) {
        $error = 'Please enter a valid UPI Transaction Reference ID (UTR / Txn ID).';
    } else {
        $pdo = get_db_connection();

        // If order exists, update status to PAID and attach txn_id
        if ($order) {
            $updateStmt = $pdo->prepare("UPDATE orders SET status = 'PAID', txn_id = ? WHERE id = ?");
            $updateStmt->execute([$txnId, $orderId]);

            if ($order['user_id']) {
                require_once __DIR__ . '/includes/matrix_engine.php';
                distribute_level_commissions($order['user_id'], $orderId);
                distribute_matrix_commissions($order['user_id'], $orderId);
            }
        }

        // Set verified payment state in session
        $_SESSION['payment_verified'] = true;
        $_SESSION['verified_txn_id'] = $txnId;
        $_SESSION['verified_order_id'] = $orderId;

        // If user is not logged in, pass guest checkout details to pre-fill registration
        if (!is_logged_in() && !empty($_SESSION['checkout_guest_info'])) {
            $_SESSION['prefill_registration'] = $_SESSION['checkout_guest_info'];
        }

        // Redirect to registration page after successful payment
        header("Location: register.php");
        exit;
    }
}

require_once __DIR__ . '/includes/header.php';
?>

<div class="max-w-md mx-auto my-12 bg-slate-900 border border-slate-800 rounded-3xl p-8 shadow-2xl">
    <div class="text-center mb-6">
        <span class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-xs font-bold bg-emerald-500/10 text-emerald-400 border border-emerald-500/20 mb-3">
            <i class="fa-solid fa-qrcode"></i> UPI Instant Gateway
        </span>
        <h1 class="text-2xl font-black text-white">Complete UPI Payment</h1>
        <p class="text-xs text-slate-400 mt-1">Scan QR or enter UPI ID to complete payment of <span class="text-emerald-400 font-extrabold text-sm"><?= format_price($amount) ?></span></p>
    </div>

    <?php if (!empty($error)): ?>
        <div class="mb-6 p-4 bg-rose-500/10 border border-rose-500/30 text-rose-400 text-xs rounded-xl flex items-center gap-3">
            <i class="fa-solid fa-circle-exclamation text-sm"></i>
            <span><?= htmlspecialchars($error) ?></span>
        </div>
    <?php endif; ?>

    <!-- QR Code & VPA Display Box -->
    <div class="bg-slate-950 border border-slate-800 rounded-2xl p-6 text-center mb-6 space-y-4">
        <div class="inline-block p-3 bg-white rounded-2xl shadow-xl">
            <!-- Simulated UPI QR Code image -->
            <img src="https://api.qrserver.com/v1/create-qr-code/?size=180x180&data=upi://pay?pa=giveorae@upi%26pn=Giveoraemart%26am=<?= $amount ?>" alt="UPI QR Code" class="w-44 h-44 mx-auto rounded-lg">
        </div>

        <div>
            <div class="text-[11px] uppercase tracking-wider text-slate-500 font-bold">UPI ID / VPA</div>
            <div class="text-emerald-400 font-mono font-bold text-sm select-all">giveorae@upi</div>
        </div>

        <div class="text-slate-400 text-xs flex justify-center items-center gap-3 pt-2 border-t border-slate-900">
            <span class="font-bold text-white">Accepted Apps:</span>
            <span class="text-slate-300"><i class="fa-brands fa-google-pay text-base"></i> GPay</span>
            <span class="text-slate-300"><i class="fa-solid fa-mobile-screen text-base"></i> PhonePe</span>
            <span class="text-slate-300"><i class="fa-solid fa-wallet text-base"></i> Paytm</span>
        </div>
    </div>

    <!-- Payment Verification Form -->
    <form method="POST" class="space-y-4">
        <div>
            <label class="block text-xs font-semibold uppercase tracking-wider text-slate-400 mb-2">UPI Transaction / UTR ID *</label>
            <input type="text" name="txn_id" required placeholder="12-digit UTR e.g. 302910492019"
                   class="w-full bg-slate-950 border border-slate-800 rounded-xl px-4 py-3 text-white placeholder-slate-500 text-xs focus:outline-none focus:border-emerald-500 font-mono">
            <p class="text-[11px] text-slate-500 mt-1">Enter reference number from your UPI payment receipt.</p>
        </div>

        <button type="submit" class="w-full bg-emerald-500 hover:bg-emerald-600 text-slate-950 font-black py-4 px-4 rounded-xl transition text-xs shadow-lg shadow-emerald-500/20 flex items-center justify-center gap-2">
            <i class="fa-solid fa-shield-check"></i>
            <span>Verify Payment & Proceed to Registration</span>
        </button>
    </form>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
