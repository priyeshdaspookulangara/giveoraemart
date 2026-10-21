<?php
// admin/orders.php
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/auth_check.php';

$pageTitle = 'Order Management';
$activeTab = 'orders';
$pdo = get_db_connection();

$message = '';

// Update Order Status
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $orderId = (int)($_POST['order_id'] ?? 0);
    $status = sanitize($_POST['status'] ?? '');

    $allowedStatuses = ['PENDING', 'PAID', 'SHIPPED', 'DELIVERED'];
    if ($orderId > 0 && in_array($status, $allowedStatuses)) {
        $stmt = $pdo->prepare("UPDATE orders SET status = ? WHERE id = ?");
        $stmt->execute([$status, $orderId]);
        $message = "Order #ORD-" . str_pad($orderId, 5, '0', STR_PAD_LEFT) . " status updated to $status.";
    }
}

// Order filter
$filterStatus = sanitize($_GET['status'] ?? '');
$query = "SELECT o.*, u.name as user_name, u.email as user_email FROM orders o LEFT JOIN users u ON o.user_id = u.id";
if (!empty($filterStatus)) {
    $query .= " WHERE o.status = " . $pdo->quote($filterStatus);
}
$query .= " ORDER BY o.id DESC";
$orders = $pdo->query($query)->fetchAll();

