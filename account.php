<?php
// account.php
require_once __DIR__ . '/includes/functions.php';

$user = get_logged_in_user();
if (!$user) {
    header('Location: /login.php');
    exit;
}

$pageTitle = 'My Account - Giveoraemart';
$pdo = get_db_connection();

// Fetch customer order history
$stmt = $pdo->prepare("SELECT * FROM orders WHERE user_id = ? ORDER BY id DESC");
$stmt->execute([$user['id']]);
$userOrders = $stmt->fetchAll();

require_once __DIR__ . '/includes/header.php';
?>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">

    <div class="mb-8 border-b border-slate-800 pb-6 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-3xl font-extrabold text-white tracking-tight">Customer Account Dashboard</h1>
            <p class="text-slate-400 text-sm mt-1">Welcome back, <span class="text-emerald-400 font-bold"><?= htmlspecialchars($user['name']) ?></span> (<?= htmlspecialchars($user['email']) ?>)</p>
        </div>
        <a href="/logout.php" class="text-xs bg-slate-900 border border-slate-800 hover:border-rose-500/30 text-slate-300 hover:text-rose-400 px-4 py-2.5 rounded-xl transition w-fit flex items-center gap-2">
            <i class="fa-solid fa-right-from-bracket"></i> Sign Out
        </a>
    </div>

    <div class="bg-slate-900 border border-slate-800 rounded-3xl p-6 md:p-8">
        <h2 class="text-xl font-bold text-white mb-6 flex items-center gap-2">
            <i class="fa-solid fa-receipt text-emerald-400"></i> My Order History (<?= count($userOrders) ?>)
        </h2>

        <?php if (empty($userOrders)): ?>
            <div class="text-center py-12 text-slate-500 text-sm">
                You haven't placed any orders yet.
            </div>
        <?php else: ?>
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm text-slate-300">
                    <thead class="bg-slate-950 text-xs uppercase font-semibold text-slate-400 border-b border-slate-800">
                        <tr>
                            <th class="py-3 px-4">Order ID</th>
                            <th class="py-3 px-4">Date</th>
                            <th class="py-3 px-4">Shipping Address</th>
                            <th class="py-3 px-4">Total Amount</th>
                            <th class="py-3 px-4">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-800">
                        <?php foreach ($userOrders as $ord): ?>
                            <tr class="hover:bg-slate-800/40 transition">
                                <td class="py-4 px-4 font-mono text-xs font-bold text-emerald-400">#ORD-<?= str_pad($ord['id'], 5, '0', STR_PAD_LEFT) ?></td>
                                <td class="py-4 px-4 text-xs text-slate-400"><?= date('M d, Y', strtotime($ord['created_at'])) ?></td>
                                <td class="py-4 px-4 text-xs text-slate-300 max-w-xs truncate"><?= htmlspecialchars($ord['shipping_address']) ?></td>
                                <td class="py-4 px-4 font-bold text-white"><?= format_price($ord['total_amount']) ?></td>
                                <td class="py-4 px-4">
                                    <span class="bg-amber-500/10 text-amber-400 border border-amber-500/20 px-2.5 py-1 rounded-full text-[10px] font-bold">
                                        <?= $ord['status'] ?>
                                    </span>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>

</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
