<?php
// index.php
require_once __DIR__ . '/includes/functions.php';

$pageTitle = 'Giveoraemart - Fresh Groceries & Premium Package Bundles';
$pdo = get_db_connection();

// Fetch Categories
$categories = $pdo->query("SELECT * FROM categories ORDER BY name ASC")->fetchAll();

// Fetch Featured Products
$repurchaseProducts = $pdo->query("SELECT p.*, c.name as category_name FROM products p LEFT JOIN categories c ON p.category_id = c.id WHERE p.purchase_type = 'REPURCHASE' ORDER BY p.id DESC LIMIT 4")->fetchAll();
$purchaseProducts = $pdo->query("SELECT p.*, c.name as category_name FROM products p LEFT JOIN categories c ON p.category_id = c.id WHERE p.purchase_type = 'PURCHASE' ORDER BY p.id DESC LIMIT 4")->fetchAll();

// Fetch Packages
$packages = $pdo->query("SELECT * FROM packages ORDER BY id DESC LIMIT 2")->fetchAll();

// Fetch User Wishlist IDs if logged in or guest session
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

require_once __DIR__ . '/includes/header.php';
?>

<!-- Hero Banner Section -->
<section class="relative bg-slate-900 overflow-hidden py-16 lg:py-24 border-b border-slate-800">
    <div class="absolute inset-0 opacity-20 bg-[radial-gradient(#10b981_1px,transparent_1px)] [background-size:16px_16px]"></div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
            <div class="space-y-6">
                <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 text-xs font-bold uppercase tracking-wider">
                    <i class="fa-solid fa-sparkles"></i> Ultimate Grocery & Lifestyle Mart
                </div>
                <h1 class="text-4xl sm:text-5xl lg:text-6xl font-black text-white tracking-tight leading-none">
                    Fresh Everyday <span class="text-transparent bg-clip-text bg-gradient-to-r from-emerald-400 to-teal-300">Repurchase</span> Essentials & Kits.
                </h1>
                <p class="text-slate-400 text-base sm:text-lg leading-relaxed">
                    Easily shop your favorite daily pantry consumables or unlock huge savings with curated promotional combo package bundles. Delivered directly to your door.
                </p>

                <div class="flex flex-wrap gap-4 pt-2">
                    <a href="/shop.php?purchase_type=REPURCHASE" class="bg-emerald-500 hover:bg-emerald-600 text-slate-950 font-bold px-6 py-3.5 rounded-2xl transition shadow-xl shadow-emerald-500/20 flex items-center gap-2">
                        <i class="fa-solid fa-arrows-rotate"></i> Shop Repurchase Grocery
                    </a>
                    <a href="/shop.php?type=package" class="bg-slate-800 hover:bg-slate-700 text-white font-semibold px-6 py-3.5 rounded-2xl transition border border-slate-700 flex items-center gap-2">
                        <i class="fa-solid fa-box-open text-amber-400"></i> View Special Packages
                    </a>
                </div>

                <div class="pt-6 border-t border-slate-800 flex items-center gap-8 text-xs text-slate-400">
                    <div class="flex items-center gap-2">
                        <i class="fa-solid fa-truck-fast text-emerald-400 text-base"></i>
                        <span>Express Delivery</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <i class="fa-solid fa-shield-check text-emerald-400 text-base"></i>
                        <span>100% Quality Guarantee</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <i class="fa-solid fa-user-check text-emerald-400 text-base"></i>
                        <span>Guest Checkout Allowed</span>
                    </div>
                </div>
            </div>

            <!-- Hero Feature Cards Preview -->
            <div class="relative">
                <div class="relative bg-slate-900 border border-slate-800 rounded-3xl p-6 shadow-2xl space-y-4">
                    <div class="flex items-center justify-between border-b border-slate-800 pb-4">
                        <span class="text-xs font-bold uppercase tracking-wider text-emerald-400">🔥 Trending Bundle Offer</span>
                        <span class="bg-rose-500/10 text-rose-400 border border-rose-500/20 px-2.5 py-1 rounded-full text-[10px] font-bold">SAVE $11.00</span>
                    </div>

                    <div class="flex items-center gap-4">
                        <img src="https://images.unsplash.com/photo-1542838132-92c53300491e?auto=format&fit=crop&w=600&q=80" class="w-24 h-24 object-cover rounded-2xl bg-slate-950">
                        <div>
                            <h3 class="font-bold text-white text-lg">Monthly Family Repurchase Essentials</h3>
                            <p class="text-xs text-slate-400 mt-1">Includes 5kg Korean Rice, Olive Oil, Roasted Seaweed + Free Green Tea Sachet.</p>
                            <div class="flex items-center gap-3 mt-3">
                                <span class="text-emerald-400 font-extrabold text-xl">$49.99</span>
                                <span class="text-xs line-through text-slate-500">$60.99</span>
                            </div>
                        </div>
                    </div>

                    <a href="/package.php?id=1" class="w-full bg-slate-800 hover:bg-slate-700 text-slate-200 hover:text-white font-semibold py-2.5 rounded-xl transition text-xs flex items-center justify-center gap-2 border border-slate-700">
                        <span>View Included Bundle Items</span>
                        <i class="fa-solid fa-arrow-right"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Purchase Type Tabs Selector -->
