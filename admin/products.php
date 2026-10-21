<?php
// admin/products.php
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/auth_check.php';

$pageTitle = 'Product Management';
$activeTab = 'products';
$pdo = get_db_connection();

$message = '';
$error = '';

// Handle Product Actions (Create, Update, Delete)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = sanitize($_POST['action'] ?? '');

    if ($action === 'save') {
        $id = (int)($_POST['id'] ?? 0);
        $name = sanitize($_POST['name'] ?? '');
        $slug = create_slug($_POST['slug'] ?? $name);
        $description = trim($_POST['description'] ?? '');
        $price = (float)($_POST['price'] ?? 0);
        $stock = (int)($_POST['stock'] ?? 0);
        $category_id = !empty($_POST['category_id']) ? (int)$_POST['category_id'] : null;
        $purchase_type = in_array($_POST['purchase_type'] ?? '', ['PURCHASE', 'REPURCHASE']) ? $_POST['purchase_type'] : 'PURCHASE';

        // Image Handling
        $imageUrl = sanitize($_POST['existing_image_url'] ?? '');
        $uploadedImage = handle_image_upload('image_file');
        if ($uploadedImage) {
            $imageUrl = $uploadedImage;
        } elseif (!empty($_POST['image_url'])) {
            $imageUrl = sanitize($_POST['image_url']);
        }

        if (!empty($name) && $price >= 0) {
            if ($id > 0) {
                // Update product
                $stmt = $pdo->prepare("UPDATE products SET name = ?, slug = ?, description = ?, price = ?, stock = ?, category_id = ?, purchase_type = ?, image_url = ? WHERE id = ?");
                $stmt->execute([$name, $slug, $description, $price, $stock, $category_id, $purchase_type, $imageUrl, $id]);
                $message = "Product updated successfully!";
            } else {
                // Insert product
                $stmt = $pdo->prepare("INSERT INTO products (name, slug, description, price, stock, category_id, purchase_type, image_url) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
                $stmt->execute([$name, $slug, $description, $price, $stock, $category_id, $purchase_type, $imageUrl]);
                $message = "Product created successfully!";
            }
        } else {
            $error = "Please fill in all required fields properly.";
        }
    } elseif ($action === 'delete') {
        $id = (int)($_POST['id'] ?? 0);
        if ($id > 0) {
            $stmt = $pdo->prepare("DELETE FROM products WHERE id = ?");
            $stmt->execute([$id]);
            $message = "Product deleted successfully.";
        }
    }
}

// Fetch categories for dropdowns
$categories = $pdo->query("SELECT * FROM categories ORDER BY name ASC")->fetchAll();

// Fetch products with category names
$products = $pdo->query("SELECT p.*, c.name AS category_name FROM products p LEFT JOIN categories c ON p.category_id = c.id ORDER BY p.id DESC")->fetchAll();

// Product being edited if edit parameter passed
$editProduct = null;
if (isset($_GET['edit'])) {
    $editId = (int)$_GET['edit'];
    $stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
    $stmt->execute([$editId]);
    $editProduct = $stmt->fetch();
}

require_once __DIR__ . '/../includes/admin_header.php';
?>

