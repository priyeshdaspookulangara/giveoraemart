<?php
// package.php
require_once __DIR__ . '/includes/functions.php';

$pdo = get_db_connection();
$id = (int)($_GET['id'] ?? 0);

$stmt = $pdo->prepare("SELECT * FROM packages WHERE id = ?");
$stmt->execute([$id]);
$package = $stmt->fetch();

if (!$package) {
    header('Location: /shop.php');
    exit;
}

$pageTitle = htmlspecialchars($package['name']) . ' - Package Bundle';

// Fetch Included Items inside Package Box
$itemStmt = $pdo->prepare("SELECT pi.*, p.name as product_name, p.image_url as product_image FROM package_items pi LEFT JOIN products p ON pi.product_id = p.id WHERE pi.package_id = ?");
$itemStmt->execute([$id]);
$items = $itemStmt->fetchAll();

// Check Wishlist
$guestSession = get_guest_session_id();
$user = get_logged_in_user();
$isLiked = false;
if ($user) {
    $wStmt = $pdo->prepare("SELECT id FROM wishlists WHERE user_id = ? AND item_type = 'PACKAGE' AND item_id = ?");
    $wStmt->execute([$user['id'], $id]);
    $isLiked = (bool)$wStmt->fetch();
} else {
    $wStmt = $pdo->prepare("SELECT id FROM wishlists WHERE session_id = ? AND user_id IS NULL AND item_type = 'PACKAGE' AND item_id = ?");
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
        <a href="/shop.php?type=package" class="hover:text-emerald-400">Packages</a>
        <span>/</span>
        <span class="text-slate-200 truncate max-w-xs"><?= htmlspecialchars($package['name']) ?></span>
    </nav>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-12 bg-slate-900 border border-slate-800 rounded-3xl p-6 md:p-10">

        <!-- Image View -->
        <div class="space-y-6">
            <div class="relative overflow-hidden rounded-2xl bg-slate-950 border border-slate-800">
                <img src="<?= htmlspecialchars($package['image_url']) ?>" class="w-full h-80 object-cover">
                <span class="absolute top-4 left-4 bg-amber-500/20 text-amber-300 border border-amber-500/30 px-3 py-1 rounded-full text-xs font-bold">
                    <i class="fa-solid fa-gift mr-1"></i> SPECIAL PACKAGE BUNDLE
                </span>
            </div>

            <!-- What's in the box list -->
            <div class="bg-slate-950 p-5 rounded-2xl border border-slate-800 space-y-3">
                <h3 class="text-xs font-bold uppercase tracking-wider text-emerald-400">What is Inside This Bundle Box:</h3>
                <div class="divide-y divide-slate-800">
                    <?php foreach ($items as $item): ?>
                        <div class="py-2.5 flex items-center justify-between text-xs">
                            <div class="flex items-center gap-3">
                                <img src="<?= htmlspecialchars($item['product_image'] ?: 'https://via.placeholder.com/60') ?>" class="w-8 h-8 object-cover rounded-lg bg-slate-900">
                                <span class="font-semibold text-slate-200"><?= htmlspecialchars($item['product_name'] ?? $item['item_name']) ?></span>
                            </div>
                            <span class="bg-slate-800 px-2.5 py-1 rounded-lg text-emerald-400 font-bold">x<?= $item['quantity'] ?></span>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

        <!-- Details -->
        <div class="flex flex-col justify-between">
            <div>
                <h1 class="text-3xl font-extrabold text-white tracking-tight mb-4"><?= htmlspecialchars($package['name']) ?></h1>

                <div class="flex items-center gap-3 mb-6">
                    <span class="text-3xl font-black text-emerald-400"><?= format_price($package['discount_price'] > 0 ? $package['discount_price'] : $package['price']) ?></span>
                    <?php if ($package['discount_price'] > 0): ?>
                        <span class="text-sm line-through text-slate-500"><?= format_price($package['price']) ?></span>
                        <span class="bg-rose-500/10 text-rose-400 border border-rose-500/20 px-2.5 py-1 rounded-full text-xs font-bold">SAVE <?= format_price($package['price'] - $package['discount_price']) ?></span>
                    <?php endif; ?>
                </div>

                <p class="text-slate-300 text-sm leading-relaxed mb-6">
                    <?= nl2br(htmlspecialchars($package['description'])) ?>
                </p>
            </div>

            <div class="space-y-4 pt-6 border-t border-slate-800">
                <div class="flex items-center gap-4">
                    <div class="flex items-center bg-slate-950 border border-slate-800 rounded-xl px-3 py-2">
                        <label class="text-xs text-slate-400 font-semibold mr-3">Qty:</label>
                        <input type="number" id="pkg-quantity" value="1" min="1" class="w-12 bg-transparent text-white font-bold text-center text-sm focus:outline-none">
                    </div>

                    <button onclick="addToCart('PACKAGE', <?= $package['id'] ?>, document.getElementById('pkg-quantity').value)" class="flex-1 bg-emerald-500 hover:bg-emerald-600 text-slate-950 font-extrabold py-3.5 px-6 rounded-xl transition text-sm flex items-center justify-center gap-2 shadow-xl shadow-emerald-500/20">
                        <i class="fa-solid fa-cart-plus text-base"></i> Add Package To Cart
                    </button>

                    <button onclick="toggleWishlist('PACKAGE', <?= $package['id'] ?>, this)" class="p-3.5 bg-slate-800 hover:bg-slate-700 rounded-xl text-slate-300 hover:text-rose-500 transition border border-slate-700 <?= $isLiked ? 'text-rose-500' : '' ?>">
                        <i class="<?= $isLiked ? 'fa-solid' : 'fa-regular' ?> fa-heart text-lg"></i>
                    </button>
                </div>
            </div>
        </div>

    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
