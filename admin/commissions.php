<?php
// admin/commissions.php
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/auth_check.php';

$pageTitle = 'Matrix & Referral Commissions';
$activeTab = 'commissions';
$pdo = get_db_connection();

$totalMatrixPayout = $pdo->query("SELECT SUM(amount) FROM commissions WHERE income_type = 'MATRIX'")->fetchColumn() ?: 0.00;
$totalLevelPayout = $pdo->query("SELECT SUM(amount) FROM commissions WHERE income_type = 'LEVEL'")->fetchColumn() ?: 0.00;
$totalCommissions = $totalMatrixPayout + $totalLevelPayout;

$allCommissions = $pdo->query("
    SELECT c.*, u.name as recipient_name, u.email as recipient_email, f.name as from_name
    FROM commissions c
    LEFT JOIN users u ON c.user_id = u.id
    LEFT JOIN users f ON c.from_user_id = f.id
    ORDER BY c.id DESC LIMIT 50
")->fetchAll();

require_once __DIR__ . '/../includes/admin_header.php';
?>

<div class="mb-8">
    <h1 class="text-3xl font-extrabold text-white tracking-tight">Commissions & Incentive Ledger</h1>
    <p class="text-slate-400 text-sm mt-1">Monitor 3-Matrix BFS auto-spillover payouts ($50 per placement across 11 levels) and 7-Level referral incentives.</p>
</div>

<!-- Stats -->
<div class="grid grid-cols-1 sm:grid-cols-3 gap-6 mb-8">
    <div class="bg-slate-800/80 border border-slate-700/60 rounded-2xl p-6">
        <span class="text-xs font-bold uppercase text-slate-400 block mb-2">3-Matrix Payouts ($50/head)</span>
        <div class="text-3xl font-extrabold text-purple-400"><?= format_price($totalMatrixPayout) ?></div>
        <p class="text-xs text-slate-500 mt-1">11-Level Matrix Spillover</p>
    </div>

    <div class="bg-slate-800/80 border border-slate-700/60 rounded-2xl p-6">
        <span class="text-xs font-bold uppercase text-slate-400 block mb-2">7-Level Referral Payouts</span>
        <div class="text-3xl font-extrabold text-blue-400"><?= format_price($totalLevelPayout) ?></div>
        <p class="text-xs text-slate-500 mt-1">L1: $500, L2: $250, L3: $200, L4: $150, L5: $100, L6: $50, L7: $50</p>
    </div>

    <div class="bg-slate-800/80 border border-slate-700/60 rounded-2xl p-6">
        <span class="text-xs font-bold uppercase text-slate-400 block mb-2">Total System Incentives</span>
        <div class="text-3xl font-black text-emerald-400"><?= format_price($totalCommissions) ?></div>
        <p class="text-xs text-slate-500 mt-1">All Distributed Commissions</p>
    </div>
</div>

<!-- Global 3-Matrix Tree Visualization -->
<div class="bg-slate-800/80 border border-slate-700/60 rounded-2xl p-6 mb-8 overflow-x-auto">
    <div class="flex items-center justify-between border-b border-slate-700/80 pb-4 mb-6">
        <div>
            <h2 class="text-lg font-bold text-white flex items-center gap-2">
                <i class="fa-solid fa-sitemap text-purple-400"></i> Global 3-Matrix Downline Tree (Apex: givoratraders)
            </h2>
            <p class="text-xs text-slate-400 mt-0.5">Real-time BFS auto-spillover matrix tree structure from top root</p>
        </div>
        <span class="text-xs bg-amber-500/10 text-amber-300 border border-amber-500/20 font-bold px-3 py-1 rounded-full">
            Apex Root: givoratraders (#1)
        </span>
    </div>

    <div class="min-w-[800px] flex justify-center py-4">
        <?php
        require_once __DIR__ . '/../includes/matrix_engine.php';
        $globalMatrixTree = get_matrix_tree_data(1, 3);
        echo render_matrix_tree_html($globalMatrixTree, 1, 3);
        ?>
    </div>
</div>

<div class="bg-slate-800/80 border border-slate-700/60 rounded-2xl p-6">
    <h2 class="text-lg font-bold text-white mb-4">Commission Distribution Log</h2>

    <div class="overflow-x-auto">
        <table class="w-full text-left text-sm text-slate-300">
            <thead class="bg-slate-900/60 text-xs uppercase font-semibold text-slate-400 border-b border-slate-700/60">
                <tr>
                    <th class="py-3 px-4">ID</th>
                    <th class="py-3 px-4">Recipient</th>
                    <th class="py-3 px-4">From Member</th>
                    <th class="py-3 px-4">Type</th>
                    <th class="py-3 px-4">Level</th>
                    <th class="py-3 px-4 text-right">Amount</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-700/40">
                <?php foreach ($allCommissions as $comm): ?>
                    <tr class="hover:bg-slate-700/20 transition">
                        <td class="py-3 px-4 text-slate-500 font-mono text-xs">#<?= $comm['id'] ?></td>
                        <td class="py-3 px-4 font-semibold text-white">
                            <?= htmlspecialchars($comm['recipient_name'] ?? 'User #' . $comm['user_id']) ?>
                            <span class="text-xs text-slate-500 block font-normal"><?= htmlspecialchars($comm['recipient_email'] ?? '') ?></span>
                        </td>
                        <td class="py-3 px-4 text-slate-300">
                            <?= htmlspecialchars($comm['from_name'] ?? 'User #' . $comm['from_user_id']) ?>
                        </td>
                        <td class="py-3 px-4">
                            <?php if ($comm['income_type'] === 'MATRIX'): ?>
                                <span class="bg-purple-500/10 text-purple-400 border border-purple-500/20 px-2.5 py-1 rounded-full text-[11px] font-bold">3-Matrix ($50)</span>
                            <?php else: ?>
                                <span class="bg-blue-500/10 text-blue-400 border border-blue-500/20 px-2.5 py-1 rounded-full text-[11px] font-bold">Level Referral</span>
                            <?php endif; ?>
                        </td>
                        <td class="py-3 px-4 font-bold text-slate-300">L<?= $comm['level'] ?></td>
                        <td class="py-3 px-4 text-right font-black text-emerald-400">+<?= format_price($comm['amount']) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/admin_footer.php'; ?>