// Detail Modal view order
$selectedOrder = null;
$selectedOrderItems = [];
if (isset($_GET['id'])) {
    $detailId = (int)$_GET['id'];
    $stmt = $pdo->prepare("SELECT o.*, u.name as user_name, u.email as user_email FROM orders o LEFT JOIN users u ON o.user_id = u.id WHERE o.id = ?");
    $stmt->execute([$detailId]);
    $selectedOrder = $stmt->fetch();

    if ($selectedOrder) {
        $itemsStmt = $pdo->prepare("
            SELECT oi.*,
                   p.name as product_name, p.image_url as product_image,
                   pkg.name as package_name, pkg.image_url as package_image
            FROM order_items oi
            LEFT JOIN products p ON oi.item_type = 'PRODUCT' AND oi.item_id = p.id
            LEFT JOIN packages pkg ON oi.item_type = 'PACKAGE' AND oi.item_id = pkg.id
            WHERE oi.order_id = ?
        ");
        $itemsStmt->execute([$detailId]);
        $selectedOrderItems = $itemsStmt->fetchAll();
    }
}

require_once __DIR__ . '/../includes/admin_header.php';
?>

<div class="mb-8 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
    <div>
        <h1 class="text-3xl font-extrabold text-white tracking-tight">Customer Orders</h1>
        <p class="text-slate-400 text-sm mt-1">View incoming orders, inspect line items, and fulfill shipments.</p>
    </div>

    <!-- Filter Pills -->
    <div class="flex items-center gap-2 bg-slate-800 p-1.5 rounded-xl border border-slate-700 text-xs">
        <a href="/admin/orders.php" class="px-3 py-1.5 rounded-lg font-semibold transition <?= empty($filterStatus) ? 'bg-emerald-500 text-slate-950' : 'text-slate-400 hover:text-white' ?>">All</a>
        <a href="/admin/orders.php?status=PENDING" class="px-3 py-1.5 rounded-lg font-semibold transition <?= $filterStatus === 'PENDING' ? 'bg-amber-500 text-slate-950' : 'text-slate-400 hover:text-white' ?>">Pending</a>
        <a href="/admin/orders.php?status=PAID" class="px-3 py-1.5 rounded-lg font-semibold transition <?= $filterStatus === 'PAID' ? 'bg-blue-500 text-slate-950' : 'text-slate-400 hover:text-white' ?>">Paid</a>
        <a href="/admin/orders.php?status=SHIPPED" class="px-3 py-1.5 rounded-lg font-semibold transition <?= $filterStatus === 'SHIPPED' ? 'bg-purple-500 text-slate-950' : 'text-slate-400 hover:text-white' ?>">Shipped</a>
        <a href="/admin/orders.php?status=DELIVERED" class="px-3 py-1.5 rounded-lg font-semibold transition <?= $filterStatus === 'DELIVERED' ? 'bg-emerald-500 text-slate-950' : 'text-slate-400 hover:text-white' ?>">Delivered</a>
    </div>
</div>

<?php if ($message): ?>
    <div class="mb-6 p-4 bg-emerald-500/10 border border-emerald-500/30 text-emerald-400 text-sm rounded-xl flex items-center gap-3">
        <i class="fa-solid fa-circle-check text-base"></i>
        <span><?= $message ?></span>
    </div>
<?php endif; ?>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
    <!-- Orders Table -->
    <div class="<?= $selectedOrder ? 'lg:col-span-2' : 'lg:col-span-3' ?> bg-slate-800/80 border border-slate-700/60 rounded-2xl p-6">
        <h2 class="text-lg font-bold text-white mb-4 flex items-center gap-2">
            <i class="fa-solid fa-receipt text-emerald-400"></i> Order List (<?= count($orders) ?>)
        </h2>

        <?php if (empty($orders)): ?>
            <div class="text-center py-12 text-slate-500 text-sm">No orders found matching criteria.</div>
        <?php else: ?>
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm text-slate-300">
                    <thead class="bg-slate-900/60 text-xs uppercase font-semibold text-slate-400 border-b border-slate-700/60">
                        <tr>
                            <th class="py-3 px-4">Order ID</th>
                            <th class="py-3 px-4">Customer</th>
                            <th class="py-3 px-4">Total</th>
                            <th class="py-3 px-4">Status</th>
                            <th class="py-3 px-4 text-right">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-700/40">
                        <?php foreach ($orders as $ord): ?>
                            <tr class="hover:bg-slate-700/20 transition <?= ($selectedOrder['id'] ?? 0) === $ord['id'] ? 'bg-slate-700/30' : '' ?>">
                                <td class="py-3.5 px-4 font-mono text-xs font-bold text-emerald-400">#ORD-<?= str_pad($ord['id'], 5, '0', STR_PAD_LEFT) ?></td>
                                <td class="py-3.5 px-4">
                                    <div class="font-medium text-white"><?= htmlspecialchars($ord['guest_name'] ?: ($ord['user_name'] ?? 'Guest')) ?></div>
                                    <div class="text-xs text-slate-500"><?= htmlspecialchars($ord['guest_email'] ?: ($ord['user_email'] ?? '')) ?></div>
                                </td>
                                <td class="py-3.5 px-4 font-bold text-white"><?= format_price($ord['total_amount']) ?></td>
                                <td class="py-3.5 px-4">
                                    <form method="POST" class="inline-block">
                                        <input type="hidden" name="order_id" value="<?= $ord['id'] ?>">
                                        <select name="status" onchange="this.form.submit()" class="bg-slate-900 border border-slate-700 text-xs font-semibold rounded-lg px-2.5 py-1 text-slate-200 focus:outline-none focus:border-emerald-500">
                                            <option value="PENDING" <?= $ord['status'] === 'PENDING' ? 'selected' : '' ?>>PENDING</option>
                                            <option value="PAID" <?= $ord['status'] === 'PAID' ? 'selected' : '' ?>>PAID</option>
                                            <option value="SHIPPED" <?= $ord['status'] === 'SHIPPED' ? 'selected' : '' ?>>SHIPPED</option>
                                            <option value="DELIVERED" <?= $ord['status'] === 'DELIVERED' ? 'selected' : '' ?>>DELIVERED</option>
                                        </select>
                                    </form>
                                </td>
                                <td class="py-3.5 px-4 text-right">
                                    <a href="/admin/orders.php?id=<?= $ord['id'] ?><?= !empty($filterStatus) ? '&status='.$filterStatus : '' ?>" class="text-xs bg-slate-700 hover:bg-slate-600 text-white font-semibold px-3 py-1.5 rounded-lg transition inline-flex items-center gap-1">
                                        <i class="fa-solid fa-eye text-[11px]"></i> Details
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>

    <!-- Selected Order Detail Panel -->
    <?php if ($selectedOrder): ?>
        <div class="bg-slate-800/80 border border-slate-700/60 rounded-2xl p-6 h-fit">
            <div class="flex items-center justify-between border-b border-slate-700 pb-4 mb-4">
                <div>
                    <h3 class="font-bold text-white text-lg">Order Details</h3>
                    <span class="font-mono text-xs text-emerald-400">#ORD-<?= str_pad($selectedOrder['id'], 5, '0', STR_PAD_LEFT) ?></span>
                </div>
                <a href="/admin/orders.php" class="text-slate-400 hover:text-white p-1"><i class="fa-solid fa-xmark text-lg"></i></a>
            </div>

            <!-- Customer Details -->
            <div class="space-y-3 text-xs mb-6 bg-slate-900/60 p-4 rounded-xl border border-slate-700/50">
                <div>
                    <span class="text-slate-500 uppercase tracking-wider block font-semibold text-[10px]">Customer Name</span>
                    <span class="text-slate-200 font-semibold text-sm"><?= htmlspecialchars($selectedOrder['guest_name'] ?: ($selectedOrder['user_name'] ?? 'Guest')) ?></span>
                </div>
                <div>
                    <span class="text-slate-500 uppercase tracking-wider block font-semibold text-[10px]">Contact Email & Phone</span>
                    <span class="text-slate-300"><?= htmlspecialchars($selectedOrder['guest_email'] ?: ($selectedOrder['user_email'] ?? 'N/A')) ?></span>
                    <?php if ($selectedOrder['guest_phone']): ?>
                        <span class="text-slate-400 block"><?= htmlspecialchars($selectedOrder['guest_phone']) ?></span>
                    <?php endif; ?>
                </div>
                <div>
                    <span class="text-slate-500 uppercase tracking-wider block font-semibold text-[10px]">Shipping Address</span>
                    <span class="text-slate-300 leading-relaxed block"><?= nl2br(htmlspecialchars($selectedOrder['shipping_address'])) ?></span>
                </div>
            </div>

            <!-- Line Items -->
            <h4 class="text-xs font-bold uppercase tracking-wider text-slate-400 mb-3">Purchased Items</h4>
            <div class="space-y-3 mb-6">
                <?php foreach ($selectedOrderItems as $item): ?>
                    <?php
                    $itemName = $item['item_type'] === 'PRODUCT' ? $item['product_name'] : $item['package_name'];
                    $itemImg = $item['item_type'] === 'PRODUCT' ? $item['product_image'] : $item['package_image'];
                    ?>
                    <div class="flex items-center gap-3 bg-slate-900/40 p-2.5 rounded-xl border border-slate-700/40">
                        <img src="<?= htmlspecialchars($itemImg ?: 'https://via.placeholder.com/80') ?>" class="w-12 h-12 object-cover rounded-lg bg-slate-950">
                        <div class="flex-1 min-w-0">
                            <h5 class="text-xs font-semibold text-white truncate"><?= htmlspecialchars($itemName ?: 'Item #' . $item['item_id']) ?></h5>
                            <span class="text-[10px] text-slate-400 font-medium">Type: <?= $item['item_type'] ?></span>
                        </div>
                        <div class="text-right">
                            <div class="text-xs font-bold text-emerald-400"><?= format_price($item['price']) ?></div>
                            <span class="text-[11px] text-slate-400">x<?= $item['quantity'] ?></span>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <div class="border-t border-slate-700 pt-4 flex items-center justify-between">
                <span class="text-sm font-bold text-slate-300">Total Charged:</span>
                <span class="text-xl font-extrabold text-emerald-400"><?= format_price($selectedOrder['total_amount']) ?></span>
            </div>
        </div>
    <?php endif; ?>
</div>

<?php require_once __DIR__ . '/../includes/admin_footer.php'; ?>
