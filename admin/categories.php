<?php
// admin/categories.php
require_once __DIR__ . '/../includes/functions.php';

$pageTitle = 'Category Management';
$activeTab = 'categories';
$pdo = get_db_connection();

$message = '';
$error = '';

// Handle Create / Edit / Delete Actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = sanitize($_POST['action'] ?? '');

    if ($action === 'create') {
        $name = sanitize($_POST['name'] ?? '');
        $slug = create_slug($_POST['slug'] ?? $name);

        if (!empty($name)) {
            try {
                $stmt = $pdo->prepare("INSERT INTO categories (name, slug) VALUES (?, ?)");
                $stmt->execute([$name, $slug]);
                $message = "Category '$name' created successfully!";
            } catch (PDOException $e) {
                $error = "Error creating category: " . ($e->getCode() == 23000 ? "Category slug already exists." : $e->getMessage());
            }
        } else {
            $error = "Category name cannot be empty.";
        }
    } elseif ($action === 'edit') {
        $id = (int)($_POST['id'] ?? 0);
        $name = sanitize($_POST['name'] ?? '');
        $slug = create_slug($_POST['slug'] ?? $name);

        if ($id > 0 && !empty($name)) {
            try {
                $stmt = $pdo->prepare("UPDATE categories SET name = ?, slug = ? WHERE id = ?");
                $stmt->execute([$name, $slug, $id]);
                $message = "Category updated successfully!";
            } catch (PDOException $e) {
                $error = "Error updating category: " . $e->getMessage();
            }
        }
    } elseif ($action === 'delete') {
        $id = (int)($_POST['id'] ?? 0);
        if ($id > 0) {
            $stmt = $pdo->prepare("DELETE FROM categories WHERE id = ?");
            $stmt->execute([$id]);
            $message = "Category deleted successfully.";
        }
    }
}

$categories = $pdo->query("SELECT c.*, COUNT(p.id) AS product_count FROM categories c LEFT JOIN products p ON c.id = p.category_id GROUP BY c.id ORDER BY c.name ASC")->fetchAll();

require_once __DIR__ . '/../includes/admin_header.php';
?>

<div class="mb-8">
    <h1 class="text-3xl font-extrabold text-white tracking-tight">Category Management</h1>
    <p class="text-slate-400 text-sm mt-1">Organize products into store categories for easy customer browsing.</p>
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
    <!-- Add Category Form -->
    <div class="bg-slate-800/80 border border-slate-700/60 rounded-2xl p-6 h-fit">
        <h2 class="text-lg font-bold text-white mb-4 flex items-center gap-2">
            <i class="fa-solid fa-folder-plus text-emerald-400"></i> Add New Category
        </h2>
        <form method="POST" class="space-y-4">
            <input type="hidden" name="action" value="create">

            <div>
                <label class="block text-xs font-semibold uppercase tracking-wider text-slate-400 mb-2">Category Name</label>
                <input type="text" name="name" required placeholder="e.g. Fresh Groceries"
                       class="w-full bg-slate-900 border border-slate-700 rounded-xl px-4 py-2.5 text-white placeholder-slate-500 focus:outline-none focus:border-emerald-500 text-sm">
            </div>

            <div>
                <label class="block text-xs font-semibold uppercase tracking-wider text-slate-400 mb-2">Slug (Optional)</label>
                <input type="text" name="slug" placeholder="e.g. fresh-groceries"
                       class="w-full bg-slate-900 border border-slate-700 rounded-xl px-4 py-2.5 text-white placeholder-slate-500 focus:outline-none focus:border-emerald-500 text-sm">
            </div>

            <button type="submit" class="w-full bg-emerald-500 hover:bg-emerald-600 text-slate-950 font-semibold py-2.5 px-4 rounded-xl transition text-sm flex items-center justify-center gap-2">
                <i class="fa-solid fa-plus"></i> Create Category
            </button>
        </form>
    </div>

    <!-- Category List Table -->
    <div class="lg:col-span-2 bg-slate-800/80 border border-slate-700/60 rounded-2xl p-6">
        <h2 class="text-lg font-bold text-white mb-4 flex items-center gap-2">
            <i class="fa-solid fa-list text-emerald-400"></i> Existing Categories (<?= count($categories) ?>)
        </h2>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-slate-300">
                <thead class="bg-slate-900/60 text-xs uppercase font-semibold text-slate-400 border-b border-slate-700/60">
                    <tr>
                        <th class="py-3 px-4">ID</th>
                        <th class="py-3 px-4">Name</th>
                        <th class="py-3 px-4">Slug</th>
                        <th class="py-3 px-4">Products</th>
                        <th class="py-3 px-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-700/40">
                    <?php foreach ($categories as $cat): ?>
                        <tr class="hover:bg-slate-700/20 transition">
                            <td class="py-3 px-4 text-slate-500 font-mono text-xs">#<?= $cat['id'] ?></td>
                            <td class="py-3 px-4 font-semibold text-white"><?= htmlspecialchars($cat['name']) ?></td>
                            <td class="py-3 px-4 font-mono text-xs text-slate-400"><?= htmlspecialchars($cat['slug']) ?></td>
                            <td class="py-3 px-4">
                                <span class="bg-slate-900 text-emerald-400 border border-emerald-500/20 px-2.5 py-1 rounded-lg text-xs font-bold">
                                    <?= $cat['product_count'] ?> items
                                </span>
                            </td>
                            <td class="py-3 px-4 text-right flex items-center justify-end gap-2">
                                <!-- Edit Modal Trigger -->
                                <button onclick="openEditModal(<?= $cat['id'] ?>, '<?= htmlspecialchars(addslashes($cat['name'])) ?>', '<?= htmlspecialchars(addslashes($cat['slug'])) ?>')" class="text-slate-400 hover:text-emerald-400 p-2 rounded-lg hover:bg-slate-700/50 transition">
                                    <i class="fa-solid fa-pen-to-square"></i>
                                </button>

                                <form method="POST" onsubmit="return confirm('Delete this category? Linked products will become unassigned.');" class="inline">
                                    <input type="hidden" name="action" value="delete">
                                    <input type="hidden" name="id" value="<?= $cat['id'] ?>">
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

