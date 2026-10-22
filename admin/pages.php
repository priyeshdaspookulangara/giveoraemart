<?php
// admin/pages.php - CMS Static Page Manager

$activeNav = 'pages';
require_once __DIR__ . '/includes/admin_header.php';
require_once __DIR__ . '/../models/CmsPageModel.php';
require_once __DIR__ . '/../controllers/AdminController.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    AdminController::handlePageSave();
    exit;
}

$action = $_GET['action'] ?? 'list';
$editId = (int)($_GET['id'] ?? 0);
$pageToEdit = $editId > 0 ? CmsPageModel::getById($editId) : null;
$pages = CmsPageModel::getAll();
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="fw-bold text-dark mb-0">Static Page Manager</h2>
        <p class="text-muted small mb-0">Create and edit website pages with dynamic slug routes.</p>
    </div>
    <?php if ($action !== 'new' && !$pageToEdit): ?>
        <a href="pages.php?action=new" class="btn btn-success fw-bold rounded-pill px-4 shadow-sm">
            <i class="fa-solid fa-plus me-1"></i> Add Static Page
        </a>
    <?php else: ?>
        <a href="pages.php" class="btn btn-outline-secondary rounded-pill px-4">
            <i class="fa-solid fa-arrow-left me-1"></i> Back to Pages List
        </a>
    <?php endif; ?>
</div>

<?php if ($action === 'new' || $pageToEdit): ?>
<!-- Page Editor Form with WYSIWYG -->
<div class="card border-0 shadow-sm rounded-4 p-4 mb-4 bg-white">
    <h4 class="fw-bold text-dark mb-4"><?= $pageToEdit ? 'Edit Static Page' : 'Create New Static Page' ?></h4>
    <form action="pages.php" method="POST">
        <?= csrf_input() ?>
        <?php if ($pageToEdit): ?>
            <input type="hidden" name="id" value="<?= $pageToEdit['id'] ?>">
        <?php endif; ?>

        <div class="row g-3">
            <div class="col-md-6">
                <label for="title" class="form-label fw-bold">Page Title <span class="text-danger">*</span></label>
                <input type="text" class="form-control form-control-lg" id="title" name="title" value="<?= htmlspecialchars($pageToEdit['title'] ?? '') ?>" placeholder="e.g. Terms of Service" required>
            </div>
            <div class="col-md-4">
                <label for="slug" class="form-label fw-bold">URL Slug (e.g. terms-of-service)</label>
                <input type="text" class="form-control form-control-lg" id="slug" name="slug" value="<?= htmlspecialchars($pageToEdit['slug'] ?? '') ?>" placeholder="terms-of-service">
            </div>
            <div class="col-md-2">
                <label for="status" class="form-label fw-bold">Publish Status</label>
                <select class="form-select form-select-lg" id="status" name="status">
                    <option value="published" <?= ($pageToEdit['status'] ?? '') === 'published' ? 'selected' : '' ?>>Published</option>
                    <option value="draft" <?= ($pageToEdit['status'] ?? '') === 'draft' ? 'selected' : '' ?>>Draft</option>
                </select>
            </div>
            <div class="col-12">
                <label for="content" class="form-label fw-bold">Page Content (WYSIWYG Editor) <span class="text-danger">*</span></label>
                <textarea class="form-control wysiwyg" id="content" name="content" rows="10" required><?= htmlspecialchars($pageToEdit['content'] ?? '') ?></textarea>
            </div>
            <div class="col-12 mt-4">
                <button type="submit" class="btn btn-success btn-lg rounded-pill px-5 fw-bold shadow-sm">
                    <i class="fa-solid fa-save me-1"></i> Save Page Content
                </button>
            </div>
        </div>
    </form>
</div>
<?php else: ?>

<!-- Static Pages List Table -->
<div class="card border-0 shadow-sm rounded-4 bg-white">
    <div class="card-body p-4">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Page Title</th>
                        <th>URL Slug Route</th>
                        <th>Status</th>
                        <th>Last Updated</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($pages)): ?>
                        <tr><td colspan="5" class="text-center text-muted py-4">No static pages found.</td></tr>
                    <?php else: ?>
                        <?php foreach ($pages as $p): ?>
                        <tr>
                            <td class="fw-bold text-dark"><?= htmlspecialchars($p['title']) ?></td>
                            <td><code class="text-success">page.php?slug=<?= htmlspecialchars($p['slug']) ?></code></td>
                            <td>
                                <span class="badge bg-<?= $p['status'] === 'published' ? 'success' : 'secondary' ?> text-uppercase">
                                    <?= $p['status'] ?>
                                </span>
                            </td>
                            <td class="small text-muted"><?= date('M j, Y - g:i A', strtotime($p['updated_at'])) ?></td>
                            <td class="text-end">
                                <a href="pages.php?id=<?= $p['id'] ?>" class="btn btn-sm btn-outline-primary me-1">
                                    <i class="fa-solid fa-pen-to-square"></i> Edit
                                </a>
                                <a href="../page.php?slug=<?= urlencode($p['slug']) ?>" target="_blank" class="btn btn-sm btn-outline-secondary">
                                    <i class="fa-solid fa-globe"></i> View Live
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php endif; ?>

<?php require_once __DIR__ . '/includes/admin_footer.php'; ?>
