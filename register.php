<?php
// register.php
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/matrix_engine.php';

$error = '';
$sponsorId = isset($_GET['ref']) ? (int)$_GET['ref'] : null;

$isPaymentVerified = isset($_SESSION['payment_verified']) && $_SESSION['payment_verified'] === true;
$verifiedTxnId = $_SESSION['verified_txn_id'] ?? '';
$prefill = $_SESSION['prefill_registration'] ?? [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = sanitize($_POST['name'] ?? '');
    $email = sanitize($_POST['email'] ?? '');
    $phone = sanitize($_POST['phone'] ?? '');
    $password = $_POST['password'] ?? '';
    $sponsorId = !empty($_POST['sponsor_id']) ? (int)$_POST['sponsor_id'] : $sponsorId;
    $txnId = $isPaymentVerified ? $verifiedTxnId : sanitize($_POST['txn_id'] ?? '');

    if (!empty($name) && !empty($email) && !empty($password)) {
        $pdo = get_db_connection();
        $checkStmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
        $checkStmt->execute([$email]);

        if ($checkStmt->fetch()) {
            $error = 'Email address is already registered.';
        } else {
            $hashedPass = password_hash($password, PASSWORD_BCRYPT);
            $insStmt = $pdo->prepare("INSERT INTO users (name, email, phone, password, role, sponsor_id, txn_id) VALUES (?, ?, ?, ?, 'CUSTOMER', ?, ?)");
            $insStmt->execute([$name, $email, $phone, $hashedPass, $sponsorId ?: null, $txnId ?: null]);

            $newId = $pdo->lastInsertId();

            // 1. Place in 3-Matrix using BFS Auto-Spillover
            place_user_in_3_matrix($newId, $sponsorId);

            // 2. Distribute Instant Registration Level & Matrix Incentives
            distribute_level_commissions($newId);
            distribute_matrix_commissions($newId);

            // If order exists from payment, assign order to newly registered user and trigger order commissions
            if (!empty($_SESSION['verified_order_id'])) {
                $orderId = (int)$_SESSION['verified_order_id'];
                $updateOrder = $pdo->prepare("UPDATE orders SET user_id = ? WHERE id = ?");
                $updateOrder->execute([$newId, $orderId]);

                distribute_level_commissions($newId, $orderId);
                distribute_matrix_commissions($newId, $orderId);
            }

            // Clear payment verification state after registration
            unset($_SESSION['payment_verified'], $_SESSION['verified_txn_id'], $_SESSION['verified_order_id'], $_SESSION['prefill_registration'], $_SESSION['pending_order_id'], $_SESSION['checkout_guest_info']);

            $_SESSION['user'] = [
                'id' => $newId,
                'name' => $name,
                'email' => $email,
                'role' => 'CUSTOMER'
            ];

            header('Location: /account.php');
            exit;
        }
    } else {
        $error = 'Please fill in all required fields.';
    }
}

$pageTitle = 'Customer Registration - Giveoraemart';
require_once __DIR__ . '/includes/header.php';
?>

<div class="max-w-md mx-auto my-16 bg-slate-900 border border-slate-800 rounded-3xl p-8 shadow-2xl">
    <div class="text-center mb-8">
        <h1 class="text-2xl font-bold text-white">Create An Account</h1>
        <?php if ($isPaymentVerified): ?>
            <div class="mt-2 inline-flex items-center gap-2 px-3 py-1 bg-emerald-500/10 border border-emerald-500/30 text-emerald-400 text-xs font-bold rounded-full">
                <i class="fa-solid fa-circle-check"></i> Payment Verified Successfully
            </div>
            <?php if ($verifiedTxnId): ?>
                <p class="text-[11px] text-slate-400 font-mono mt-1">Txn ID: <?= htmlspecialchars($verifiedTxnId) ?></p>
            <?php endif; ?>
        <?php else: ?>
            <p class="text-xs text-slate-400 mt-1">Join Giveoraemart for easy order tracking & express checkout</p>
        <?php endif; ?>
    </div>

    <?php if ($error): ?>
        <div class="mb-6 p-4 bg-rose-500/10 border border-rose-500/30 text-rose-400 text-xs rounded-xl flex items-center gap-3">
            <i class="fa-solid fa-circle-exclamation text-sm"></i>
            <span><?= $error ?></span>
        </div>
    <?php endif; ?>

    <form method="POST" class="space-y-4">
        <div>
            <label class="block text-xs font-semibold uppercase tracking-wider text-slate-400 mb-2">Full Name *</label>
            <input type="text" name="name" value="<?= htmlspecialchars($prefill['name'] ?? '') ?>" required placeholder="John Doe"
                   class="w-full bg-slate-950 border border-slate-800 rounded-xl px-4 py-3 text-white placeholder-slate-500 text-xs focus:outline-none focus:border-emerald-500">
        </div>

        <div>
            <label class="block text-xs font-semibold uppercase tracking-wider text-slate-400 mb-2">Email Address *</label>
            <input type="email" name="email" value="<?= htmlspecialchars($prefill['email'] ?? '') ?>" required placeholder="john@example.com"
                   class="w-full bg-slate-950 border border-slate-800 rounded-xl px-4 py-3 text-white placeholder-slate-500 text-xs focus:outline-none focus:border-emerald-500">
        </div>

        <div>
            <label class="block text-xs font-semibold uppercase tracking-wider text-slate-400 mb-2">Phone Number</label>
            <input type="tel" name="phone" value="<?= htmlspecialchars($prefill['phone'] ?? '') ?>" placeholder="+1 (555) 000-1234"
                   class="w-full bg-slate-950 border border-slate-800 rounded-xl px-4 py-3 text-white placeholder-slate-500 text-xs focus:outline-none focus:border-emerald-500">
        </div>

        <div>
            <label class="block text-xs font-semibold uppercase tracking-wider text-slate-400 mb-2">Sponsor / Referrer ID (Optional)</label>
            <input type="number" name="sponsor_id" value="<?= htmlspecialchars($sponsorId ?? '') ?>" placeholder="e.g. 2"
                   class="w-full bg-slate-950 border border-slate-800 rounded-xl px-4 py-3 text-white placeholder-slate-500 text-xs focus:outline-none focus:border-emerald-500">
        </div>

        <div>
            <label class="block text-xs font-semibold uppercase tracking-wider text-slate-400 mb-2">Password *</label>
            <input type="password" name="password" required placeholder="••••••••"
                   class="w-full bg-slate-950 border border-slate-800 rounded-xl px-4 py-3 text-white placeholder-slate-500 text-xs focus:outline-none focus:border-emerald-500">
        </div>

        <button type="submit" class="w-full bg-emerald-500 hover:bg-emerald-600 text-slate-950 font-bold py-3.5 px-4 rounded-xl transition text-xs shadow-lg shadow-emerald-500/20">
            Create Customer Account
        </button>
    </form>

    <div class="mt-6 text-center text-xs text-slate-400">
        Already have an account? <a href="/login.php" class="text-emerald-400 font-bold hover:underline">Sign In</a>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