<section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 -mt-6 relative z-20">
    <div class="bg-slate-900 border border-slate-800 rounded-2xl p-4 shadow-xl grid grid-cols-1 md:grid-cols-2 gap-4">

        <a href="/shop.php?purchase_type=REPURCHASE" class="group bg-slate-950/80 hover:bg-slate-950 p-5 rounded-xl border border-purple-500/20 hover:border-purple-500/50 transition flex items-center justify-between">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl bg-purple-500/10 text-purple-400 border border-purple-500/20 flex items-center justify-center text-xl group-hover:scale-110 transition">
                    <i class="fa-solid fa-arrows-rotate"></i>
                </div>
                <div>
                    <h3 class="font-bold text-white text-base">Repurchase Category</h3>
                    <p class="text-xs text-slate-400 mt-0.5">Everyday staples: Rice, Oils, Teas, Snacks & Consumables</p>
                </div>
            </div>
            <i class="fa-solid fa-chevron-right text-slate-500 group-hover:text-purple-400 transition"></i>
        </a>

        <a href="/shop.php?purchase_type=PURCHASE" class="group bg-slate-950/80 hover:bg-slate-950 p-5 rounded-xl border border-blue-500/20 hover:border-blue-500/50 transition flex items-center justify-between">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl bg-blue-500/10 text-blue-400 border border-blue-500/20 flex items-center justify-center text-xl group-hover:scale-110 transition">
                    <i class="fa-solid fa-bag-shopping"></i>
                </div>
                <div>
                    <h3 class="font-bold text-white text-base">Purchase Category</h3>
                    <p class="text-xs text-slate-400 mt-0.5">One-time investments: Cookware, Air Purifiers & Appliances</p>
                </div>
            </div>
            <i class="fa-solid fa-chevron-right text-slate-500 group-hover:text-blue-400 transition"></i>
        </a>

    </div>
</section>

