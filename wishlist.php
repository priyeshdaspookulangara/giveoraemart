<?php
// wishlist.php
require_once __DIR__ . '/includes/functions.php';

$pageTitle = 'Saved Wishlist - Giveoraemart';
$pdo = get_db_connection();

$user = get_logged_in_user();
$guestSession = get_guest_session_id();

if ($user) {
    $stmt = $pdo->prepare("SELECT * FROM wishlists WHERE user_id = ?");
    $stmt->execute([$user['id']]);
} else {
    $stmt = $pdo->prepare("SELECT * FROM wishlists WHERE session_id = ? AND user_id IS NULL");
    $stmt->execute([$guestSession]);
}

$wishlistItems = $stmt->fetchAll();

$itemsList = [];
foreach ($wishlistItems as $w) {
    if ($w['item_type'] === 'PRODUCT') {
        $pStmt = $pdo->prepare("SELECT id, name, price, image_url, purchase_type FROM products WHERE id = ?");
        $pStmt->execute([$w['item_id']]);
        $prod = $pStmt->fetch();
        if ($prod) {
            $itemsList[] = [
                'type' => 'PRODUCT',
                'id' => $prod['id'],
                'name' => $prod['name'],
                'price' => $prod['price'],
                'image_url' => $prod['image_url'],
                'badge' => $prod['purchase_type']
            ];
        }
    } elseif ($w['item_type'] === 'PACKAGE') {
        $pkgStmt = $pdo->prepare("SELECT id, name, price, discount_price, image_url FROM packages WHERE id = ?");
        $pkgStmt->execute([$w['item_id']]);
        $pkg = $pkgStmt->fetch();
        if ($pkg) {
            $effectivePrice = ($pkg['discount_price'] > 0) ? $pkg['discount_price'] : $pkg['price'];
            $itemsList[] = [
                'type' => 'PACKAGE',
                'id' => $pkg['id'],
                'name' => $pkg['name'],
                'price' => $effectivePrice,
                'image_url' => $pkg['image_url'],
                'badge' => 'PACKAGE BUNDLE'
            ];
        }
    }
}

require_once __DIR__ . '/includes/header.php';
?>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">

    <div class="mb-8 border-b border-slate-800 pb-6 flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-extrabold text-white tracking-tight">Saved Wishlist</h1>
            <p class="text-slate-400 text-sm mt-1">Items you liked for fast future repurchase or checkout.</p>
        </div>
        <span class="bg-rose-500/10 text-rose-400 border border-rose-500/20 px-3 py-1 rounded-full text-xs font-bold">
            <i class="fa-solid fa-heart mr-1"></i> <?= count($itemsList) ?> Liked Items
        </span>
    </div>

    <?php if (empty($itemsList)): ?>
        <div class="bg-slate-900 border border-slate-800 rounded-3xl p-16 text-center max-w-xl mx-auto">
            <div class="w-16 h-16 bg-slate-800 text-rose-400 rounded-full flex items-center justify-center mx-auto mb-4 text-2xl">
                <i class="fa-regular fa-heart"></i>
            </div>
            <h2 class="text-xl font-bold text-white mb-2">No saved items in your wishlist</h2>
            <p class="text-xs text-slate-400 mb-6">Click the heart icon on any product or package card to save items to your wishlist.</p>
            <a href="/shop.php" class="inline-flex items-center gap-2 bg-emerald-500 hover:bg-emerald-600 text-slate-950 font-bold px-6 py-3 rounded-xl transition text-xs shadow-lg shadow-emerald-500/20">
                <span>Explore Catalog</span>
                <i class="fa-solid fa-arrow-right"></i>
            </a>
        </div>
    <?php else: ?>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
            <?php foreach ($itemsList as $item): ?>
                <div class="bg-slate-900 border border-slate-800 rounded-2xl p-4 flex flex-col justify-between hover:border-emerald-500/40 transition">
                    <div>
                        <div class="relative overflow-hidden rounded-xl mb-3">
                            <img src="<?= htmlspecialchars($item['image_url']) ?>" class="w-full h-44 object-cover bg-slate-950">
                            <span class="absolute top-2 left-2 bg-slate-950/80 text-emerald-400 border border-slate-800 px-2 py-0.5 rounded-md text-[10px] font-bold">
                                <?= $item['badge'] ?>
                            </span>
                            <button onclick="removeWishlistItem('<?= $item['type'] ?>', <?= $item['id'] ?>, this)" class="absolute top-2 right-2 w-8 h-8 rounded-full bg-slate-950/80 text-rose-500 hover:text-rose-400 flex items-center justify-center text-xs border border-slate-800" title="Remove">
                                <i class="fa-solid fa-xmark"></i>
                            </button>
                        </div>

                        <h3 class="font-bold text-white text-sm line-clamp-1 mb-1"><?= htmlspecialchars($item['name']) ?></h3>
                        <div class="text-base font-extrabold text-emerald-400 mb-3"><?= format_price($item['price']) ?></div>
                    </div>

                    <button onclick="addToCart('<?= $item['type'] ?>', <?= $item['id'] ?>)" class="w-full bg-emerald-500 hover:bg-emerald-600 text-slate-950 font-bold py-2.5 rounded-xl transition text-xs flex items-center justify-center gap-2">
                        <i class="fa-solid fa-cart-plus"></i> Move To Cart
                    </button>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

</div>

<script>
async function removeWishlistItem(type, id, btn) {
    const formData = new FormData();
    formData.append('type', type);
    formData.append('id', id);

    const res = await fetch('/api/wishlist.php', { method: 'POST', body: formData });
    const data = await res.json();
    if (data.success) {
        window.location.reload();
    }
}
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
