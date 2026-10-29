<?php
// index.php
require_once __DIR__ . '/includes/functions.php';

$pageTitle = 'Giveoraemart - Quality Groceries, Bundles & Welfare Schemes';
$pdo = get_db_connection();

// Fetch Categories
$categories = $pdo->query("SELECT * FROM categories ORDER BY name ASC")->fetchAll();

// Fetch Featured Products
$repurchaseProducts = $pdo->query("SELECT p.*, c.name as category_name FROM products p LEFT JOIN categories c ON p.category_id = c.id WHERE p.purchase_type = 'REPURCHASE' ORDER BY p.id DESC LIMIT 4")->fetchAll();
$purchaseProducts = $pdo->query("SELECT p.*, c.name as category_name FROM products p LEFT JOIN categories c ON p.category_id = c.id WHERE p.purchase_type = 'PURCHASE' ORDER BY p.id DESC LIMIT 4")->fetchAll();

// Fetch Packages
$packages = $pdo->query("SELECT * FROM packages ORDER BY id DESC LIMIT 2")->fetchAll();

// Fetch User Wishlist IDs
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

<!-- Modern Hero Section -->
<section class="relative bg-slate-950 pt-12 pb-20 border-b border-slate-800/80 overflow-hidden">
    <div class="absolute inset-0 bg-[radial-gradient(ellipse_at_top_right,_var(--tw-gradient-stops))] from-emerald-950/30 via-slate-950 to-slate-950"></div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 items-center">

            <div class="lg:col-span-7 space-y-6">
                <div class="inline-flex items-center gap-2.5 px-3 py-1 rounded-full bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 text-xs font-semibold tracking-wide">
                    <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span> E-Commerce & Community Welfare Platform
                </div>

                <h1 class="text-4xl sm:text-5xl lg:text-6xl font-extrabold text-white tracking-tight leading-[1.15]">
                    Quality Groceries, Combo Packages & Social Security Schemes.
                </h1>

                <p class="text-slate-400 text-base sm:text-lg max-w-2xl leading-relaxed">
                    Shop monthly pantry staples and household kits, earn matrix spillover rewards, and access comprehensive health & education insurance through our community welfare programs.
                </p>

                <div class="flex flex-wrap gap-4 pt-3">
                    <a href="/shop.php" class="bg-emerald-500 hover:bg-emerald-400 text-slate-950 font-bold px-6 py-3.5 rounded-xl transition shadow-lg shadow-emerald-500/15 flex items-center gap-2 text-sm">
                        <i class="fa-solid fa-bag-shopping"></i> Explore Marketplace
                    </a>
                    <a href="/welfare-schemes.php" class="bg-slate-900 hover:bg-slate-800 text-slate-200 font-semibold px-6 py-3.5 rounded-xl transition border border-slate-800 flex items-center gap-2 text-sm">
                        <i class="fa-solid fa-hand-holding-heart text-amber-400"></i> Welfare Schemes
                    </a>
                </div>

                <!-- Stats Bar -->
                <div class="pt-8 border-t border-slate-800/80 grid grid-cols-3 gap-6 max-w-lg">
                    <div>
                        <div class="text-2xl font-black text-white">10K+</div>
                        <div class="text-xs text-slate-400 mt-0.5">Active Members</div>
                    </div>
                    <div>
                        <div class="text-2xl font-black text-emerald-400">₹60K</div>
                        <div class="text-xs text-slate-400 mt-0.5">Charity Support / User</div>
                    </div>
                    <div>
                        <div class="text-2xl font-black text-amber-400">₹10 Lakh</div>
                        <div class="text-xs text-slate-400 mt-0.5">Max Health Cover</div>
                    </div>
                </div>
            </div>

            <!-- Hero Feature Box -->
            <div class="lg:col-span-5">
                <div class="bg-slate-900 border border-slate-800 rounded-2xl p-6 shadow-2xl relative">
                    <div class="flex items-center justify-between pb-4 mb-4 border-b border-slate-800">
                        <div class="flex items-center gap-2">
                            <span class="w-3 h-3 rounded-full bg-emerald-500"></span>
                            <span class="text-xs font-bold uppercase tracking-wider text-slate-300">Instant UPI Payment</span>
                        </div>
                        <span class="text-[11px] font-semibold text-emerald-400 bg-emerald-500/10 border border-emerald-500/20 px-2.5 py-0.5 rounded-full">QR Express</span>
                    </div>

                    <div class="space-y-4">
                        <div class="bg-slate-950 p-4 rounded-xl border border-slate-800 flex items-center justify-between">
                            <div>
                                <span class="text-[10px] font-bold text-slate-500 uppercase block">Daily Pantry Repurchase</span>
                                <h4 class="text-sm font-bold text-white mt-0.5">Grain, Edible Oils & Spices</h4>
                                <p class="text-xs text-slate-400 mt-1">Automatic wallet credit on repurchase orders.</p>
                            </div>
                            <span class="text-lg font-black text-emerald-400">$49.99</span>
                        </div>

                        <div class="bg-slate-950 p-4 rounded-xl border border-slate-800 flex items-center justify-between">
                            <div>
                                <span class="text-[10px] font-bold text-amber-400 uppercase block">Community Welfare</span>
                                <h4 class="text-sm font-bold text-white mt-0.5">Vidya Vikas & Charity Scheme</h4>
                                <p class="text-xs text-slate-400 mt-1">Insurance & family support benefits included.</p>
                            </div>
                            <span class="text-xs font-bold text-amber-400 bg-amber-500/10 px-2 py-1 rounded">Enrolled</span>
                        </div>
                    </div>

                    <div class="mt-5 pt-4 border-t border-slate-800 flex items-center justify-between text-xs text-slate-400">
                        <span><i class="fa-solid fa-shield-check text-emerald-400 mr-1.5"></i> Encrypted Transactions</span>
                        <a href="/shop.php" class="text-emerald-400 hover:underline font-semibold">Browse All <i class="fa-solid fa-arrow-right text-[10px] ml-1"></i></a>
                    </div>
                </div>
            </div>

        </div>
    </div>
