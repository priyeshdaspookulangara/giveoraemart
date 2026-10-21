<?php
// admin/index.php
require_once __DIR__ . '/../includes/functions.php';

$pageTitle = 'Dashboard Overview';
$activeTab = 'dashboard';

$pdo = get_db_connection();

$totalProducts = $pdo->query("SELECT COUNT(*) FROM products")->fetchColumn();
$totalPackages = $pdo->query("SELECT COUNT(*) FROM packages")->fetchColumn();
$totalCategories = $pdo->query("SELECT COUNT(*) FROM categories")->fetchColumn();
$totalOrders = $pdo->query("SELECT COUNT(*) FROM orders")->fetchColumn();
$totalRevenue = $pdo->query("SELECT SUM(total_amount) FROM orders WHERE status != 'PENDING'")->fetchColumn() ?: 0.00;

$recentOrders = $pdo->query("SELECT * FROM orders ORDER BY created_at DESC LIMIT 5")->fetchAll();

require_once __DIR__ . '/../includes/admin_header.php';
?>

<div class="mb-8 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
    <div>
        <h1 class="text-3xl font-extrabold text-white tracking-tight">Overview Dashboard</h1>
        <p class="text-slate-400 text-sm mt-1">Welcome back, <?= htmlspecialchars($_SESSION['user']['name']) ?>! Here is what is happening with Giveoraemart today.</p>
    </div>
    <div>
        <a href="/admin/products.php" class="bg-emerald-500 hover:bg-emerald-600 text-slate-950 font-semibold px-4 py-2.5 rounded-xl transition inline-flex items-center gap-2 text-sm shadow-lg shadow-emerald-500/20">
            <i class="fa-solid fa-plus"></i> Add New Product
        </a>
    </div>
</div>

<!-- Stats Cards Grid -->
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    <div class="bg-slate-800/80 border border-slate-700/60 rounded-2xl p-6">
        <div class="flex items-center justify-between mb-4">
            <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Total Products</span>
            <div class="w-10 h-10 rounded-xl bg-blue-500/10 text-blue-400 flex items-center justify-center border border-blue-500/20">
                <i class="fa-solid fa-boxes-stacked"></i>
            </div>
        </div>
        <div class="text-3xl font-bold text-white"><?= $totalProducts ?></div>
        <p class="text-xs text-slate-500 mt-1">Active catalog items</p>
    </div>

    <div class="bg-slate-800/80 border border-slate-700/60 rounded-2xl p-6">
        <div class="flex items-center justify-between mb-4">
            <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Package Bundles</span>
            <div class="w-10 h-10 rounded-xl bg-purple-500/10 text-purple-400 flex items-center justify-center border border-purple-500/20">
                <i class="fa-solid fa-box-open"></i>
            </div>
        </div>
        <div class="text-3xl font-bold text-white"><?= $totalPackages ?></div>
        <p class="text-xs text-slate-500 mt-1">Custom package deals</p>
    </div>

    <div class="bg-slate-800/80 border border-slate-700/60 rounded-2xl p-6">
        <div class="flex items-center justify-between mb-4">
            <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Total Orders</span>
            <div class="w-10 h-10 rounded-xl bg-amber-500/10 text-amber-400 flex items-center justify-center border border-amber-500/20">
                <i class="fa-solid fa-receipt"></i>
            </div>
        </div>
        <div class="text-3xl font-bold text-white"><?= $totalOrders ?></div>
        <p class="text-xs text-slate-500 mt-1">Processed orders</p>
    </div>

    <div class="bg-slate-800/80 border border-slate-700/60 rounded-2xl p-6">
        <div class="flex items-center justify-between mb-4">
            <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Total Revenue</span>
            <div class="w-10 h-10 rounded-xl bg-emerald-500/10 text-emerald-400 flex items-center justify-center border border-emerald-500/20">
                <i class="fa-solid fa-sack-dollar"></i>
            </div>
        </div>
        <div class="text-3xl font-bold text-emerald-400"><?= format_price($totalRevenue) ?></div>
        <p class="text-xs text-slate-500 mt-1">Fulfilled sales total</p>
    </div>
</div>

<!-- Recent Orders Section -->
<div class="bg-slate-800/80 border border-slate-700/60 rounded-2xl p-6">
    <div class="flex items-center justify-between mb-6">
        <h2 class="text-xl font-bold text-white">Recent Store Orders</h2>
        <a href="/admin/orders.php" class="text-xs font-semibold text-emerald-400 hover:text-emerald-300 transition flex items-center gap-1">
            View All Orders <i class="fa-solid fa-chevron-right text-[10px]"></i>
        </a>
    </div>

    <?php if (empty($recentOrders)): ?>
        <div class="text-center py-12 text-slate-500 text-sm">
            No orders have been placed yet.
        </div>
    <?php else: ?>
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-slate-300">
                <thead class="bg-slate-900/60 text-xs uppercase font-semibold text-slate-400 border-b border-slate-700/60">
                    <tr>
                        <th class="py-3 px-4">Order ID</th>
                        <th class="py-3 px-4">Customer</th>
                        <th class="py-3 px-4">Total</th>
                        <th class="py-3 px-4">Status</th>
                        <th class="py-3 px-4">Date</th>
                        <th class="py-3 px-4 text-right">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-700/40">
                    <?php foreach ($recentOrders as $order): ?>
                        <tr class="hover:bg-slate-700/20 transition">
                            <td class="py-4 px-4 font-mono text-xs font-bold text-emerald-400">#ORD-<?= str_pad($order['id'], 5, '0', STR_PAD_LEFT) ?></td>
                            <td class="py-4 px-4">
                                <div class="font-medium text-white"><?= htmlspecialchars($order['guest_name'] ?? 'User #' . $order['user_id']) ?></div>
                                <div class="text-xs text-slate-500"><?= htmlspecialchars($order['guest_email'] ?? '') ?></div>
                            </td>
                            <td class="py-4 px-4 font-semibold text-white"><?= format_price($order['total_amount']) ?></td>
                            <td class="py-4 px-4">
                                <?php
                                $statusClasses = [
                                    'PENDING' => 'bg-amber-500/10 text-amber-400 border-amber-500/20',
                                    'PAID' => 'bg-blue-500/10 text-blue-400 border-blue-500/20',
                                    'SHIPPED' => 'bg-purple-500/10 text-purple-400 border-purple-500/20',
                                    'DELIVERED' => 'bg-emerald-500/10 text-emerald-400 border-emerald-500/20',
                                ];
                                ?>
                                <span class="px-2.5 py-1 text-[11px] font-bold rounded-full border <?= $statusClasses[$order['status']] ?? 'bg-slate-700 text-slate-300' ?>">
                                    <?= $order['status'] ?>
                                </span>
                            </td>
                            <td class="py-4 px-4 text-xs text-slate-400"><?= date('M d, Y H:i', strtotime($order['created_at'])) ?></td>
                            <td class="py-4 px-4 text-right">
                                <a href="/admin/orders.php?id=<?= $order['id'] ?>" class="text-xs text-slate-300 hover:text-white bg-slate-700 hover:bg-slate-600 px-3 py-1.5 rounded-lg transition">
                                    Manage
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>

<?php require_once __DIR__ . '/../includes/admin_footer.php'; ?>
