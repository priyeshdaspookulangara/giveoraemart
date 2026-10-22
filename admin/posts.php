<?php
// admin/posts.php - CMS Blog & News Manager

$activeNav = 'posts';
require_once __DIR__ . '/includes/admin_header.php';
require_once __DIR__ . '/../models/CmsPostModel.php';
require_once __DIR__ . '/../controllers/AdminController.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    AdminController::handlePostSave();
    exit;
}

$action = $_GET['action'] ?? 'list';
$editId = (int)($_GET['id'] ?? 0);
$postToEdit = $editId > 0 ? CmsPostModel::getById($editId) : null;
$posts = CmsPostModel::getAll();
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="fw-bold text-dark mb-0">Blog & News Manager</h2>
        <p class="text-muted small mb-0">Publish articles, updates, and impact stories.</p>
    </div>
    <?php if ($action !== 'new' && !$postToEdit): ?>
        <a href="posts.php?action=new" class="btn btn-primary fw-bold rounded-pill px-4 shadow-sm">
            <i class="fa-solid fa-pen me-1"></i> Write New Article
        </a>
    <?php else: ?>
        <a href="posts.php" class="btn btn-outline-secondary rounded-pill px-4">
            <i class="fa-solid fa-arrow-left me-1"></i> Back to Articles
        </a>
    <?php endif; ?>
</div>

<?php if ($action === 'new' || $postToEdit): ?>
<!-- Post Form with WYSIWYG Editor -->
<div class="card border-0 shadow-sm rounded-4 p-4 mb-4 bg-white">
    <h4 class="fw-bold text-dark mb-4"><?= $postToEdit ? 'Edit Article' : 'Write New Article' ?></h4>
    <form action="posts.php" method="POST">
        <?= csrf_input() ?>
        <?php if ($postToEdit): ?>
            <input type="hidden" name="id" value="<?= $postToEdit['id'] ?>">
        <?php endif; ?>

        <div class="row g-3">
            <div class="col-md-6">
                <label for="title" class="form-label fw-bold">Article Title <span class="text-danger">*</span></label>
                <input type="text" class="form-control form-control-lg" id="title" name="title" value="<?= htmlspecialchars($postToEdit['title'] ?? '') ?>" placeholder="e.g. Annual Gala Raises Funds for Healthcare" required>
            </div>
            <div class="col-md-4">
                <label for="slug" class="form-label fw-bold">URL Slug (e.g. annual-gala-raises-funds)</label>
                <input type="text" class="form-control form-control-lg" id="slug" name="slug" value="<?= htmlspecialchars($postToEdit['slug'] ?? '') ?>" placeholder="annual-gala-raises-funds">
            </div>
            <div class="col-md-2">
                <label for="status" class="form-label fw-bold">Status</label>
                <select class="form-select form-select-lg" id="status" name="status">
                    <option value="published" <?= ($postToEdit['status'] ?? '') === 'published' ? 'selected' : '' ?>>Published</option>
                    <option value="draft" <?= ($postToEdit['status'] ?? '') === 'draft' ? 'selected' : '' ?>>Draft</option>
                </select>
            </div>
            <div class="col-12">
                <label for="featured_image" class="form-label fw-bold">Featured Image URL</label>
                <input type="url" class="form-control" id="featured_image" name="featured_image" value="<?= htmlspecialchars($postToEdit['featured_image'] ?? '') ?>" placeholder="https://images.unsplash.com/...">
            </div>
            <div class="col-12">
                <label for="content" class="form-label fw-bold">Article Body Content (WYSIWYG Editor) <span class="text-danger">*</span></label>
                <textarea class="form-control wysiwyg" id="content" name="content" rows="10" required><?= htmlspecialchars($postToEdit['content'] ?? '') ?></textarea>
            </div>
            <div class="col-12 mt-4">
                <button type="submit" class="btn btn-primary btn-lg rounded-pill px-5 fw-bold shadow-sm">
                    <i class="fa-solid fa-paper-plane me-1"></i> Save & Publish Article
                </button>
            </div>
        </div>
    </form>
</div>
<?php else: ?>

<!-- Articles Table -->
<div class="card border-0 shadow-sm rounded-4 bg-white">
    <div class="card-body p-4">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Title</th>
                        <th>Author</th>
                        <th>Status</th>
                        <th>Published Date</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($posts)): ?>
                        <tr><td colspan="5" class="text-center text-muted py-4">No articles found.</td></tr>
                    <?php else: ?>
                        <?php foreach ($posts as $post): ?>
                        <tr>
                            <td class="fw-bold text-dark">
                                <?= htmlspecialchars($post['title']) ?>
                                <div class="small text-muted font-monospace">/<?= htmlspecialchars($post['slug']) ?></div>
                            </td>
                            <td><?= htmlspecialchars($post['author_name'] ?? 'System') ?></td>
                            <td>
                                <span class="badge bg-<?= $post['status'] === 'published' ? 'success' : 'secondary' ?> text-uppercase">
                                    <?= $post['status'] ?>
                                </span>
                            </td>
                            <td class="small text-muted"><?= date('M j, Y - g:i A', strtotime($post['created_at'])) ?></td>
                            <td class="text-end">
                                <a href="posts.php?id=<?= $post['id'] ?>" class="btn btn-sm btn-outline-primary me-1">
                                    <i class="fa-solid fa-pen-to-square"></i> Edit
                                </a>
                                <a href="../post.php?slug=<?= urlencode($post['slug']) ?>" target="_blank" class="btn btn-sm btn-outline-secondary">
                                    <i class="fa-solid fa-globe"></i> View
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
