<?php
// account.php
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/matrix_engine.php';

$user = get_logged_in_user();
if (!$user) {
    header('Location: /login.php');
    exit;
}

$pageTitle = 'My Account & Earnings - Giveoraemart';
$pdo = get_db_connection();

// Fetch Earnings Breakdown
$earnings = get_user_earnings($user['id']);

// Fetch Detailed Commissions Ledger
$commStmt = $pdo->prepare("
    SELECT c.*, u.name as from_name, u.email as from_email
    FROM commissions c
    LEFT JOIN users u ON c.from_user_id = u.id
    WHERE c.user_id = ?
    ORDER BY c.id DESC LIMIT 15
");
$commStmt->execute([$user['id']]);
$commissionsList = $commStmt->fetchAll();

// Fetch Direct Referrals
$refStmt = $pdo->prepare("SELECT id, name, email, created_at FROM users WHERE sponsor_id = ? ORDER BY id DESC");
$refStmt->execute([$user['id']]);
$directReferrals = $refStmt->fetchAll();

// Fetch Matrix Children (Directly under this user in 3-matrix)
$matStmt = $pdo->prepare("SELECT id, name, email, matrix_position, created_at FROM users WHERE matrix_parent_id = ? ORDER BY matrix_position ASC");
$matStmt->execute([$user['id']]);
$matrixChildren = $matStmt->fetchAll();

// Fetch Customer Orders
$stmt = $pdo->prepare("SELECT * FROM orders WHERE user_id = ? ORDER BY id DESC");
$stmt->execute([$user['id']]);
$userOrders = $stmt->fetchAll();

require_once __DIR__ . '/includes/header.php';
?>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">

    <div class="mb-8 border-b border-slate-800 pb-6 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-3xl font-extrabold text-white tracking-tight">Customer Dashboard & Earnings</h1>
            <p class="text-slate-400 text-sm mt-1">Welcome back, <span class="text-emerald-400 font-bold"><?= htmlspecialchars($user['name']) ?></span> (User ID: #<?= $user['id'] ?>)</p>
        </div>
        <div class="flex items-center gap-3">
            <a href="/register.php?ref=<?= $user['id'] ?>" target="_blank" class="text-xs bg-emerald-500/10 text-emerald-400 border border-emerald-500/20 font-bold px-4 py-2.5 rounded-xl transition flex items-center gap-2">
                <i class="fa-solid fa-link"></i> Referral Link
            </a>
            <a href="/logout.php" class="text-xs bg-slate-900 border border-slate-800 hover:border-rose-500/30 text-slate-300 hover:text-rose-400 px-4 py-2.5 rounded-xl transition flex items-center gap-2">
                <i class="fa-solid fa-right-from-bracket"></i> Sign Out
            </a>
        </div>
    </div>

    <!-- Earnings Overview Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-6 mb-10">
        <div class="bg-slate-900 border border-slate-800 rounded-3xl p-6">
            <div class="flex items-center justify-between mb-2">
                <span class="text-xs font-bold uppercase tracking-wider text-slate-400">3-Matrix Spillover Income ($50/head)</span>
                <div class="w-8 h-8 rounded-xl bg-purple-500/10 text-purple-400 flex items-center justify-center border border-purple-500/20">
                    <i class="fa-solid fa-diagram-project"></i>
                </div>
            </div>
            <div class="text-3xl font-extrabold text-purple-400"><?= format_price($earnings['matrix_total']) ?></div>
            <p class="text-[11px] text-slate-500 mt-1">Auto BFS 10-level matrix commissions</p>
        </div>

        <div class="bg-slate-900 border border-slate-800 rounded-3xl p-6">
            <div class="flex items-center justify-between mb-2">
                <span class="text-xs font-bold uppercase tracking-wider text-slate-400">7-Level Direct Referral Income</span>
                <div class="w-8 h-8 rounded-xl bg-blue-500/10 text-blue-400 flex items-center justify-center border border-blue-500/20">
                    <i class="fa-solid fa-users"></i>
                </div>
            </div>
            <div class="text-3xl font-extrabold text-blue-400"><?= format_price($earnings['level_total']) ?></div>
            <p class="text-[11px] text-slate-500 mt-1">L1: $500, L2: $250, L3: $200, L4: $150, L5: $100, L6: $50, L7: $50</p>
        </div>

        <div class="bg-slate-900 border border-slate-800 rounded-3xl p-6">
            <div class="flex items-center justify-between mb-2">
                <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Grand Total Earnings</span>
                <div class="w-8 h-8 rounded-xl bg-emerald-500/10 text-emerald-400 flex items-center justify-center border border-emerald-500/20">
                    <i class="fa-solid fa-sack-dollar"></i>
                </div>
            </div>
            <div class="text-3xl font-black text-emerald-400"><?= format_price($earnings['grand_total']) ?></div>
            <p class="text-[11px] text-slate-500 mt-1">Accumulated income total</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-10">
        <!-- 3-Matrix Team Hierarchy -->
        <div class="bg-slate-900 border border-slate-800 rounded-3xl p-6">
            <h2 class="text-lg font-bold text-white mb-4 flex items-center gap-2">
                <i class="fa-solid fa-sitemap text-purple-400"></i> My 3-Matrix Direct Nodes
            </h2>
            <div class="grid grid-cols-3 gap-3">
                <?php for ($pos = 1; $pos <= 3; $pos++): ?>
                    <?php
                    $childAtPos = null;
                    foreach ($matrixChildren as $mc) {
                        if ($mc['matrix_position'] == $pos) {
                            $childAtPos = $mc;
                            break;
                        }
                    }
                    ?>
                    <div class="bg-slate-950 border border-slate-800 rounded-2xl p-4 text-center">
                        <span class="text-[10px] font-bold uppercase text-slate-500 block mb-2">Position <?= $pos ?></span>
                        <?php if ($childAtPos): ?>
                            <div class="w-10 h-10 rounded-full bg-purple-500/20 text-purple-300 font-bold flex items-center justify-center mx-auto mb-2 text-xs border border-purple-500/30">
                                <?= strtoupper(substr($childAtPos['name'], 0, 1)) ?>
                            </div>
                            <span class="text-xs font-bold text-white block truncate"><?= htmlspecialchars($childAtPos['name']) ?></span>
                            <span class="text-[10px] text-slate-500 font-mono block">ID: #<?= $childAtPos['id'] ?></span>
                        <?php else: ?>
                            <div class="w-10 h-10 rounded-full bg-slate-900 text-slate-600 font-bold flex items-center justify-center mx-auto mb-2 text-xs border border-slate-800">
                                <i class="fa-solid fa-user-plus"></i>
                            </div>
                            <span class="text-xs text-slate-600 font-medium block">Empty Slot</span>
                        <?php endif; ?>
                    </div>
                <?php endfor; ?>
            </div>
        </div>

        <!-- Direct Referrals List -->
        <div class="bg-slate-900 border border-slate-800 rounded-3xl p-6">
            <h2 class="text-lg font-bold text-white mb-4 flex items-center gap-2">
                <i class="fa-solid fa-user-group text-blue-400"></i> Direct Referrals (<?= count($directReferrals) ?>)
            </h2>
            <?php if (empty($directReferrals)): ?>
                <div class="text-center py-8 text-slate-500 text-xs">No direct referrals yet. Share your referral link!</div>
            <?php else: ?>
                <div class="space-y-2 max-h-48 overflow-y-auto">
                    <?php foreach ($directReferrals as $ref): ?>
                        <div class="flex items-center justify-between bg-slate-950 p-3 rounded-xl border border-slate-800 text-xs">
                            <div>
                                <span class="font-bold text-white block"><?= htmlspecialchars($ref['name']) ?></span>
                                <span class="text-slate-500"><?= htmlspecialchars($ref['email']) ?></span>
                            </div>
                            <span class="font-mono text-slate-400">ID: #<?= $ref['id'] ?></span>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Recent Commissions Ledger -->
    <div class="bg-slate-900 border border-slate-800 rounded-3xl p-6 mb-10">
        <h2 class="text-lg font-bold text-white mb-4 flex items-center gap-2">
            <i class="fa-solid fa-money-bill-wave text-emerald-400"></i> Recent Commission Income Ledger
        </h2>
        <?php if (empty($commissionsList)): ?>
            <div class="text-center py-8 text-slate-500 text-xs">No commissions received yet.</div>
        <?php else: ?>
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs text-slate-300">
                    <thead class="bg-slate-950 uppercase font-semibold text-slate-400 border-b border-slate-800">
                        <tr>
                            <th class="py-3 px-4">Date</th>
                            <th class="py-3 px-4">Income Type</th>
                            <th class="py-3 px-4">From Member</th>
                            <th class="py-3 px-4">Tree Level</th>
                            <th class="py-3 px-4 text-right">Amount Credit</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-800">
                        <?php foreach ($commissionsList as $c): ?>
                            <tr class="hover:bg-slate-800/40 transition">
                                <td class="py-3 px-4 text-slate-400"><?= date('M d, Y H:i', strtotime($c['created_at'])) ?></td>
                                <td class="py-3 px-4">
                                    <?php if ($c['income_type'] === 'MATRIX'): ?>
                                        <span class="bg-purple-500/10 text-purple-400 border border-purple-500/20 px-2 py-0.5 rounded font-bold">3-Matrix Spillover</span>
                                    <?php else: ?>
                                        <span class="bg-blue-500/10 text-blue-400 border border-blue-500/20 px-2 py-0.5 rounded font-bold">Level Referral</span>
                                    <?php endif; ?>
                                </td>
                                <td class="py-3 px-4 font-semibold text-white"><?= htmlspecialchars($c['from_name'] ?? 'User #' . $c['from_user_id']) ?></td>
                                <td class="py-3 px-4 font-bold text-slate-300">Level <?= $c['level'] ?></td>
                                <td class="py-3 px-4 text-right font-black text-emerald-400">+<?= format_price($c['amount']) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>

    <!-- Orders Section -->
    <div class="bg-slate-900 border border-slate-800 rounded-3xl p-6">
        <h2 class="text-lg font-bold text-white mb-6 flex items-center gap-2">
            <i class="fa-solid fa-receipt text-emerald-400"></i> My Order History (<?= count($userOrders) ?>)
        </h2>

        <?php if (empty($userOrders)): ?>
            <div class="text-center py-12 text-slate-500 text-sm">You haven't placed any orders yet.</div>
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