<!-- Promotional Package Bundles Grid -->
<section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
    <div class="flex items-center justify-between mb-8">
        <div>
            <div class="flex items-center gap-2 text-xs font-bold uppercase tracking-wider text-amber-400">
                <i class="fa-solid fa-gift"></i> Special Combos
            </div>
            <h2 class="text-2xl sm:text-3xl font-extrabold text-white">Curated Package Bundles</h2>
        </div>
        <a href="/shop.php?type=package" class="text-xs font-semibold text-emerald-400 hover:text-emerald-300 transition flex items-center gap-1">
            See All Packages <i class="fa-solid fa-arrow-right text-[10px]"></i>
        </a>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
        <?php foreach ($packages as $pkg): ?>
            <?php
            $isLiked = in_array('PACKAGE_' . $pkg['id'], $wishlistSet);
            // Fetch items inside this package
            $itemStmt = $pdo->prepare("SELECT pi.*, p.name as product_name FROM package_items pi LEFT JOIN products p ON pi.product_id = p.id WHERE pi.package_id = ?");
            $itemStmt->execute([$pkg['id']]);
            $pkgItems = $itemStmt->fetchAll();
            ?>
            <div class="bg-slate-900 border border-slate-800 rounded-3xl p-6 hover:border-emerald-500/50 transition group flex flex-col justify-between">
                <div>
                    <div class="relative overflow-hidden rounded-2xl mb-5">
                        <img src="<?= htmlspecialchars($pkg['image_url']) ?>" class="w-full h-56 object-cover group-hover:scale-105 transition duration-500 bg-slate-950">

                        <div class="absolute top-3 left-3 bg-slate-950/80 backdrop-blur-md px-3 py-1 rounded-full text-xs font-bold text-amber-400 border border-amber-500/20">
                            BUNDLE DEAL
                        </div>

                        <!-- Wishlist Toggle -->
                        <button onclick="toggleWishlist('PACKAGE', <?= $pkg['id'] ?>, this)" class="absolute top-3 right-3 w-10 h-10 rounded-full bg-slate-950/80 backdrop-blur-md flex items-center justify-center <?= $isLiked ? 'text-rose-500' : 'text-slate-400' ?> hover:text-rose-500 transition border border-slate-800">
                            <i class="<?= $isLiked ? 'fa-solid' : 'fa-regular' ?> fa-heart text-base"></i>
                        </button>
                    </div>

                    <h3 class="text-xl font-bold text-white group-hover:text-emerald-400 transition mb-2">
                        <a href="/package.php?id=<?= $pkg['id'] ?>"><?= htmlspecialchars($pkg['name']) ?></a>
                    </h3>

                    <p class="text-xs text-slate-400 mb-4 line-clamp-2"><?= htmlspecialchars($pkg['description']) ?></p>

                    <!-- Items Included -->
                    <div class="bg-slate-950/80 rounded-xl p-3 border border-slate-800 space-y-1.5 mb-6">
                        <span class="text-[10px] font-bold uppercase tracking-wider text-slate-500 block">Package Box Includes:</span>
                        <?php foreach ($pkgItems as $pi): ?>
                            <div class="text-xs text-slate-300 flex items-center justify-between">
                                <span class="truncate">
                                    <i class="fa-solid fa-check text-emerald-400 text-[10px] mr-1.5"></i>
                                    <?= htmlspecialchars($pi['product_name'] ?? $pi['item_name']) ?>
                                </span>
                                <span class="text-[11px] font-semibold text-slate-400">x<?= $pi['quantity'] ?></span>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>

                <div class="flex items-center justify-between pt-4 border-t border-slate-800">
                    <div>
                        <span class="text-2xl font-black text-emerald-400"><?= format_price($pkg['discount_price'] > 0 ? $pkg['discount_price'] : $pkg['price']) ?></span>
                        <?php if ($pkg['discount_price'] > 0): ?>
                            <span class="text-xs line-through text-slate-500 ml-2"><?= format_price($pkg['price']) ?></span>
                        <?php endif; ?>
                    </div>

                    <button onclick="addToCart('PACKAGE', <?= $pkg['id'] ?>)" class="bg-emerald-500 hover:bg-emerald-600 text-slate-950 font-bold px-5 py-2.5 rounded-xl transition text-xs flex items-center gap-2 shadow-lg shadow-emerald-500/20">
                        <i class="fa-solid fa-cart-plus text-sm"></i> Add Bundle To Cart
                    </button>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</section>

