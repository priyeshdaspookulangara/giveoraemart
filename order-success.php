<?php
// order-success.php
require_once __DIR__ . '/includes/functions.php';

$pdo = get_db_connection();
$orderId = (int)($_GET['id'] ?? 0);

$stmt = $pdo->prepare("SELECT * FROM orders WHERE id = ?");
$stmt->execute([$orderId]);
$order = $stmt->fetch();

if (!$order) {
    header('Location: /');
    exit;
}

$pageTitle = 'Order Confirmed - Giveoraemart';

// Fetch items
$itemsStmt = $pdo->prepare("
    SELECT oi.*,
           p.name as product_name, p.image_url as product_image,
           pkg.name as package_name, pkg.image_url as package_image
    FROM order_items oi
    LEFT JOIN products p ON oi.item_type = 'PRODUCT' AND oi.item_id = p.id
    LEFT JOIN packages pkg ON oi.item_type = 'PACKAGE' AND oi.item_id = pkg.id
    WHERE oi.order_id = ?
");
$itemsStmt->execute([$orderId]);
$items = $itemsStmt->fetchAll();

require_once __DIR__ . '/includes/header.php';
?>

<div class="max-w-3xl mx-auto px-4 py-16 text-center">

    <div class="w-20 h-20 bg-emerald-500/10 text-emerald-400 border border-emerald-500/20 rounded-full flex items-center justify-center mx-auto mb-6 text-3xl">
        <i class="fa-solid fa-circle-check"></i>
    </div>

    <h1 class="text-3xl font-extrabold text-white tracking-tight mb-2">Thank You For Your Order!</h1>
    <p class="text-slate-400 text-sm mb-8">Order <span class="font-mono text-emerald-400 font-bold">#ORD-<?= str_pad($order['id'], 5, '0', STR_PAD_LEFT) ?></span> has been placed successfully.</p>

    <div class="bg-slate-900 border border-slate-800 rounded-3xl p-6 text-left space-y-6 mb-8">

        <div class="grid grid-cols-2 gap-4 text-xs border-b border-slate-800 pb-4">
            <div>
                <span class="text-slate-500 block uppercase tracking-wider font-semibold text-[10px]">Customer Name</span>
                <span class="text-white font-bold"><?= htmlspecialchars($order['guest_name']) ?></span>
            </div>
            <div>
                <span class="text-slate-500 block uppercase tracking-wider font-semibold text-[10px]">Status</span>
                <span class="bg-amber-500/10 text-amber-400 border border-amber-500/20 px-2 py-0.5 rounded font-bold text-[11px]">PENDING FULFILLMENT</span>
            </div>
        </div>

        <div>
            <h3 class="text-xs font-bold uppercase tracking-wider text-slate-400 mb-3">Order Summary Items:</h3>
            <div class="space-y-3">
                <?php foreach ($items as $item): ?>
                    <?php
                    $itemName = $item['item_type'] === 'PRODUCT' ? $item['product_name'] : $item['package_name'];
                    $itemImg = $item['item_type'] === 'PRODUCT' ? $item['product_image'] : $item['package_image'];
                    ?>
                    <div class="flex items-center gap-3 bg-slate-950 p-2.5 rounded-xl border border-slate-800">
                        <img src="<?= htmlspecialchars($itemImg) ?>" class="w-10 h-10 object-cover rounded-lg bg-slate-900">
                        <div class="flex-1 min-w-0">
                            <h4 class="text-xs font-bold text-white truncate"><?= htmlspecialchars($itemName) ?></h4>
                            <span class="text-[10px] text-slate-500">Quantity: <?= $item['quantity'] ?></span>
                        </div>
                        <span class="text-xs font-bold text-emerald-400"><?= format_price($item['price'] * $item['quantity']) ?></span>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

        <div class="border-t border-slate-800 pt-4 flex items-center justify-between text-sm">
            <span class="font-bold text-slate-300">Total Charged Amount:</span>
            <span class="text-2xl font-black text-emerald-400"><?= format_price($order['total_amount']) ?></span>
        </div>

    </div>

    <a href="/shop.php" class="inline-flex items-center gap-2 bg-emerald-500 hover:bg-emerald-600 text-slate-950 font-bold px-6 py-3 rounded-xl transition text-xs shadow-lg shadow-emerald-500/20">
        <span>Continue Shopping</span>
        <i class="fa-solid fa-arrow-right"></i>
    </a>

</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
