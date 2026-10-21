<?php
// includes/header.php
require_once __DIR__ . '/functions.php';

$cartDetails = get_cart_details();
$cartCount = $cartDetails['count'];
$currentUser = get_logged_in_user();

// Fetch categories for navbar navigation
$pdo = get_db_connection();
$navCategories = $pdo->query("SELECT * FROM categories ORDER BY name ASC")->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle ?? 'Giveoraemart - Quality Groceries & Lifestyle Store') ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        [x-cloak] { display: none !important; }
    </style>
</head>
<body class="bg-slate-950 text-slate-100 min-h-screen flex flex-col font-sans">

    <!-- Top Announcement Bar -->
    <div class="bg-gradient-to-r from-emerald-600 to-teal-600 text-slate-950 text-xs font-bold py-2 px-4 text-center tracking-wide">
        ⚡ FREE SHIPPING ON ALL REPURCHASE BUNDLES OVER $50 • FAST SAME-DAY DISPATCH ⚡
    </div>

    <!-- Main Navigation Bar -->
    <header class="bg-slate-900/90 backdrop-blur-md border-b border-slate-800 sticky top-0 z-40">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-3.5">
            <div class="flex items-center justify-between gap-4">

                <!-- Logo -->
                <a href="/" class="flex items-center gap-3 group">
                    <div class="w-11 h-11 rounded-2xl bg-emerald-500 text-slate-950 flex items-center justify-center font-black text-2xl shadow-lg shadow-emerald-500/20 group-hover:scale-105 transition">
                        G
                    </div>
                    <div>
                        <span class="font-extrabold text-xl tracking-tight text-white group-hover:text-emerald-400 transition">Giveoraemart</span>
                        <span class="text-[10px] text-emerald-400 font-semibold block -mt-1 tracking-wider uppercase">Grocery & Bundles</span>
                    </div>
                </a>

                <!-- Real-time Live Search Input -->
                <div class="hidden md:flex flex-1 max-w-md mx-6 relative">
                    <div class="relative w-full">
                        <i class="fa-solid fa-magnifying-glass absolute left-4 top-3.5 text-slate-400 text-sm"></i>
                        <input type="text" id="store-search-input" placeholder="Search rice, cookware, packages..."
                               class="w-full bg-slate-950 border border-slate-800 text-white pl-11 pr-10 py-2.5 rounded-full text-sm focus:outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 transition placeholder-slate-500">
                        <button id="search-clear-btn" class="hidden absolute right-3 top-3 text-slate-400 hover:text-white">
                            <i class="fa-solid fa-xmark text-sm"></i>
                        </button>
                    </div>

                    <!-- Search Quick Results Dropdown -->
                    <div id="search-results-dropdown" class="hidden absolute left-0 right-0 top-full mt-2 bg-slate-900 border border-slate-800 rounded-2xl shadow-2xl overflow-hidden z-50 max-h-96 overflow-y-auto">
                        <div id="search-results-list" class="p-2 space-y-1"></div>
                    </div>
                </div>

                <!-- Right Nav Actions -->
                <div class="flex items-center gap-3">

                    <!-- Purchase / Repurchase Quick Filter Nav -->
                    <div class="hidden lg:flex items-center gap-1 bg-slate-950 p-1 rounded-full border border-slate-800 text-xs">
                        <a href="/shop.php?purchase_type=PURCHASE" class="px-3 py-1.5 rounded-full font-semibold text-slate-300 hover:text-white hover:bg-slate-800 transition">Purchase</a>
                        <a href="/shop.php?purchase_type=REPURCHASE" class="px-3 py-1.5 rounded-full font-semibold text-emerald-400 hover:bg-emerald-500/10 transition">Repurchase</a>
                    </div>

                    <!-- Wishlist Button -->
                    <a href="/wishlist.php" class="relative p-2.5 text-slate-300 hover:text-rose-400 transition bg-slate-800/60 hover:bg-slate-800 rounded-full border border-slate-800" title="Wishlist">
                        <i class="fa-solid fa-heart text-base"></i>
                        <span id="wishlist-badge" class="hidden absolute -top-1 -right-1 bg-rose-500 text-white text-[10px] font-extrabold w-4 h-4 rounded-full flex items-center justify-center">0</span>
                    </a>

                    <!-- Shopping Cart Button -->
                    <a href="/cart.php" class="relative p-2.5 text-slate-300 hover:text-emerald-400 transition bg-slate-800/60 hover:bg-slate-800 rounded-full border border-slate-800 flex items-center gap-2 px-3.5" title="Shopping Cart">
                        <i class="fa-solid fa-cart-shopping text-base"></i>
                        <span id="nav-cart-count" class="bg-emerald-500 text-slate-950 text-xs font-black px-2 py-0.5 rounded-full">
                            <?= $cartCount ?>
                        </span>
                    </a>

                    <!-- User Account / Admin Button -->
                    <?php if ($currentUser): ?>
                        <div class="relative group">
                            <button class="flex items-center gap-2 p-1.5 bg-slate-800 hover:bg-slate-700 rounded-full border border-slate-700 transition">
                                <div class="w-8 h-8 rounded-full bg-emerald-500 text-slate-950 flex items-center justify-center font-bold text-sm">
                                    <?= strtoupper(substr($currentUser['name'], 0, 1)) ?>
                                </div>
                            </button>
                            <div class="absolute right-0 mt-2 w-48 bg-slate-900 border border-slate-800 rounded-xl shadow-2xl py-2 hidden group-hover:block z-50">
                                <div class="px-4 py-2 border-b border-slate-800">
                                    <p class="text-xs font-bold text-white truncate"><?= htmlspecialchars($currentUser['name']) ?></p>
                                    <p class="text-[10px] text-slate-500 truncate"><?= htmlspecialchars($currentUser['email']) ?></p>
                                </div>
                                <?php if ($currentUser['role'] === 'ADMIN'): ?>
                                    <a href="/admin/index.php" class="block px-4 py-2 text-xs font-semibold text-emerald-400 hover:bg-slate-800"><i class="fa-solid fa-user-shield mr-2"></i> Admin Portal</a>
                                <?php endif; ?>
                                <a href="/account.php" class="block px-4 py-2 text-xs text-slate-300 hover:bg-slate-800"><i class="fa-solid fa-box mr-2"></i> My Orders</a>
                                <a href="/logout.php" class="block px-4 py-2 text-xs text-rose-400 hover:bg-slate-800"><i class="fa-solid fa-right-from-bracket mr-2"></i> Logout</a>
                            </div>
                        </div>
                    <?php else: ?>
                        <a href="/login.php" class="text-xs font-semibold text-slate-300 hover:text-white bg-slate-800 hover:bg-slate-700 px-3.5 py-2 rounded-full border border-slate-700 transition hidden sm:inline-flex items-center gap-1.5">
                            <i class="fa-regular fa-user text-xs"></i> Sign In
                        </a>
                    <?php endif; ?>

                </div>
            </div>

            <!-- Categories Bar -->
            <div class="flex items-center gap-4 overflow-x-auto mt-3 pt-2 border-t border-slate-800/80 text-xs no-scrollbar">
                <a href="/shop.php" class="text-slate-300 hover:text-emerald-400 font-bold whitespace-nowrap transition">All Shop Items</a>
                <a href="/shop.php?type=package" class="text-amber-400 hover:text-amber-300 font-bold whitespace-nowrap transition flex items-center gap-1">
                    <i class="fa-solid fa-gift"></i> Special Package Bundles
                </a>
                <span class="text-slate-700">|</span>
                <?php foreach ($navCategories as $nc): ?>
                    <a href="/shop.php?category=<?= $nc['id'] ?>" class="text-slate-400 hover:text-emerald-400 font-medium whitespace-nowrap transition">
                        <?= htmlspecialchars($nc['name']) ?>
                    </a>
                <?php endforeach; ?>
            </div>

        </div>
    </header>

    <!-- Main Content Container -->
    <main class="flex-1">
