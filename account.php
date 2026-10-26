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

// Fetch Earnings & Qualification Status
$earnings = get_user_earnings($user['id']);
$directReferralCount = get_direct_referral_count($user['id']);
$isQualified = is_matrix_qualified($user['id']);

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

    <!-- Wallet Balance Summary Header Card -->
    <div class="bg-gradient-to-r from-slate-900 to-slate-950 border border-slate-800 rounded-3xl p-6 md:p-8 mb-8 shadow-2xl flex flex-col md:flex-row items-center justify-between gap-6">
        <div>
            <span class="text-xs font-bold uppercase tracking-widest text-emerald-400 block mb-1"><i class="fa-solid fa-wallet mr-1.5"></i> Wallet Withdrawal Balance</span>
            <div class="text-4xl font-black text-white"><?= format_price($earnings['withdrawable_balance']) ?></div>
            <p class="text-xs text-slate-400 mt-1">Available for instant payout withdrawal.</p>
        </div>

        <div class="flex items-center gap-4 bg-slate-950/80 p-4 rounded-2xl border border-slate-800 text-xs">
            <div>
                <span class="text-slate-500 block font-semibold text-[10px] uppercase">Reserved Matrix Vault:</span>
                <span class="text-amber-400 font-extrabold text-base"><?= number_format($earnings['reserved_matrix_balance'], 2) ?> USDT</span>
            </div>
            <?php if ($earnings['is_matrix_unlocked']): ?>
                <span class="bg-emerald-500/10 text-emerald-400 border border-emerald-500/20 px-3 py-1.5 rounded-xl text-xs font-bold flex items-center gap-1">
                    <i class="fa-solid fa-lock-open text-xs"></i> Unlocked
                </span>
            <?php else: ?>
                <span class="bg-amber-500/10 text-amber-400 border border-amber-500/20 px-3 py-1.5 rounded-xl text-xs font-bold flex items-center gap-1">
                    <i class="fa-solid fa-lock text-xs"></i> Locked (3 Referrals Needed)
                </span>
            <?php endif; ?>
        </div>
    </div>

    <!-- Earnings Overview Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-6 mb-10">
        <div class="bg-slate-900 border border-slate-800 rounded-3xl p-6">
            <div class="flex items-center justify-between mb-2">
                <span class="text-xs font-bold uppercase tracking-wider text-slate-400">3-Matrix Spillover Income</span>
                <?php if ($isQualified): ?>
                    <span class="bg-emerald-500/10 text-emerald-400 border border-emerald-500/20 px-2 py-0.5 rounded-full text-[10px] font-bold">WITHDRAWABLE</span>
                <?php else: ?>
                    <span class="bg-amber-500/10 text-amber-400 border border-amber-500/20 px-2 py-0.5 rounded-full text-[10px] font-bold">RESERVED VAULT</span>
                <?php endif; ?>
            </div>
            <div class="text-3xl font-extrabold text-purple-400"><?= number_format($earnings['matrix_total'], 2) ?> USDT</div>
            <p class="text-[11px] text-slate-500 mt-1">
                <?= $isQualified ? 'Unlocked & Withdrawable (3+ direct referrals complete)' : 'Earned matrix income is stored in Reserved Vault until 3 direct referrals milestone is reached.' ?>
            </p>
        </div>

        <div class="bg-slate-900 border border-slate-800 rounded-3xl p-6">
            <div class="flex items-center justify-between mb-2">
                <span class="text-xs font-bold uppercase tracking-wider text-slate-400">7-Level Direct Referral Income</span>
                <div class="w-8 h-8 rounded-xl bg-blue-500/10 text-blue-400 flex items-center justify-center border border-blue-500/20">
                    <i class="fa-solid fa-users"></i>
                </div>
            </div>
            <div class="text-3xl font-extrabold text-blue-400"><?= format_price($earnings['level_total']) ?></div>
            <p class="text-[11px] text-slate-500 mt-1">Directly withdrawable referral bonuses (L1: $500, L2: $200...)</p>
        </div>

        <div class="bg-slate-900 border border-slate-800 rounded-3xl p-6">
            <div class="flex items-center justify-between mb-2">
                <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Grand Total Earnings</span>
                <div class="w-8 h-8 rounded-xl bg-emerald-500/10 text-emerald-400 flex items-center justify-center border border-emerald-500/20">
                    <i class="fa-solid fa-sack-dollar"></i>
                </div>
            </div>
            <div class="text-3xl font-black text-emerald-400"><?= format_price($earnings['grand_total']) ?></div>
            <p class="text-[11px] text-slate-500 mt-1">Accumulated income total (Level + Matrix)</p>
        </div>
    </div>

    <!-- Visual 3-Matrix Interactive Tree -->
    <div class="bg-slate-900 border border-slate-800 rounded-3xl p-6 mb-10 overflow-x-auto">
        <div class="flex items-center justify-between border-b border-slate-800 pb-4 mb-6">
            <div>
                <h2 class="text-lg font-bold text-white flex items-center gap-2">
                    <i class="fa-solid fa-sitemap text-purple-400"></i> My 3-Matrix Visual Downline Tree
                </h2>
                <p class="text-xs text-slate-400 mt-0.5">Explore auto-spillover placement downline structure (BFS Level Placement)</p>
            </div>
            <span class="text-xs bg-purple-500/10 text-purple-300 border border-purple-500/20 font-bold px-3 py-1 rounded-full">
                Root: <?= htmlspecialchars($user['name']) ?>
            </span>
        </div>

        <div class="w-max mx-auto px-12 py-4">
            <?php
            $userMatrixTree = get_matrix_tree_data($user['id'], 3);
            echo render_matrix_tree_html($userMatrixTree, 1, 3);
            ?>
        </div>
    </div>

    <!-- Direct Referrals List -->
    <div class="bg-slate-900 border border-slate-800 rounded-3xl p-6 mb-10">
        <h2 class="text-lg font-bold text-white mb-4 flex items-center gap-2">
            <i class="fa-solid fa-user-group text-blue-400"></i> Direct Referrals (<?= count($directReferrals) ?>)
        </h2>
        <?php if (empty($directReferrals)): ?>
            <div class="text-center py-8 text-slate-500 text-xs">No direct referrals yet. Share your referral link!</div>
        <?php else: ?>
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-3">
                <?php foreach ($directReferrals as $ref): ?>
                    <div class="flex items-center justify-between bg-slate-950 p-3 rounded-xl border border-slate-800 text-xs">
                        <div>
                            <span class="font-bold text-white block truncate"><?= htmlspecialchars($ref['name']) ?></span>
                            <span class="text-slate-500 text-[10px] truncate block"><?= htmlspecialchars($ref['email']) ?></span>
                        </div>
                        <span class="font-mono text-slate-400 text-[10px] bg-slate-900 px-2 py-1 rounded">ID: #<?= $ref['id'] ?></span>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
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
