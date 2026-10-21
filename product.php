<?php
// product.php
require_once __DIR__ . '/includes/functions.php';

$pdo = get_db_connection();
$id = (int)($_GET['id'] ?? 0);

$stmt = $pdo->prepare("SELECT p.*, c.name as category_name FROM products p LEFT JOIN categories c ON p.category_id = c.id WHERE p.id = ?");
$stmt->execute([$id]);
$product = $stmt->fetch();

if (!$product) {
    header('Location: /shop.php');
    exit;
}

$pageTitle = htmlspecialchars($product['name']) . ' - Giveoraemart';

// Check Wishlist
$guestSession = get_guest_session_id();
$user = get_logged_in_user();
$isLiked = false;
if ($user) {
    $wStmt = $pdo->prepare("SELECT id FROM wishlists WHERE user_id = ? AND item_type = 'PRODUCT' AND item_id = ?");
    $wStmt->execute([$user['id'], $id]);
    $isLiked = (bool)$wStmt->fetch();
} else {
    $wStmt = $pdo->prepare("SELECT id FROM wishlists WHERE session_id = ? AND user_id IS NULL AND item_type = 'PRODUCT' AND item_id = ?");
    $wStmt->execute([$guestSession, $id]);
    $isLiked = (bool)$wStmt->fetch();
}

require_once __DIR__ . '/includes/header.php';
?>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">

    <!-- Breadcrumb Nav -->
    <nav class="flex text-xs text-slate-400 gap-2 mb-8">
        <a href="/" class="hover:text-emerald-400">Home</a>
        <span>/</span>
        <a href="/shop.php" class="hover:text-emerald-400">Shop</a>
        <span>/</span>
        <span class="text-slate-200 truncate max-w-xs"><?= htmlspecialchars($product['name']) ?></span>
    </nav>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-12 bg-slate-900 border border-slate-800 rounded-3xl p-6 md:p-10">

        <!-- Image View -->
        <div class="relative overflow-hidden rounded-2xl bg-slate-950 border border-slate-800">
            <img src="<?= htmlspecialchars($product['image_url']) ?>" class="w-full h-96 md:h-[450px] object-cover">

            <?php if ($product['purchase_type'] === 'REPURCHASE'): ?>
                <span class="absolute top-4 left-4 bg-purple-500/20 text-purple-300 border border-purple-500/30 px-3 py-1 rounded-full text-xs font-bold">
                    <i class="fa-solid fa-arrows-rotate mr-1"></i> REPURCHASE CONSUMABLE
                </span>
            <?php else: ?>
                <span class="absolute top-4 left-4 bg-blue-500/20 text-blue-300 border border-blue-500/30 px-3 py-1 rounded-full text-xs font-bold">
                    <i class="fa-solid fa-bag-shopping mr-1"></i> ONE-TIME PURCHASE
                </span>
            <?php endif; ?>
        </div>

        <!-- Details -->
        <div class="flex flex-col justify-between">
            <div>
                <div class="text-xs font-bold uppercase tracking-wider text-emerald-400 mb-2">
                    Category: <?= htmlspecialchars($product['category_name'] ?? 'General Grocery') ?>
                </div>

                <h1 class="text-3xl font-extrabold text-white tracking-tight mb-4"><?= htmlspecialchars($product['name']) ?></h1>

                <div class="text-3xl font-black text-emerald-400 mb-6"><?= format_price($product['price']) ?></div>

                <p class="text-slate-300 text-sm leading-relaxed mb-6">
                    <?= nl2br(htmlspecialchars($product['description'])) ?>
                </p>

                <div class="bg-slate-950/60 p-4 rounded-xl border border-slate-800 space-y-2 mb-8 text-xs text-slate-400">
                    <div class="flex justify-between">
                        <span>Availability Stock:</span>
                        <span class="font-bold text-white"><?= $product['stock'] > 0 ? $product['stock'] . ' in stock' : 'Out of Stock' ?></span>
                    </div>
                    <div class="flex justify-between">
                        <span>Classification:</span>
                        <span class="font-bold text-white"><?= $product['purchase_type'] === 'REPURCHASE' ? 'Recurring Grocery / Pantry' : 'Hardware / Kitchenware' ?></span>
                    </div>
                </div>
            </div>

            <div class="space-y-4 pt-4 border-t border-slate-800">
                <div class="flex items-center gap-4">
                    <div class="flex items-center bg-slate-950 border border-slate-800 rounded-xl px-3 py-2">
                        <label class="text-xs text-slate-400 font-semibold mr-3">Qty:</label>
                        <input type="number" id="prod-quantity" value="1" min="1" max="<?= $product['stock'] ?>" class="w-12 bg-transparent text-white font-bold text-center text-sm focus:outline-none">
                    </div>

                    <button onclick="addToCart('PRODUCT', <?= $product['id'] ?>, document.getElementById('prod-quantity').value)" class="flex-1 bg-emerald-500 hover:bg-emerald-600 text-slate-950 font-extrabold py-3.5 px-6 rounded-xl transition text-sm flex items-center justify-center gap-2 shadow-xl shadow-emerald-500/20">
                        <i class="fa-solid fa-cart-plus text-base"></i> Add To Cart
                    </button>

                    <button onclick="toggleWishlist('PRODUCT', <?= $product['id'] ?>, this)" class="p-3.5 bg-slate-800 hover:bg-slate-700 rounded-xl text-slate-300 hover:text-rose-500 transition border border-slate-700 <?= $isLiked ? 'text-rose-500' : '' ?>">
                        <i class="<?= $isLiked ? 'fa-solid' : 'fa-regular' ?> fa-heart text-lg"></i>
                    </button>
                </div>
            </div>
        </div>

    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
