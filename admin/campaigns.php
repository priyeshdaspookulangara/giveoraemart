<?php
// admin/campaigns.php - Campaign Management Dashboard

$activeNav = 'campaigns';
require_once __DIR__ . '/includes/admin_header.php';
require_once __DIR__ . '/../models/CampaignModel.php';
require_once __DIR__ . '/../controllers/AdminController.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    AdminController::handleCampaignSave();
    exit;
}

$action = $_GET['action'] ?? 'list';
$editId = (int)($_GET['id'] ?? 0);
$campaignToEdit = $editId > 0 ? CampaignModel::getById($editId) : null;
$campaigns = CampaignModel::getAll();
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="fw-bold text-dark mb-0">Fundraising Campaigns</h2>
        <p class="text-muted small mb-0">Create, edit, and track active and completed charity projects.</p>
    </div>
    <?php if ($action !== 'new' && !$campaignToEdit): ?>
        <a href="campaigns.php?action=new" class="btn btn-success fw-bold rounded-pill px-4 shadow-sm">
            <i class="fa-solid fa-plus me-1"></i> Add New Campaign
        </a>
    <?php else: ?>
        <a href="campaigns.php" class="btn btn-outline-secondary rounded-pill px-4">
            <i class="fa-solid fa-arrow-left me-1"></i> Back to Campaign List
        </a>
    <?php endif; ?>
</div>

<?php if ($action === 'new' || $campaignToEdit): ?>
<!-- Campaign Form -->
<div class="card border-0 shadow-sm rounded-4 p-4 mb-4 bg-white">
    <h4 class="fw-bold text-dark mb-4"><?= $campaignToEdit ? 'Edit Campaign' : 'Create New Campaign Drive' ?></h4>
    <form action="campaigns.php" method="POST">
        <?= csrf_input() ?>
        <?php if ($campaignToEdit): ?>
            <input type="hidden" name="id" value="<?= $campaignToEdit['id'] ?>">
        <?php endif; ?>

        <div class="row g-3">
            <div class="col-md-8">
                <label for="title" class="form-label fw-bold">Campaign Title <span class="text-danger">*</span></label>
                <input type="text" class="form-control form-control-lg" id="title" name="title" value="<?= htmlspecialchars($campaignToEdit['title'] ?? '') ?>" placeholder="e.g. Clean Water Wells for Village A" required>
            </div>
            <div class="col-md-4">
                <label for="slug" class="form-label fw-bold">URL Slug (Auto-generated if empty)</label>
                <input type="text" class="form-control form-control-lg" id="slug" name="slug" value="<?= htmlspecialchars($campaignToEdit['slug'] ?? '') ?>" placeholder="clean-water-wells">
            </div>
            <div class="col-md-6">
                <label for="goal_amount" class="form-label fw-bold">Fundraising Target Goal ($) <span class="text-danger">*</span></label>
                <div class="input-group input-group-lg">
                    <span class="input-group-text">$</span>
                    <input type="number" step="0.01" min="1" class="form-control" id="goal_amount" name="goal_amount" value="<?= htmlspecialchars($campaignToEdit['goal_amount'] ?? '10000.00') ?>" required>
                </div>
            </div>
            <div class="col-md-6">
                <label for="status" class="form-label fw-bold">Campaign Status</label>
                <select class="form-select form-select-lg" id="status" name="status">
                    <option value="active" <?= ($campaignToEdit['status'] ?? '') === 'active' ? 'selected' : '' ?>>Active</option>
                    <option value="paused" <?= ($campaignToEdit['status'] ?? '') === 'paused' ? 'selected' : '' ?>>Paused</option>
                    <option value="completed" <?= ($campaignToEdit['status'] ?? '') === 'completed' ? 'selected' : '' ?>>Completed</option>
                </select>
            </div>
            <div class="col-12">
                <label for="image_url" class="form-label fw-bold">Banner Image URL</label>
                <input type="url" class="form-control" id="image_url" name="image_url" value="<?= htmlspecialchars($campaignToEdit['image_url'] ?? '') ?>" placeholder="https://images.unsplash.com/...">
            </div>
            <div class="col-12">
                <label for="description" class="form-label fw-bold">Campaign Description & Story <span class="text-danger">*</span></label>
                <textarea class="form-control" id="description" name="description" rows="5" required><?= htmlspecialchars($campaignToEdit['description'] ?? '') ?></textarea>
            </div>
            <div class="col-12 mt-4">
                <button type="submit" class="btn btn-success btn-lg rounded-pill px-5 fw-bold shadow-sm">
                    <i class="fa-solid fa-save me-1"></i> Save Campaign
                </button>
            </div>
        </div>
    </form>
</div>
<?php else: ?>

<!-- Campaigns Table -->
<div class="card border-0 shadow-sm rounded-4 bg-white">
    <div class="card-body p-4">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Campaign Title</th>
                        <th>Status</th>
                        <th>Goal Amount</th>
                        <th>Raised Amount</th>
                        <th>Progress</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($campaigns)): ?>
                        <tr><td colspan="6" class="text-center text-muted py-4">No campaigns found.</td></tr>
                    <?php else: ?>
                        <?php foreach ($campaigns as $c):
                            $pct = calculate_percentage($c['raised_amount'], $c['goal_amount']);
                        ?>
                        <tr>
                            <td class="fw-bold text-dark">
                                <?= htmlspecialchars($c['title']) ?>
                                <div class="small text-muted font-monospace">/<?= htmlspecialchars($c['slug']) ?></div>
                            </td>
                            <td>
                                <span class="badge bg-<?= $c['status'] === 'active' ? 'success' : ($c['status'] === 'completed' ? 'primary' : 'secondary') ?> text-uppercase">
                                    <?= $c['status'] ?>
                                </span>
                            </td>
                            <td class="fw-bold"><?= format_currency($c['goal_amount']) ?></td>
                            <td class="fw-bold text-success"><?= format_currency($c['raised_amount']) ?></td>
                            <td style="width: 150px;">
                                <div class="progress" style="height: 8px;">
                                    <div class="progress-bar bg-success" role="progressbar" style="width: <?= $pct ?>%;"></div>
                                </div>
                                <span class="small text-muted"><?= $pct ?>%</span>
                            </td>
                            <td class="text-end">
                                <a href="campaigns.php?id=<?= $c['id'] ?>" class="btn btn-sm btn-outline-primary me-1">
                                    <i class="fa-solid fa-pen-to-square"></i> Edit
                                </a>
                                <a href="../campaign-detail.php?slug=<?= urlencode($c['slug']) ?>" target="_blank" class="btn btn-sm btn-outline-secondary">
                                    <i class="fa-solid fa-eye"></i> View
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