</section>

<!-- Core Ecosystem Services Section -->
<section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
    <div class="mb-10 text-center max-w-2xl mx-auto">
        <span class="text-xs font-bold uppercase tracking-widest text-emerald-400">Our Services</span>
        <h2 class="text-3xl font-extrabold text-white mt-2">Everything You Need In One Ecosystem</h2>
        <p class="text-slate-400 text-sm mt-2">Combining seamless e-commerce, promotional packages, instant UPI checkout, and structured welfare support.</p>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">

        <!-- Service 1 -->
        <div class="bg-slate-900 border border-slate-800 hover:border-slate-700 p-6 rounded-2xl transition group flex flex-col justify-between">
            <div>
                <div class="w-12 h-12 rounded-xl bg-purple-500/10 border border-purple-500/20 text-purple-400 flex items-center justify-center text-xl mb-5 group-hover:scale-105 transition">
                    <i class="fa-solid fa-repeat"></i>
                </div>
                <h3 class="text-base font-bold text-white mb-2">Repurchase Grocery</h3>
                <p class="text-xs text-slate-400 leading-relaxed">Monthly pantry staples, rice, oils, and tea. Fast home delivery with automated order tracking.</p>
            </div>
            <a href="/shop.php?purchase_type=REPURCHASE" class="mt-5 text-xs font-semibold text-purple-400 hover:text-purple-300 flex items-center gap-1.5">
                Shop Repurchase <i class="fa-solid fa-arrow-right text-[10px]"></i>
            </a>
        </div>

        <!-- Service 2 -->
        <div class="bg-slate-900 border border-slate-800 hover:border-slate-700 p-6 rounded-2xl transition group flex flex-col justify-between">
            <div>
                <div class="w-12 h-12 rounded-xl bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 flex items-center justify-center text-xl mb-5 group-hover:scale-105 transition">
                    <i class="fa-solid fa-box-open"></i>
                </div>
                <h3 class="text-base font-bold text-white mb-2">Special Combo Bundles</h3>
                <p class="text-xs text-slate-400 leading-relaxed">Discounted promotional family packages packed with essential items for extra value.</p>
            </div>
            <a href="/shop.php?type=package" class="mt-5 text-xs font-semibold text-emerald-400 hover:text-emerald-300 flex items-center gap-1.5">
                View Packages <i class="fa-solid fa-arrow-right text-[10px]"></i>
            </a>
        </div>

        <!-- Service 3 -->
        <div class="bg-slate-900 border border-slate-800 hover:border-slate-700 p-6 rounded-2xl transition group flex flex-col justify-between">
            <div>
                <div class="w-12 h-12 rounded-xl bg-blue-500/10 border border-blue-500/20 text-blue-400 flex items-center justify-center text-xl mb-5 group-hover:scale-105 transition">
                    <i class="fa-solid fa-qrcode"></i>
                </div>
                <h3 class="text-base font-bold text-white mb-2">Instant UPI Payment</h3>
                <p class="text-xs text-slate-400 leading-relaxed">Seamless checkout via Google Pay, PhonePe, Paytm QR code with instant UTR confirmation.</p>
            </div>
            <a href="/checkout.php" class="mt-5 text-xs font-semibold text-blue-400 hover:text-blue-300 flex items-center gap-1.5">
                Checkout Details <i class="fa-solid fa-arrow-right text-[10px]"></i>
            </a>
        </div>

        <!-- Service 4 -->
        <div class="bg-slate-900 border border-slate-800 hover:border-slate-700 p-6 rounded-2xl transition group flex flex-col justify-between">
            <div>
                <div class="w-12 h-12 rounded-xl bg-amber-500/10 border border-amber-500/20 text-amber-400 flex items-center justify-center text-xl mb-5 group-hover:scale-105 transition">
                    <i class="fa-solid fa-sitemap"></i>
                </div>
                <h3 class="text-base font-bold text-white mb-2">Matrix & Referral Wallet</h3>
                <p class="text-xs text-slate-400 leading-relaxed">Auto-spillover referral engine across 11 levels with direct income & reserve wallet tracking.</p>
            </div>
            <a href="/account.php" class="mt-5 text-xs font-semibold text-amber-400 hover:text-amber-300 flex items-center gap-1.5">
                My Matrix Wallet <i class="fa-solid fa-arrow-right text-[10px]"></i>
            </a>
        </div>

    </div>