<!-- Edit Category Modal -->
<div id="editModal" class="fixed inset-0 bg-slate-950/80 backdrop-blur-sm z-50 flex items-center justify-center hidden">
    <div class="bg-slate-800 border border-slate-700 rounded-2xl p-6 max-w-md w-full shadow-2xl">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-bold text-white">Edit Category</h3>
            <button onclick="closeEditModal()" class="text-slate-400 hover:text-white"><i class="fa-solid fa-xmark text-lg"></i></button>
        </div>
        <form method="POST" class="space-y-4">
            <input type="hidden" name="action" value="edit">
            <input type="hidden" name="id" id="edit_id">

            <div>
                <label class="block text-xs font-semibold uppercase tracking-wider text-slate-400 mb-2">Category Name</label>
                <input type="text" name="name" id="edit_name" required class="w-full bg-slate-900 border border-slate-700 rounded-xl px-4 py-2.5 text-white text-sm focus:outline-none focus:border-emerald-500">
            </div>

            <div>
                <label class="block text-xs font-semibold uppercase tracking-wider text-slate-400 mb-2">Slug</label>
                <input type="text" name="slug" id="edit_slug" required class="w-full bg-slate-900 border border-slate-700 rounded-xl px-4 py-2.5 text-white text-sm focus:outline-none focus:border-emerald-500">
            </div>

            <div class="flex justify-end gap-3 pt-2">
                <button type="button" onclick="closeEditModal()" class="px-4 py-2 rounded-xl text-slate-400 hover:text-white bg-slate-700 text-sm font-semibold">Cancel</button>
                <button type="submit" class="px-4 py-2 rounded-xl bg-emerald-500 hover:bg-emerald-600 text-slate-950 text-sm font-semibold">Save Changes</button>
            </div>
        </form>
    </div>
</div>

<script>
function openEditModal(id, name, slug) {
    document.getElementById('edit_id').value = id;
    document.getElementById('edit_name').value = name;
    document.getElementById('edit_slug').value = slug;
    document.getElementById('editModal').classList.remove('hidden');
}

function closeEditModal() {
    document.getElementById('editModal').classList.add('hidden');
}
</script>

<?php require_once __DIR__ . '/../includes/admin_footer.php'; ?>
