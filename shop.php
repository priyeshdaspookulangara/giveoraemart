<?php
// shop.php
require_once __DIR__ . '/includes/functions.php';

$pageTitle = 'Catalog Store - Giveoraemart';
$pdo = get_db_connection();

// Read Filters
$selectedType = sanitize($_GET['type'] ?? ''); // 'product' or 'package'
$selectedPurchaseType = sanitize($_GET['purchase_type'] ?? ''); // 'PURCHASE' or 'REPURCHASE'
$selectedCategory = (int)($_GET['category'] ?? 0);
$searchQuery = sanitize($_GET['q'] ?? '');

// Wishlist set
$guestSession = get_guest_session_id();
$user = get_logged_in_user();
$wishlistSet = [];
if ($user) {
    $wStmt = $pdo->prepare("SELECT CONCAT(item_type, '_', item_id) FROM wishlists WHERE user_id = ?");
    $wStmt->execute([$user['id']]);
    $wishlistSet = $wStmt->fetchAll(PDO::FETCH_COLUMN);
} else {
    $wStmt = $pdo->prepare("SELECT CONCAT(item_type, '_', item_id) FROM wishlists WHERE session_id = ? AND user_id IS NULL");
    $wStmt->execute([$guestSession]);
    $wishlistSet = $wStmt->fetchAll(PDO::FETCH_COLUMN);
}

// Fetch categories
$categories = $pdo->query("SELECT * FROM categories ORDER BY name ASC")->fetchAll();

// Build Product Query
$productWhere = ["1=1"];
$params = [];

if ($selectedPurchaseType) {
    $productWhere[] = "p.purchase_type = ?";
    $params[] = $selectedPurchaseType;
}

if ($selectedCategory > 0) {
    $productWhere[] = "p.category_id = ?";
    $params[] = $selectedCategory;
}

if ($searchQuery) {
    $productWhere[] = "(p.name LIKE ? OR p.description LIKE ?)";
    $params[] = "%$searchQuery%";
    $params[] = "%$searchQuery%";
}

$productSql = "SELECT p.*, c.name as category_name, 'PRODUCT' as item_kind FROM products p LEFT JOIN categories c ON p.category_id = c.id WHERE " . implode(' AND ', $productWhere) . " ORDER BY p.id DESC";
$prodStmt = $pdo->prepare($productSql);
$prodStmt->execute($params);
$productsList = $prodStmt->fetchAll();

// Build Package Query
$packagesList = [];
if ($selectedType !== 'product' && empty($selectedPurchaseType) && $selectedCategory === 0) {
    $pkgWhere = ["1=1"];
    $pkgParams = [];
    if ($searchQuery) {
        $pkgWhere[] = "(name LIKE ? OR description LIKE ?)";
        $pkgParams[] = "%$searchQuery%";
        $pkgParams[] = "%$searchQuery%";
    }
    $pkgSql = "SELECT *, 'PACKAGE' as item_kind FROM packages WHERE " . implode(' AND ', $pkgWhere) . " ORDER BY id DESC";
    $pkgStmt = $pdo->prepare($pkgSql);
    $pkgStmt->execute($pkgParams);
    $packagesList = $pkgStmt->fetchAll();
}

