<?php
// includes/admin_header.php
require_once __DIR__ . '/../admin/auth_check.php';

$pageTitle = $pageTitle ?? 'Admin Dashboard';
$activeTab = $activeTab ?? 'dashboard';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle) ?> - Admin Portal</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-slate-900 text-slate-100 min-h-screen flex flex-col md:flex-row">

    <!-- Sidebar -->
    <aside class="w-full md:w-64 bg-slate-950 border-r border-slate-800 flex-shrink-0 flex flex-col justify-between">
        <div>
            <!-- Header Brand -->
            <div class="p-6 border-b border-slate-800 flex items-center justify-between">
                <a href="/admin/index.php" class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-emerald-500 text-slate-950 flex items-center justify-center font-bold text-xl shadow-lg shadow-emerald-500/20">
                        G
                    </div>
                    <div>
                        <h2 class="font-bold text-white text-lg leading-none">Giveoraemart</h2>
                        <span class="text-xs text-emerald-400 font-medium">Management Suite</span>
                    </div>
                </a>
            </div>

            <!-- Navigation Links -->
            <nav class="p-4 space-y-1">
                <a href="/admin/index.php" class="flex items-center gap-3 px-4 py-3 rounded-xl font-medium transition <?= $activeTab === 'dashboard' ? 'bg-emerald-500/10 text-emerald-400 border border-emerald-500/20' : 'text-slate-400 hover:bg-slate-900 hover:text-slate-200' ?>">
                    <i class="fa-solid fa-chart-pie w-5"></i>
                    <span>Dashboard</span>
                </a>
                <a href="/admin/categories.php" class="flex items-center gap-3 px-4 py-3 rounded-xl font-medium transition <?= $activeTab === 'categories' ? 'bg-emerald-500/10 text-emerald-400 border border-emerald-500/20' : 'text-slate-400 hover:bg-slate-900 hover:text-slate-200' ?>">
                    <i class="fa-solid fa-layer-group w-5"></i>
                    <span>Categories</span>
                </a>
                <a href="/admin/products.php" class="flex items-center gap-3 px-4 py-3 rounded-xl font-medium transition <?= $activeTab === 'products' ? 'bg-emerald-500/10 text-emerald-400 border border-emerald-500/20' : 'text-slate-400 hover:bg-slate-900 hover:text-slate-200' ?>">
                    <i class="fa-solid fa-boxes-stacked w-5"></i>
                    <span>Products</span>
                </a>
                <a href="/admin/packages.php" class="flex items-center gap-3 px-4 py-3 rounded-xl font-medium transition <?= $activeTab === 'packages' ? 'bg-emerald-500/10 text-emerald-400 border border-emerald-500/20' : 'text-slate-400 hover:bg-slate-900 hover:text-slate-200' ?>">
                    <i class="fa-solid fa-box-open w-5"></i>
                    <span>Packages & Bundles</span>
                </a>
                <a href="/admin/orders.php" class="flex items-center gap-3 px-4 py-3 rounded-xl font-medium transition <?= $activeTab === 'orders' ? 'bg-emerald-500/10 text-emerald-400 border border-emerald-500/20' : 'text-slate-400 hover:bg-slate-900 hover:text-slate-200' ?>">
                    <i class="fa-solid fa-receipt w-5"></i>
                    <span>Orders</span>
                </a>
            </nav>
        </div>

        <!-- Footer / User & Storefront Link -->
        <div class="p-4 border-t border-slate-800 space-y-3">
            <a href="/" target="_blank" class="flex items-center justify-between px-4 py-2.5 rounded-xl text-xs font-semibold bg-slate-900 text-slate-300 hover:bg-slate-800 transition border border-slate-800">
                <span class="flex items-center gap-2"><i class="fa-solid fa-store text-emerald-400"></i> View Front Store</span>
                <i class="fa-solid fa-arrow-up-right-from-square text-slate-500"></i>
            </a>

            <div class="flex items-center justify-between px-4 py-3 bg-slate-900/50 rounded-xl border border-slate-800">
                <div class="flex items-center gap-3 overflow-hidden">
                    <div class="w-8 h-8 rounded-full bg-slate-800 flex items-center justify-center font-bold text-slate-300 text-xs">
                        <?= strtoupper(substr($_SESSION['user']['name'] ?? 'A', 0, 1)) ?>
                    </div>
                    <div class="truncate">
                        <p class="text-xs font-semibold text-white truncate"><?= htmlspecialchars($_SESSION['user']['name'] ?? 'Admin') ?></p>
                        <p class="text-[10px] text-slate-500 truncate"><?= htmlspecialchars($_SESSION['user']['email'] ?? '') ?></p>
                    </div>
                </div>
                <a href="/admin/logout.php" title="Logout" class="text-slate-500 hover:text-rose-400 p-2 transition">
                    <i class="fa-solid fa-right-from-bracket"></i>
                </a>
            </div>
        </div>
    </aside>

    <!-- Main Content Area -->
    <main class="flex-1 p-6 md:p-10 overflow-y-auto min-h-screen">