<div class="mb-8 flex items-center justify-between">
    <div>
        <h1 class="text-3xl font-extrabold text-white tracking-tight">Product Management</h1>
        <p class="text-slate-400 text-sm mt-1">Manage single product catalog, stock counts, pricing, and purchase type tags.</p>
    </div>
    <?php if ($editProduct): ?>
        <a href="/admin/products.php" class="bg-slate-800 hover:bg-slate-700 text-white font-semibold px-4 py-2 rounded-xl transition text-sm">
            <i class="fa-solid fa-plus"></i> Add New Product
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
    <!-- Product Form -->
    <div class="bg-slate-800/80 border border-slate-700/60 rounded-2xl p-6 h-fit">
        <h2 class="text-lg font-bold text-white mb-4 flex items-center gap-2">
            <i class="fa-solid <?= $editProduct ? 'fa-pen-to-square text-amber-400' : 'fa-plus-circle text-emerald-400' ?>"></i>
            <?= $editProduct ? 'Edit Product #' . $editProduct['id'] : 'Add New Product' ?>
        </h2>

        <form method="POST" enctype="multipart/form-data" class="space-y-4">
            <input type="hidden" name="action" value="save">
            <input type="hidden" name="id" value="<?= $editProduct['id'] ?? 0 ?>">
            <input type="hidden" name="existing_image_url" value="<?= htmlspecialchars($editProduct['image_url'] ?? '') ?>">

            <div>
                <label class="block text-xs font-semibold uppercase tracking-wider text-slate-400 mb-1">Product Name *</label>
                <input type="text" name="name" value="<?= htmlspecialchars($editProduct['name'] ?? '') ?>" required placeholder="e.g. Organic Korean Rice 5kg"
                       class="w-full bg-slate-900 border border-slate-700 rounded-xl px-4 py-2.5 text-white placeholder-slate-500 focus:outline-none focus:border-emerald-500 text-sm">
            </div>

            <div>
                <label class="block text-xs font-semibold uppercase tracking-wider text-slate-400 mb-1">Purchase Type *</label>
                <select name="purchase_type" required class="w-full bg-slate-900 border border-slate-700 rounded-xl px-4 py-2.5 text-white focus:outline-none focus:border-emerald-500 text-sm">
                    <option value="PURCHASE" <?= ($editProduct['purchase_type'] ?? '') === 'PURCHASE' ? 'selected' : '' ?>>Purchase (One-time / Hardware / Kitchenware)</option>
                    <option value="REPURCHASE" <?= ($editProduct['purchase_type'] ?? '') === 'REPURCHASE' ? 'selected' : '' ?>>Repurchase (Daily Consumables / Grocery / Pantry)</option>
                </select>
                <p class="text-[11px] text-slate-500 mt-1">Categorizes item for one-time buy vs recurring pantry repurchase filters.</p>
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-slate-400 mb-1">Price ($) *</label>
                    <input type="number" step="0.01" name="price" value="<?= $editProduct['price'] ?? '' ?>" required placeholder="28.50"
                           class="w-full bg-slate-900 border border-slate-700 rounded-xl px-4 py-2.5 text-white placeholder-slate-500 focus:outline-none focus:border-emerald-500 text-sm">
                </div>
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-slate-400 mb-1">Stock *</label>
                    <input type="number" name="stock" value="<?= $editProduct['stock'] ?? 50 ?>" required
                           class="w-full bg-slate-900 border border-slate-700 rounded-xl px-4 py-2.5 text-white placeholder-slate-500 focus:outline-none focus:border-emerald-500 text-sm">
                </div>
            </div>

            <div>
                <label class="block text-xs font-semibold uppercase tracking-wider text-slate-400 mb-1">Category</label>
                <select name="category_id" class="w-full bg-slate-900 border border-slate-700 rounded-xl px-4 py-2.5 text-white focus:outline-none focus:border-emerald-500 text-sm">
                    <option value="">-- Select Category --</option>
                    <?php foreach ($categories as $cat): ?>
                        <option value="<?= $cat['id'] ?>" <?= ($editProduct['category_id'] ?? 0) == $cat['id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($cat['name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div>
                <label class="block text-xs font-semibold uppercase tracking-wider text-slate-400 mb-1">Product Description</label>
                <textarea name="description" rows="3" placeholder="Detailed product specifications..."
                          class="w-full bg-slate-900 border border-slate-700 rounded-xl px-4 py-2 text-white placeholder-slate-500 focus:outline-none focus:border-emerald-500 text-sm"><?= htmlspecialchars($editProduct['description'] ?? '') ?></textarea>
            </div>

            <div>
                <label class="block text-xs font-semibold uppercase tracking-wider text-slate-400 mb-1">Product Image</label>
                <input type="file" name="image_file" accept="image/*" class="w-full text-xs text-slate-400 file:mr-3 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-slate-700 file:text-slate-200 hover:file:bg-slate-600 mb-2">

                <p class="text-[11px] text-slate-500 mb-1">Or paste Image URL directly:</p>
                <input type="text" name="image_url" value="<?= htmlspecialchars($editProduct['image_url'] ?? '') ?>" placeholder="https://..."
                       class="w-full bg-slate-900 border border-slate-700 rounded-xl px-4 py-2 text-white placeholder-slate-500 focus:outline-none focus:border-emerald-500 text-xs">
            </div>

            <button type="submit" class="w-full bg-emerald-500 hover:bg-emerald-600 text-slate-950 font-bold py-3 px-4 rounded-xl transition text-sm flex items-center justify-center gap-2 shadow-lg shadow-emerald-500/20">
                <i class="fa-solid fa-floppy-disk"></i> <?= $editProduct ? 'Update Product' : 'Save New Product' ?>
            </button>
        </form>
    </div>

    <!-- Product List Table -->
    <div class="lg:col-span-2 bg-slate-800/80 border border-slate-700/60 rounded-2xl p-6">
        <h2 class="text-lg font-bold text-white mb-4 flex items-center gap-2">
            <i class="fa-solid fa-boxes-stacked text-emerald-400"></i> Store Products Catalog (<?= count($products) ?>)
        </h2>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-slate-300">
                <thead class="bg-slate-900/60 text-xs uppercase font-semibold text-slate-400 border-b border-slate-700/60">
                    <tr>
                        <th class="py-3 px-4">Item</th>
                        <th class="py-3 px-4">Type</th>
                        <th class="py-3 px-4">Price / Stock</th>
                        <th class="py-3 px-4">Category</th>
                        <th class="py-3 px-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-700/40">
                    <?php foreach ($products as $p): ?>
                        <tr class="hover:bg-slate-700/20 transition">
                            <td class="py-3 px-4">
                                <div class="flex items-center gap-3">
                                    <img src="<?= htmlspecialchars($p['image_url'] ?: 'https://via.placeholder.com/100') ?>" class="w-10 h-10 object-cover rounded-lg bg-slate-900 border border-slate-700">
                                    <div>
                                        <div class="font-semibold text-white line-clamp-1"><?= htmlspecialchars($p['name']) ?></div>
                                        <div class="text-[11px] text-slate-500 font-mono">ID: #<?= $p['id'] ?></div>
                                    </div>
                                </div>
                            </td>
                            <td class="py-3 px-4">
                                <?php if ($p['purchase_type'] === 'REPURCHASE'): ?>
                                    <span class="bg-purple-500/10 text-purple-400 border border-purple-500/20 px-2.5 py-1 rounded-full text-[11px] font-bold">
                                        <i class="fa-solid fa-arrows-rotate text-[10px] mr-1"></i> Repurchase
                                    </span>
                                <?php else: ?>
                                    <span class="bg-blue-500/10 text-blue-400 border border-blue-500/20 px-2.5 py-1 rounded-full text-[11px] font-bold">
                                        <i class="fa-solid fa-bag-shopping text-[10px] mr-1"></i> Purchase
                                    </span>
                                <?php endif; ?>
                            </td>
                            <td class="py-3 px-4">
                                <div class="font-bold text-emerald-400"><?= format_price($p['price']) ?></div>
                                <div class="text-xs text-slate-400">Stock: <?= $p['stock'] ?></div>
                            </td>
                            <td class="py-3 px-4 text-xs text-slate-400">
                                <?= htmlspecialchars($p['category_name'] ?? 'Uncategorized') ?>
                            </td>
                            <td class="py-3 px-4 text-right flex items-center justify-end gap-2">
                                <a href="/admin/products.php?edit=<?= $p['id'] ?>" class="text-slate-400 hover:text-emerald-400 p-2 rounded-lg hover:bg-slate-700/50 transition">
                                    <i class="fa-solid fa-pen-to-square"></i>
                                </a>

                                <form method="POST" onsubmit="return confirm('Delete this product permanently?');" class="inline">
                                    <input type="hidden" name="action" value="delete">
                                    <input type="hidden" name="id" value="<?= $p['id'] ?>">
                                    <button type="submit" class="text-slate-400 hover:text-rose-400 p-2 rounded-lg hover:bg-slate-700/50 transition">
                                        <i class="fa-solid fa-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/admin_footer.php'; ?>