require_once __DIR__ . '/includes/header.php';
?>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">

    <!-- Title & Search Bar Header -->
    <div class="mb-8 flex flex-col md:flex-row md:items-center justify-between gap-4 border-b border-slate-800 pb-6">
        <div>
            <h1 class="text-3xl font-extrabold text-white tracking-tight">Explore Storefront Catalog</h1>
            <p class="text-slate-400 text-sm mt-1">Filter by purchase type, categories, or special promotional package bundles.</p>
        </div>

        <form method="GET" class="flex gap-2 max-w-md w-full">
            <?php if ($selectedPurchaseType): ?><input type="hidden" name="purchase_type" value="<?= htmlspecialchars($selectedPurchaseType) ?>"><?php endif; ?>
            <?php if ($selectedCategory): ?><input type="hidden" name="category" value="<?= $selectedCategory ?>"><?php endif; ?>
            <div class="relative w-full">
                <i class="fa-solid fa-magnifying-glass absolute left-3.5 top-3 text-slate-500 text-xs"></i>
                <input type="text" name="q" value="<?= htmlspecialchars($searchQuery) ?>" placeholder="Filter products..." class="w-full bg-slate-900 border border-slate-800 rounded-xl pl-9 pr-4 py-2 text-xs text-white placeholder-slate-500 focus:outline-none focus:border-emerald-500">
            </div>
            <button type="submit" class="bg-emerald-500 text-slate-950 font-bold px-4 py-2 rounded-xl text-xs hover:bg-emerald-600 transition">Search</button>
        </form>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">

        <!-- Sidebar Filters -->
        <div class="space-y-6">
            <!-- Purchase Type Filter -->
            <div class="bg-slate-900 border border-slate-800 rounded-2xl p-5">
                <h3 class="text-xs font-bold uppercase tracking-wider text-emerald-400 mb-3 flex items-center gap-2">
                    <i class="fa-solid fa-filter"></i> Purchase Type
                </h3>
                <div class="space-y-2 text-xs font-semibold">
                    <a href="/shop.php" class="block px-3 py-2 rounded-xl transition <?= empty($selectedPurchaseType) && empty($selectedType) ? 'bg-emerald-500/10 text-emerald-400 border border-emerald-500/20' : 'text-slate-400 hover:text-white' ?>">
                        All Products & Bundles
                    </a>
                    <a href="/shop.php?purchase_type=REPURCHASE" class="block px-3 py-2 rounded-xl transition <?= $selectedPurchaseType === 'REPURCHASE' ? 'bg-purple-500/10 text-purple-400 border border-purple-500/20' : 'text-slate-400 hover:text-white' ?>">
                        <i class="fa-solid fa-arrows-rotate text-purple-400 mr-2"></i> Repurchase (Grocery / Pantry)
                    </a>
                    <a href="/shop.php?purchase_type=PURCHASE" class="block px-3 py-2 rounded-xl transition <?= $selectedPurchaseType === 'PURCHASE' ? 'bg-blue-500/10 text-blue-400 border border-blue-500/20' : 'text-slate-400 hover:text-white' ?>">
                        <i class="fa-solid fa-bag-shopping text-blue-400 mr-2"></i> Purchase (One-time Buy)
                    </a>
                    <a href="/shop.php?type=package" class="block px-3 py-2 rounded-xl transition <?= $selectedType === 'package' ? 'bg-amber-500/10 text-amber-400 border border-amber-500/20' : 'text-slate-400 hover:text-white' ?>">
                        <i class="fa-solid fa-box-open text-amber-400 mr-2"></i> Package Bundles Only
                    </a>
                </div>
            </div>

            <!-- Categories Filter -->
            <div class="bg-slate-900 border border-slate-800 rounded-2xl p-5">
                <h3 class="text-xs font-bold uppercase tracking-wider text-emerald-400 mb-3 flex items-center gap-2">
                    <i class="fa-solid fa-layer-group"></i> Store Categories
                </h3>
                <div class="space-y-1 text-xs">
                    <a href="/shop.php" class="block py-1.5 px-2 rounded-lg transition <?= $selectedCategory === 0 ? 'text-emerald-400 font-bold' : 'text-slate-400 hover:text-white' ?>">All Categories</a>
                    <?php foreach ($categories as $cat): ?>
                        <a href="/shop.php?category=<?= $cat['id'] ?><?= $selectedPurchaseType ? '&purchase_type='.$selectedPurchaseType : '' ?>" class="block py-1.5 px-2 rounded-lg transition <?= $selectedCategory === $cat['id'] ? 'text-emerald-400 font-bold bg-slate-800' : 'text-slate-400 hover:text-white' ?>">
                            <?= htmlspecialchars($cat['name']) ?>
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

        <!-- Catalog Product Grid -->
        <div class="lg:col-span-3 space-y-8">

            <!-- Packages Section if present -->
            <?php if (!empty($packagesList)): ?>
                <div>
                    <h2 class="text-lg font-bold text-white mb-4 flex items-center gap-2">
                        <i class="fa-solid fa-gift text-amber-400"></i> Promotional Package Deals
                    </h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <?php foreach ($packagesList as $pkg): ?>
                            <?php $isLiked = in_array('PACKAGE_' . $pkg['id'], $wishlistSet); ?>
                            <div class="bg-slate-900 border border-slate-800 rounded-2xl p-4 flex flex-col justify-between hover:border-emerald-500/40 transition">
                                <div class="relative overflow-hidden rounded-xl mb-3">
                                    <img src="<?= htmlspecialchars($pkg['image_url']) ?>" class="w-full h-44 object-cover bg-slate-950">
                                    <button onclick="toggleWishlist('PACKAGE', <?= $pkg['id'] ?>, this)" class="absolute top-2 right-2 w-8 h-8 rounded-full bg-slate-950/80 backdrop-blur-md flex items-center justify-center <?= $isLiked ? 'text-rose-500' : 'text-slate-400' ?> hover:text-rose-500 transition border border-slate-800 text-xs">
                                        <i class="<?= $isLiked ? 'fa-solid' : 'fa-regular' ?> fa-heart"></i>
                                    </button>
                                </div>
                                <div>
                                    <h3 class="font-bold text-white text-base"><a href="/package.php?id=<?= $pkg['id'] ?>" class="hover:text-emerald-400 transition"><?= htmlspecialchars($pkg['name']) ?></a></h3>
                                    <p class="text-xs text-slate-400 mt-1 line-clamp-2"><?= htmlspecialchars($pkg['description']) ?></p>
                                </div>
                                <div class="flex items-center justify-between pt-4 mt-3 border-t border-slate-800">
                                    <span class="text-emerald-400 font-extrabold text-base"><?= format_price($pkg['discount_price'] > 0 ? $pkg['discount_price'] : $pkg['price']) ?></span>
                                    <button onclick="addToCart('PACKAGE', <?= $pkg['id'] ?>)" class="bg-emerald-500 text-slate-950 font-bold px-3 py-1.5 rounded-lg text-xs hover:bg-emerald-600 transition">Add Bundle</button>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Products Section -->
            <?php if ($selectedType !== 'package'): ?>
                <div>
                    <h2 class="text-lg font-bold text-white mb-4 flex items-center gap-2">
                        <i class="fa-solid fa-boxes-stacked text-emerald-400"></i> Individual Store Items (<?= count($productsList) ?>)
                    </h2>

                    <?php if (empty($productsList)): ?>
                        <div class="bg-slate-900 border border-slate-800 rounded-2xl p-12 text-center text-slate-500 text-sm">
                            No products match your current filter selections.
                        </div>
                    <?php else: ?>
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                            <?php foreach ($productsList as $p): ?>
                                <?php $isLiked = in_array('PRODUCT_' . $p['id'], $wishlistSet); ?>
                                <div class="bg-slate-900 border border-slate-800 rounded-2xl p-4 hover:border-emerald-500/40 transition group flex flex-col justify-between">
                                    <div>
                                        <div class="relative overflow-hidden rounded-xl mb-4">
                                            <img src="<?= htmlspecialchars($p['image_url']) ?>" class="w-full h-48 object-cover group-hover:scale-105 transition duration-500 bg-slate-950">

                                            <?php if ($p['purchase_type'] === 'REPURCHASE'): ?>
                                                <span class="absolute top-2 left-2 bg-purple-500/20 text-purple-300 border border-purple-500/30 px-2 py-0.5 rounded-md text-[10px] font-bold">REPURCHASE</span>
                                            <?php else: ?>
                                                <span class="absolute top-2 left-2 bg-blue-500/20 text-blue-300 border border-blue-500/30 px-2 py-0.5 rounded-md text-[10px] font-bold">PURCHASE</span>
                                            <?php endif; ?>

                                            <button onclick="toggleWishlist('PRODUCT', <?= $p['id'] ?>, this)" class="absolute top-2 right-2 w-8 h-8 rounded-full bg-slate-950/80 backdrop-blur-md flex items-center justify-center <?= $isLiked ? 'text-rose-500' : 'text-slate-400' ?> hover:text-rose-500 transition border border-slate-800 text-xs">
                                                <i class="<?= $isLiked ? 'fa-solid' : 'fa-regular' ?> fa-heart"></i>
                                            </button>
                                        </div>

                                        <div class="text-[10px] uppercase font-bold text-slate-500 mb-1"><?= htmlspecialchars($p['category_name'] ?? 'General') ?></div>
                                        <h3 class="font-bold text-white text-sm line-clamp-1 group-hover:text-emerald-400 transition mb-2">
                                            <a href="/product.php?id=<?= $p['id'] ?>"><?= htmlspecialchars($p['name']) ?></a>
                                        </h3>
                                    </div>

                                    <div class="flex items-center justify-between pt-3 border-t border-slate-800/80 mt-2">
                                        <span class="text-base font-extrabold text-emerald-400"><?= format_price($p['price']) ?></span>
                                        <button onclick="addToCart('PRODUCT', <?= $p['id'] ?>)" class="bg-slate-800 hover:bg-emerald-500 text-slate-200 hover:text-slate-950 font-bold p-2.5 rounded-xl transition text-xs border border-slate-700 hover:border-emerald-500" title="Add to Cart">
                                            <i class="fa-solid fa-cart-plus"></i>
                                        </button>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endif; ?>

        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