</section>

<!-- Dedicated Welfare Schemes Highlight Section -->
<section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <div class="bg-gradient-to-r from-slate-900 via-slate-900 to-slate-950 border border-amber-500/30 rounded-3xl p-8 sm:p-10 shadow-2xl relative overflow-hidden">
        <div class="absolute right-0 top-0 w-96 h-96 bg-amber-500/5 rounded-full filter blur-3xl pointer-events-none"></div>

        <div class="flex flex-col lg:flex-row items-start lg:items-center justify-between gap-8 mb-10 pb-8 border-b border-slate-800">
            <div class="space-y-2">
                <div class="inline-flex items-center gap-2 text-xs font-bold uppercase tracking-wider text-amber-400">
                    <i class="fa-solid fa-hand-holding-heart"></i> Community Security & Welfare
                </div>
                <h2 class="text-2xl sm:text-3xl font-extrabold text-white">Giveoraemart Welfare Schemes</h2>
                <p class="text-slate-400 text-sm max-w-2xl">Financial safety nets, health insurance, and educational support tailored for our members and families.</p>
            </div>

            <a href="/welfare-schemes.php" class="bg-amber-500 hover:bg-amber-400 text-slate-950 font-bold px-6 py-3 rounded-xl transition text-xs whitespace-nowrap shadow-lg shadow-amber-500/10 flex items-center gap-2">
                View All Scheme Details <i class="fa-solid fa-arrow-right"></i>
            </a>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">

            <!-- Scheme 1: Charity Support Scheme -->
            <div class="bg-slate-950 border border-slate-800 rounded-2xl p-6 hover:border-amber-500/40 transition">
                <div class="flex items-center justify-between mb-4">
                    <span class="text-xs font-bold text-amber-400 bg-amber-500/10 border border-amber-500/20 px-3 py-1 rounded-full">Support Tier: ₹10,000+</span>
                    <span class="text-xs text-slate-500">60 Cycles Payout</span>
                </div>

                <h3 class="text-xl font-bold text-white mb-2">1. Charity Support Scheme</h3>
                <p class="text-xs text-slate-400 mb-6 leading-relaxed">Designed to provide continuous financial relief and medical insurance protection for member families.</p>

                <div class="space-y-3 bg-slate-900/60 p-4 rounded-xl border border-slate-800/80 text-xs text-slate-300">
                    <div class="flex items-center gap-2">
                        <i class="fa-solid fa-check text-emerald-400 text-xs"></i>
                        <span><strong>₹3,000 x 60 Cycles</strong> monthly support payout</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <i class="fa-solid fa-check text-emerald-400 text-xs"></i>
                        <span>Health Insurance cover for EMIs ≥ ₹50,000</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <i class="fa-solid fa-check text-emerald-400 text-xs"></i>
                        <span><strong>₹10 Lakh Cover</strong> for Women • <strong>₹5 Lakh Cover</strong> for Men</span>
                    </div>
                </div>
            </div>

            <!-- Scheme 2: Vidya Vikas Scheme -->
            <div class="bg-slate-950 border border-slate-800 rounded-2xl p-6 hover:border-amber-500/40 transition">
                <div class="flex items-center justify-between mb-4">
                    <span class="text-xs font-bold text-emerald-400 bg-emerald-500/10 border border-emerald-500/20 px-3 py-1 rounded-full">Support Tier: ₹10,000+</span>
                    <span class="text-xs text-slate-500">Student & Family Security</span>
                </div>

                <h3 class="text-xl font-bold text-white mb-2">2. Vidya Vikas Scheme</h3>
                <p class="text-xs text-slate-400 mb-6 leading-relaxed">Dedicated education assistance, student medical safety nets, and parental accidental coverage.</p>

                <div class="space-y-3 bg-slate-900/60 p-4 rounded-xl border border-slate-800/80 text-xs text-slate-300">
                    <div class="flex items-center gap-2">
                        <i class="fa-solid fa-check text-emerald-400 text-xs"></i>
                        <span><strong>₹2 Lakh Health Insurance</strong> for active students</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <i class="fa-solid fa-check text-emerald-400 text-xs"></i>
                        <span><strong>₹50,000 Accidental Death Cover</strong> for students</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <i class="fa-solid fa-check text-emerald-400 text-xs"></i>
                        <span><strong>₹4 Lakh Accidental Death Coverage</strong> for parents</span>
                    </div>
                </div>
            </div>

        </div>
    </div>