<!-- Daily Repurchase Products Grid -->
<section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <div class="flex items-center justify-between mb-8">
        <div>
            <div class="flex items-center gap-2 text-xs font-bold uppercase tracking-wider text-purple-400">
                <i class="fa-solid fa-arrows-rotate"></i> Pantry Replenishment
            </div>
            <h2 class="text-2xl sm:text-3xl font-extrabold text-white">Daily Repurchase Essentials</h2>
        </div>
        <a href="/shop.php?purchase_type=REPURCHASE" class="text-xs font-semibold text-emerald-400 hover:text-emerald-300 transition flex items-center gap-1">
            Browse All Repurchase Items <i class="fa-solid fa-arrow-right text-[10px]"></i>
        </a>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
        <?php foreach ($repurchaseProducts as $p): ?>
            <?php
            $isLiked = in_array('PRODUCT_' . $p['id'], $wishlistSet);
            ?>
            <div class="bg-slate-900 border border-slate-800 rounded-2xl p-4 hover:border-emerald-500/40 transition group flex flex-col justify-between">
                <div>
                    <div class="relative overflow-hidden rounded-xl mb-4">
                        <img src="<?= htmlspecialchars($p['image_url']) ?>" class="w-full h-48 object-cover group-hover:scale-105 transition duration-500 bg-slate-950">

                        <span class="absolute top-2 left-2 bg-purple-500/20 text-purple-300 border border-purple-500/30 px-2 py-0.5 rounded-md text-[10px] font-bold">
                            REPURCHASE
                        </span>

                        <button onclick="toggleWishlist('PRODUCT', <?= $p['id'] ?>, this)" class="absolute top-2 right-2 w-8 h-8 rounded-full bg-slate-950/80 backdrop-blur-md flex items-center justify-center <?= $isLiked ? 'text-rose-500' : 'text-slate-400' ?> hover:text-rose-500 transition border border-slate-800 text-xs">
                            <i class="<?= $isLiked ? 'fa-solid' : 'fa-regular' ?> fa-heart"></i>
                        </button>
                    </div>

                    <div class="text-[10px] uppercase font-bold text-slate-500 mb-1"><?= htmlspecialchars($p['category_name'] ?? 'Consumables') ?></div>
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
</section>

<!-- One-Time Purchase Products Grid -->
<section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <div class="flex items-center justify-between mb-8">
        <div>
            <div class="flex items-center gap-2 text-xs font-bold uppercase tracking-wider text-blue-400">
                <i class="fa-solid fa-bag-shopping"></i> Kitchenware & Appliances
            </div>
            <h2 class="text-2xl sm:text-3xl font-extrabold text-white">One-Time Household Purchases</h2>
        </div>
        <a href="/shop.php?purchase_type=PURCHASE" class="text-xs font-semibold text-emerald-400 hover:text-emerald-300 transition flex items-center gap-1">
            Browse All Household Purchases <i class="fa-solid fa-arrow-right text-[10px]"></i>
        </a>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
        <?php foreach ($purchaseProducts as $p): ?>
            <?php
            $isLiked = in_array('PRODUCT_' . $p['id'], $wishlistSet);
            ?>
            <div class="bg-slate-900 border border-slate-800 rounded-2xl p-4 hover:border-emerald-500/40 transition group flex flex-col justify-between">
                <div>
                    <div class="relative overflow-hidden rounded-xl mb-4">
                        <img src="<?= htmlspecialchars($p['image_url']) ?>" class="w-full h-48 object-cover group-hover:scale-105 transition duration-500 bg-slate-950">

                        <span class="absolute top-2 left-2 bg-blue-500/20 text-blue-300 border border-blue-500/30 px-2 py-0.5 rounded-md text-[10px] font-bold">
                            PURCHASE
                        </span>

                        <button onclick="toggleWishlist('PRODUCT', <?= $p['id'] ?>, this)" class="absolute top-2 right-2 w-8 h-8 rounded-full bg-slate-950/80 backdrop-blur-md flex items-center justify-center <?= $isLiked ? 'text-rose-500' : 'text-slate-400' ?> hover:text-rose-500 transition border border-slate-800 text-xs">
                            <i class="<?= $isLiked ? 'fa-solid' : 'fa-regular' ?> fa-heart"></i>
                        </button>
                    </div>

                    <div class="text-[10px] uppercase font-bold text-slate-500 mb-1"><?= htmlspecialchars($p['category_name'] ?? 'Home Care') ?></div>
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
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
