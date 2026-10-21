<?php
// admin/matrix_tree.php
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/auth_check.php';
require_once __DIR__ . '/../includes/matrix_engine.php';

$pageTitle = '3-Matrix Downline Tree';
$activeTab = 'matrix_tree';
$pdo = get_db_connection();

// Root user selection (defaults to ID 1: givoratraders)
$rootId = isset($_GET['root_id']) ? (int)$_GET['root_id'] : 1;
$treeDepth = isset($_GET['depth']) ? min(5, max(2, (int)$_GET['depth'])) : 3;

// Fetch root user info
$rootUser = $pdo->prepare("SELECT id, name, email FROM users WHERE id = ?");
$rootUser->execute([$rootId]);
$rootUserData = $rootUser->fetch();

if (!$rootUserData) {
    $rootId = 1;
    $rootUser->execute([1]);
    $rootUserData = $rootUser->fetch();
}

$matrixTree = get_matrix_tree_data($rootId, $treeDepth);

require_once __DIR__ . '/../includes/admin_header.php';
?>

<div class="mb-8 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
    <div>
        <h1 class="text-3xl font-extrabold text-white tracking-tight">3-Matrix Downline Visual Tree</h1>
        <p class="text-slate-400 text-sm mt-1">Interactive Breadth-First Search (BFS) auto-spillover matrix structure.</p>
    </div>

    <!-- Actions & Quick Jump -->
    <div class="flex items-center gap-3">
        <?php if ($rootId != 1): ?>
            <a href="/admin/matrix_tree.php" class="bg-slate-800 hover:bg-slate-700 text-slate-200 text-xs font-semibold px-3 py-2 rounded-xl transition border border-slate-700">
                <i class="fa-solid fa-rotate-left mr-1"></i> Reset to Apex (givoratraders)
            </a>
        <?php endif; ?>

        <!-- Depth Selector -->
        <div class="flex items-center gap-1 bg-slate-800 p-1 rounded-xl border border-slate-700 text-xs">
            <span class="text-slate-400 font-semibold px-2">Levels:</span>
            <a href="/admin/matrix_tree.php?root_id=<?= $rootId ?>&depth=2" class="px-2.5 py-1 rounded-lg transition <?= $treeDepth == 2 ? 'bg-purple-500 text-white font-bold' : 'text-slate-400 hover:text-white' ?>">2</a>
            <a href="/admin/matrix_tree.php?root_id=<?= $rootId ?>&depth=3" class="px-2.5 py-1 rounded-lg transition <?= $treeDepth == 3 ? 'bg-purple-500 text-white font-bold' : 'text-slate-400 hover:text-white' ?>">3</a>
            <a href="/admin/matrix_tree.php?root_id=<?= $rootId ?>&depth=4" class="px-2.5 py-1 rounded-lg transition <?= $treeDepth == 4 ? 'bg-purple-500 text-white font-bold' : 'text-slate-400 hover:text-white' ?>">4</a>
        </div>
    </div>
</div>

<!-- Search Member Root Input -->
<div class="bg-slate-800/80 border border-slate-700/60 rounded-2xl p-4 mb-8 flex flex-col sm:flex-row items-center justify-between gap-4">
    <form method="GET" class="flex items-center gap-2 w-full sm:w-auto">
        <input type="hidden" name="depth" value="<?= $treeDepth ?>">
        <label class="text-xs font-semibold uppercase text-slate-400 whitespace-nowrap">Focus Member ID:</label>
        <input type="number" name="root_id" value="<?= $rootId ?>" placeholder="e.g. 1" required class="bg-slate-900 border border-slate-700 rounded-xl px-3 py-1.5 text-xs text-white focus:outline-none focus:border-purple-500 w-24">
        <button type="submit" class="bg-purple-500 hover:bg-purple-600 text-white text-xs font-bold px-4 py-1.5 rounded-xl transition">View Sub-Tree</button>
    </form>

    <div class="text-xs text-slate-400 flex items-center gap-4">
        <span><span class="w-2.5 h-2.5 rounded-full bg-amber-400 inline-block mr-1"></span> Apex Root</span>
        <span><span class="w-2.5 h-2.5 rounded-full bg-emerald-400 inline-block mr-1"></span> Qualified (3+ Refs)</span>
        <span><span class="w-2.5 h-2.5 rounded-full bg-rose-400 inline-block mr-1"></span> Unqualified</span>
    </div>
</div>

<!-- Visual Tree Display Canvas -->
<div class="bg-slate-800/80 border border-slate-700/60 rounded-2xl p-6 overflow-x-auto min-h-[500px]">
    <div class="min-w-[900px] flex justify-center py-6">
        <?php if ($matrixTree): ?>
            <?= render_matrix_tree_html($matrixTree, 1, $treeDepth) ?>
        <?php else: ?>
            <div class="text-center py-16 text-slate-500 text-sm">No matrix data found for User #<?= $rootId ?></div>
        <?php endif; ?>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/admin_footer.php'; ?>
