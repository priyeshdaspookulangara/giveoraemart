<?php
// admin/packages.php
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/auth_check.php';

$pageTitle = 'Package & Bundle Management';
$activeTab = 'packages';
$pdo = get_db_connection();

$message = '';
$error = '';

// Handle Package Creation / Update / Deletion
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = sanitize($_POST['action'] ?? '');

    if ($action === 'save_package') {
        $id = (int)($_POST['id'] ?? 0);
        $name = sanitize($_POST['name'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $price = (float)($_POST['price'] ?? 0);
        $discount_price = (float)($_POST['discount_price'] ?? 0);

        // Image Handling
        $imageUrl = sanitize($_POST['existing_image_url'] ?? '');
        $uploadedImage = handle_image_upload('image_file');
        if ($uploadedImage) {
            $imageUrl = $uploadedImage;
        } elseif (!empty($_POST['image_url'])) {
            $imageUrl = sanitize($_POST['image_url']);
        }

        // Package Items Data
        $catalogProducts = $_POST['catalog_products'] ?? []; // Array of product IDs
        $catalogQuantities = $_POST['catalog_quantities'] ?? [];
        $inlineNames = $_POST['inline_names'] ?? []; // Array of inline item strings
        $inlineQuantities = $_POST['inline_quantities'] ?? [];

        if (!empty($name) && $price >= 0) {
            $pdo->beginTransaction();
            try {
                if ($id > 0) {
                    $stmt = $pdo->prepare("UPDATE packages SET name = ?, description = ?, price = ?, discount_price = ?, image_url = ? WHERE id = ?");
                    $stmt->execute([$name, $description, $price, $discount_price, $imageUrl, $id]);
                    $pkgId = $id;
                } else {
                    $stmt = $pdo->prepare("INSERT INTO packages (name, description, price, discount_price, image_url) VALUES (?, ?, ?, ?, ?)");
                    $stmt->execute([$name, $description, $price, $discount_price, $imageUrl]);
                    $pkgId = $pdo->lastInsertId();
                }

                // Delete existing package items and re-insert
                $delStmt = $pdo->prepare("DELETE FROM package_items WHERE package_id = ?");
                $delStmt->execute([$pkgId]);

                $itemStmt = $pdo->prepare("INSERT INTO package_items (package_id, product_id, item_name, quantity) VALUES (?, ?, ?, ?)");

                // Insert catalog product items
                if (is_array($catalogProducts)) {
                    foreach ($catalogProducts as $index => $prodId) {
                        $prodId = (int)$prodId;
                        $qty = max(1, (int)($catalogQuantities[$index] ?? 1));
                        if ($prodId > 0) {
                            $itemStmt->execute([$pkgId, $prodId, null, $qty]);
                        }
                    }
                }

                // Insert ad-hoc inline items
                if (is_array($inlineNames)) {
                    foreach ($inlineNames as $index => $iName) {
                        $iName = sanitize($iName);
                        $qty = max(1, (int)($inlineQuantities[$index] ?? 1));
                        if (!empty($iName)) {
                            $itemStmt->execute([$pkgId, null, $iName, $qty]);
                        }
                    }
                }

                $pdo->commit();
                $message = "Package bundle saved successfully!";
            } catch (Exception $e) {
                $pdo->rollBack();
                $error = "Failed to save package: " . $e->getMessage();
            }
        } else {
            $error = "Package name and regular price are required.";
        }
    } elseif ($action === 'delete_package') {
        $id = (int)($_POST['id'] ?? 0);
        if ($id > 0) {
            $stmt = $pdo->prepare("DELETE FROM packages WHERE id = ?");
            $stmt->execute([$id]);
            $message = "Package bundle deleted successfully.";
        }
    }
}

// Fetch all packages with item count
$packages = $pdo->query("SELECT p.*, COUNT(pi.id) as item_count FROM packages p LEFT JOIN package_items pi ON p.id = pi.package_id GROUP BY p.id ORDER BY p.id DESC")->fetchAll();

// Fetch catalog products for multi-select inclusion
$catalogProductsList = $pdo->query("SELECT id, name, price FROM products ORDER BY name ASC")->fetchAll();

// Load Edit Package if edit parameter supplied
$editPkg = null;
$editItems = [];
if (isset($_GET['edit'])) {
    $editId = (int)$_GET['edit'];
    $stmt = $pdo->prepare("SELECT * FROM packages WHERE id = ?");
    $stmt->execute([$editId]);
    $editPkg = $stmt->fetch();

    if ($editPkg) {
        $itemStmt = $pdo->prepare("SELECT pi.*, p.name as product_name FROM package_items pi LEFT JOIN products p ON pi.product_id = p.id WHERE pi.package_id = ?");
        $itemStmt->execute([$editId]);
        $editItems = $itemStmt->fetchAll();
    }
}

require_once __DIR__ . '/../includes/admin_header.php';
?>

<div class="mb-8 flex items-center justify-between">
    <div>
        <h1 class="text-3xl font-extrabold text-white tracking-tight">Package & Bundle Deals</h1>
        <p class="text-slate-400 text-sm mt-1">Combine existing catalog products with new inline items to offer discounted bundle kits.</p>
    </div>
    <?php if ($editPkg): ?>
        <a href="/admin/packages.php" class="bg-slate-800 hover:bg-slate-700 text-white font-semibold px-4 py-2 rounded-xl transition text-sm">
            <i class="fa-solid fa-plus"></i> Create New Package
        </a>
    <?php endif; ?>
</div>

<?php if ($message): ?>
    <div class="mb-6 p-4 bg-emerald-500/10 border border-emerald-500/30 text-emerald-400 text-sm rounded-xl flex items-center gap-3">
        <i class="fa-solid fa-circle-check text-base"></i>
        <span><?= $message ?></span>
    </div>
<?php endif; ?>

<?php if ($error): ?>
    <div class="mb-6 p-4 bg-rose-500/10 border border-rose-500/30 text-rose-400 text-sm rounded-xl flex items-center gap-3">
        <i class="fa-solid fa-circle-exclamation text-base"></i>
        <span><?= $error ?></span>
    </div>
<?php endif; ?>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
    <!-- Form Side -->
    <div class="bg-slate-800/80 border border-slate-700/60 rounded-2xl p-6 h-fit">
        <h2 class="text-lg font-bold text-white mb-4 flex items-center gap-2">
            <i class="fa-solid <?= $editPkg ? 'fa-pen-to-square text-amber-400' : 'fa-box-open text-emerald-400' ?>"></i>
            <?= $editPkg ? 'Edit Package #' . $editPkg['id'] : 'Create Package Bundle' ?>
        </h2>

        <form method="POST" enctype="multipart/form-data" class="space-y-4">
            <input type="hidden" name="action" value="save_package">
            <input type="hidden" name="id" value="<?= $editPkg['id'] ?? 0 ?>">
            <input type="hidden" name="existing_image_url" value="<?= htmlspecialchars($editPkg['image_url'] ?? '') ?>">

            <div>
                <label class="block text-xs font-semibold uppercase tracking-wider text-slate-400 mb-1">Package Title *</label>
                <input type="text" name="name" value="<?= htmlspecialchars($editPkg['name'] ?? '') ?>" required placeholder="e.g. Monthly Pantry Essentials Pack"
                       class="w-full bg-slate-900 border border-slate-700 rounded-xl px-4 py-2.5 text-white placeholder-slate-500 focus:outline-none focus:border-emerald-500 text-sm">
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-slate-400 mb-1">Original Price ($)</label>
                    <input type="number" step="0.01" name="price" value="<?= $editPkg['price'] ?? '' ?>" required placeholder="65.00"
                           class="w-full bg-slate-900 border border-slate-700 rounded-xl px-4 py-2.5 text-white placeholder-slate-500 focus:outline-none focus:border-emerald-500 text-sm">
                </div>
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-slate-400 mb-1">Discount Price ($)</label>
                    <input type="number" step="0.01" name="discount_price" value="<?= $editPkg['discount_price'] ?? '' ?>" placeholder="49.99"
                           class="w-full bg-slate-900 border border-slate-700 rounded-xl px-4 py-2.5 text-white placeholder-slate-500 focus:outline-none focus:border-emerald-500 text-sm">
                </div>
            </div>

            <div>
                <label class="block text-xs font-semibold uppercase tracking-wider text-slate-400 mb-1">Description</label>
                <textarea name="description" rows="2" placeholder="Describe the contents or promotional savings..."
                          class="w-full bg-slate-900 border border-slate-700 rounded-xl px-4 py-2 text-white placeholder-slate-500 focus:outline-none focus:border-emerald-500 text-sm"><?= htmlspecialchars($editPkg['description'] ?? '') ?></textarea>
            </div>

            <!-- Package Image -->
            <div>
                <label class="block text-xs font-semibold uppercase tracking-wider text-slate-400 mb-1">Bundle Cover Image</label>
                <input type="file" name="image_file" accept="image/*" class="w-full text-xs text-slate-400 file:mr-3 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-slate-700 file:text-slate-200 hover:file:bg-slate-600 mb-2">
                <input type="text" name="image_url" value="<?= htmlspecialchars($editPkg['image_url'] ?? '') ?>" placeholder="https://..."
                       class="w-full bg-slate-900 border border-slate-700 rounded-xl px-4 py-2 text-white placeholder-slate-500 focus:outline-none focus:border-emerald-500 text-xs">
            </div>

            <hr class="border-slate-700 my-4">

            <!-- Dynamic Package Contents Builder -->
            <div>
                <div class="flex items-center justify-between mb-2">
                    <label class="block text-xs font-bold uppercase tracking-wider text-emerald-400">Package Included Items</label>
                </div>

                <!-- Catalog Products Container -->
                <div class="space-y-2 mb-3" id="catalog-items-container">
                    <p class="text-[11px] text-slate-400">Catalog Products Included:</p>
                    <?php
                    $catalogItemFound = false;
                    foreach ($editItems as $item) {
                        if ($item['product_id']) {
                            $catalogItemFound = true;
                            ?>
                            <div class="flex items-center gap-2 catalog-row">
                                <select name="catalog_products[]" class="flex-1 bg-slate-900 border border-slate-700 rounded-lg px-3 py-1.5 text-xs text-white">
                                    <option value="">-- Choose Product --</option>
                                    <?php foreach ($catalogProductsList as $prod): ?>
                                        <option value="<?= $prod['id'] ?>" <?= $item['product_id'] == $prod['id'] ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($prod['name']) ?> ($<?= $prod['price'] ?>)
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <input type="number" name="catalog_quantities[]" value="<?= $item['quantity'] ?>" min="1" class="w-16 bg-slate-900 border border-slate-700 rounded-lg px-2 py-1.5 text-xs text-white text-center">
                                <button type="button" onclick="this.parentElement.remove()" class="text-rose-400 hover:text-rose-300 p-1"><i class="fa-solid fa-xmark"></i></button>
                            </div>
                            <?php
                        }
                    }
                    if (!$catalogItemFound):
                    ?>
                        <div class="flex items-center gap-2 catalog-row">
                            <select name="catalog_products[]" class="flex-1 bg-slate-900 border border-slate-700 rounded-lg px-3 py-1.5 text-xs text-white">
                                <option value="">-- Choose Product --</option>
                                <?php foreach ($catalogProductsList as $prod): ?>
                                    <option value="<?= $prod['id'] ?>"><?= htmlspecialchars($prod['name']) ?> ($<?= $prod['price'] ?>)</option>
                                <?php endforeach; ?>
                            </select>
                            <input type="number" name="catalog_quantities[]" value="1" min="1" class="w-16 bg-slate-900 border border-slate-700 rounded-lg px-2 py-1.5 text-xs text-white text-center">
                            <button type="button" onclick="this.parentElement.remove()" class="text-rose-400 hover:text-rose-300 p-1"><i class="fa-solid fa-xmark"></i></button>
                        </div>
                    <?php endif; ?>
                </div>

                <button type="button" onclick="addCatalogRow()" class="text-xs text-emerald-400 hover:text-emerald-300 font-semibold inline-flex items-center gap-1 mb-4">
                    <i class="fa-solid fa-plus-circle"></i> Add Another Catalog Item
                </button>

                <!-- Ad-hoc Inline Items Container -->
                <div class="space-y-2 mb-3" id="inline-items-container">
                    <p class="text-[11px] text-slate-400">Ad-Hoc / Custom Inline Items (Bonus/Gifts):</p>
                    <?php
                    $inlineItemFound = false;
                    foreach ($editItems as $item) {
                        if (!$item['product_id'] && !empty($item['item_name'])) {
                            $inlineItemFound = true;
                            ?>
                            <div class="flex items-center gap-2 inline-row">
                                <input type="text" name="inline_names[]" value="<?= htmlspecialchars($item['item_name']) ?>" placeholder="e.g. Master Chef Apron" class="flex-1 bg-slate-900 border border-slate-700 rounded-lg px-3 py-1.5 text-xs text-white">
                                <input type="number" name="inline_quantities[]" value="<?= $item['quantity'] ?>" min="1" class="w-16 bg-slate-900 border border-slate-700 rounded-lg px-2 py-1.5 text-xs text-white text-center">
                                <button type="button" onclick="this.parentElement.remove()" class="text-rose-400 hover:text-rose-300 p-1"><i class="fa-solid fa-xmark"></i></button>
                            </div>
                            <?php
                        }
                    }
                    if (!$inlineItemFound):
                    ?>
                        <div class="flex items-center gap-2 inline-row">
                            <input type="text" name="inline_names[]" placeholder="e.g. Bonus Green Tea Sachet 10s" class="flex-1 bg-slate-900 border border-slate-700 rounded-lg px-3 py-1.5 text-xs text-white">
                            <input type="number" name="inline_quantities[]" value="1" min="1" class="w-16 bg-slate-900 border border-slate-700 rounded-lg px-2 py-1.5 text-xs text-white text-center">
                            <button type="button" onclick="this.parentElement.remove()" class="text-rose-400 hover:text-rose-300 p-1"><i class="fa-solid fa-xmark"></i></button>
                        </div>
                    <?php endif; ?>
                </div>

                <button type="button" onclick="addInlineRow()" class="text-xs text-emerald-400 hover:text-emerald-300 font-semibold inline-flex items-center gap-1">
                    <i class="fa-solid fa-plus-circle"></i> Add Custom Ad-Hoc Item
                </button>
            </div>

            <button type="submit" class="w-full mt-4 bg-emerald-500 hover:bg-emerald-600 text-slate-950 font-bold py-3 px-4 rounded-xl transition text-sm flex items-center justify-center gap-2 shadow-lg shadow-emerald-500/20">
                <i class="fa-solid fa-floppy-disk"></i> <?= $editPkg ? 'Update Package Bundle' : 'Save Package Bundle' ?>
            </button>
        </form>
    </div>

    <!-- Package Table -->
    <div class="lg:col-span-2 bg-slate-800/80 border border-slate-700/60 rounded-2xl p-6">
        <h2 class="text-lg font-bold text-white mb-4 flex items-center gap-2">
            <i class="fa-solid fa-box-open text-emerald-400"></i> Promotional Package Bundles (<?= count($packages) ?>)
        </h2>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <?php foreach ($packages as $pkg): ?>
                <?php
                // Fetch items for each package
                $itemStmt = $pdo->prepare("SELECT pi.*, p.name as product_name FROM package_items pi LEFT JOIN products p ON pi.product_id = p.id WHERE pi.package_id = ?");
                $itemStmt->execute([$pkg['id']]);
                $pkgItems = $itemStmt->fetchAll();
                ?>
                <div class="bg-slate-900 border border-slate-700/80 rounded-xl p-4 flex flex-col justify-between">
                    <div>
                        <div class="flex items-start gap-3 mb-3">
                            <img src="<?= htmlspecialchars($pkg['image_url'] ?: 'https://via.placeholder.com/150') ?>" class="w-16 h-16 object-cover rounded-lg bg-slate-950 border border-slate-800 flex-shrink-0">
                            <div>
                                <h3 class="font-bold text-white text-base leading-tight"><?= htmlspecialchars($pkg['name']) ?></h3>
                                <div class="flex items-center gap-2 mt-1">
                                    <span class="text-emerald-400 font-bold text-sm"><?= format_price($pkg['discount_price'] > 0 ? $pkg['discount_price'] : $pkg['price']) ?></span>
                                    <?php if ($pkg['discount_price'] > 0): ?>
                                        <span class="text-xs line-through text-slate-500"><?= format_price($pkg['price']) ?></span>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>

                        <p class="text-xs text-slate-400 mb-3 line-clamp-2"><?= htmlspecialchars($pkg['description']) ?></p>

                        <!-- Included Items Badge List -->
                        <div class="bg-slate-950/60 rounded-lg p-2.5 border border-slate-800 space-y-1 mb-4">
                            <span class="text-[10px] font-bold uppercase tracking-wider text-slate-500 block">Bundle Contents (<?= count($pkgItems) ?>):</span>
                            <?php foreach ($pkgItems as $pi): ?>
                                <div class="text-xs text-slate-300 flex items-center justify-between">
                                    <span class="truncate">
                                        <i class="fa-solid fa-circle text-[6px] text-emerald-400 mr-1.5 align-middle"></i>
                                        <?= htmlspecialchars($pi['product_name'] ?? $pi['item_name']) ?>
                                    </span>
                                    <span class="text-[11px] font-semibold text-slate-400">x<?= $pi['quantity'] ?></span>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <div class="flex items-center justify-end gap-2 pt-2 border-t border-slate-800">
                        <a href="/admin/packages.php?edit=<?= $pkg['id'] ?>" class="text-xs font-semibold bg-slate-800 hover:bg-slate-700 text-slate-300 px-3 py-1.5 rounded-lg transition flex items-center gap-1">
                            <i class="fa-solid fa-pen-to-square"></i> Edit
                        </a>
                        <form method="POST" onsubmit="return confirm('Delete package bundle permanently?');">
                            <input type="hidden" name="action" value="delete_package">
                            <input type="hidden" name="id" value="<?= $pkg['id'] ?>">
                            <button type="submit" class="text-xs font-semibold bg-rose-500/10 hover:bg-rose-500/20 text-rose-400 border border-rose-500/20 px-3 py-1.5 rounded-lg transition flex items-center gap-1">
                                <i class="fa-solid fa-trash"></i> Delete
                            </button>
                        </form>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<script>
function addCatalogRow() {
    const container = document.getElementById('catalog-items-container');
    const firstRow = container.querySelector('.catalog-row');
    if (firstRow) {
        const newRow = firstRow.cloneNode(true);
        newRow.querySelector('select').selectedIndex = 0;
        newRow.querySelector('input[type="number"]').value = 1;
        container.appendChild(newRow);
    }
}

function addInlineRow() {
    const container = document.getElementById('inline-items-container');
    const firstRow = container.querySelector('.inline-row');
    if (firstRow) {
        const newRow = firstRow.cloneNode(true);
        newRow.querySelector('input[type="text"]').value = '';
        newRow.querySelector('input[type="number"]').value = 1;
        container.appendChild(newRow);
    }
}
</script>

<?php require_once __DIR__ . '/../includes/admin_footer.php'; ?>