</section>

<!-- Featured Combo Packages Section -->
<section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <div class="flex items-center justify-between mb-8">
        <div>
            <div class="flex items-center gap-2 text-xs font-bold uppercase tracking-wider text-emerald-400">
                <i class="fa-solid fa-gift"></i> Special Offers
            </div>
            <h2 class="text-2xl sm:text-3xl font-extrabold text-white">Curated Package Combos</h2>
        </div>
        <a href="/shop.php?type=package" class="text-xs font-semibold text-emerald-400 hover:text-emerald-300 transition flex items-center gap-1">
            See All Packages <i class="fa-solid fa-arrow-right text-[10px]"></i>
        </a>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
        <?php foreach ($packages as $pkg): ?>
            <?php
            $isLiked = in_array('PACKAGE_' . $pkg['id'], $wishlistSet);
            $itemStmt = $pdo->prepare("SELECT pi.*, p.name as product_name FROM package_items pi LEFT JOIN products p ON pi.product_id = p.id WHERE pi.package_id = ?");
            $itemStmt->execute([$pkg['id']]);
            $pkgItems = $itemStmt->fetchAll();
            ?>
            <div class="bg-slate-900 border border-slate-800 rounded-2xl p-6 hover:border-emerald-500/40 transition group flex flex-col justify-between">
                <div>
                    <div class="relative overflow-hidden rounded-xl mb-5">
                        <img src="<?= htmlspecialchars($pkg['image_url']) ?>" class="w-full h-56 object-cover group-hover:scale-105 transition duration-500 bg-slate-950">
                        <div class="absolute top-3 left-3 bg-slate-950/90 backdrop-blur-md px-3 py-1 rounded-full text-xs font-bold text-amber-400 border border-amber-500/20">
                            SPECIAL BUNDLE
                        </div>
                        <button onclick="toggleWishlist('PACKAGE', <?= $pkg['id'] ?>, this)" class="absolute top-3 right-3 w-9 h-9 rounded-full bg-slate-950/80 flex items-center justify-center <?= $isLiked ? 'text-rose-500' : 'text-slate-400' ?> hover:text-rose-500 transition border border-slate-800">
                            <i class="<?= $isLiked ? 'fa-solid' : 'fa-regular' ?> fa-heart text-sm"></i>
                        </button>
                    </div>

                    <h3 class="text-lg font-bold text-white group-hover:text-emerald-400 transition mb-1">
                        <a href="/package.php?id=<?= $pkg['id'] ?>"><?= htmlspecialchars($pkg['name']) ?></a>
                    </h3>
                    <p class="text-xs text-slate-400 mb-4 line-clamp-2"><?= htmlspecialchars($pkg['description']) ?></p>

                    <div class="bg-slate-950/80 rounded-xl p-3 border border-slate-800 space-y-1 mb-5">
                        <span class="text-[10px] font-bold uppercase tracking-wider text-slate-500 block">Package Box Contents:</span>
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
                    <button onclick="addToCart('PACKAGE', <?= $pkg['id'] ?>)" class="bg-emerald-500 hover:bg-emerald-400 text-slate-950 font-bold px-5 py-2.5 rounded-xl transition text-xs flex items-center gap-2">
                        <i class="fa-solid fa-cart-plus text-sm"></i> Add Bundle
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
                <i class="fa-solid fa-repeat"></i> Daily Essentials
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
            <div class="bg-slate-900 border border-slate-800 rounded-xl p-4 hover:border-emerald-500/40 transition group flex flex-col justify-between">
                <div>
                    <div class="relative overflow-hidden rounded-lg mb-3">
                        <img src="<?= htmlspecialchars($p['image_url']) ?>" class="w-full h-44 object-cover group-hover:scale-105 transition duration-500 bg-slate-950">
                        <span class="absolute top-2 left-2 bg-purple-500/20 text-purple-300 border border-purple-500/30 px-2 py-0.5 rounded text-[10px] font-bold">
                            REPURCHASE
                        </span>
                        <button onclick="toggleWishlist('PRODUCT', <?= $p['id'] ?>, this)" class="absolute top-2 right-2 w-8 h-8 rounded-full bg-slate-950/80 flex items-center justify-center <?= $isLiked ? 'text-rose-500' : 'text-slate-400' ?> hover:text-rose-500 transition border border-slate-800 text-xs">
                            <i class="<?= $isLiked ? 'fa-solid' : 'fa-regular' ?> fa-heart"></i>
                        </button>
                    </div>

                    <div class="text-[10px] uppercase font-bold text-slate-500 mb-1"><?= htmlspecialchars($p['category_name'] ?? 'Consumables') ?></div>
                    <h3 class="font-bold text-white text-sm line-clamp-1 group-hover:text-emerald-400 transition mb-2">
                        <a href="/product.php?id=<?= $p['id'] ?>"><?= htmlspecialchars($p['name']) ?></a>
                    </h3>
                </div>

                <div class="flex items-center justify-between pt-3 border-t border-slate-800 mt-2">
                    <span class="text-base font-extrabold text-emerald-400"><?= format_price($p['price']) ?></span>
                    <button onclick="addToCart('PRODUCT', <?= $p['id'] ?>)" class="bg-slate-800 hover:bg-emerald-500 text-slate-200 hover:text-slate-950 font-bold p-2.5 rounded-lg transition text-xs border border-slate-700 hover:border-emerald-500" title="Add to Cart">
                        <i class="fa-solid fa-cart-plus"></i>
                    </button>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
