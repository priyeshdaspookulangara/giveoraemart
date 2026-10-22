<?php
// campaigns.php - Browse All Fundraising Campaigns

require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/controllers/CampaignController.php';

$data = CampaignController::index();
$campaigns = $data['campaigns'];
$currentStatus = $data['current_status'];
?>

<div class="bg-white border-bottom py-4">
    <div class="container">
        <h1 class="fw-bold text-dark mb-1">Fundraising Campaigns</h1>
        <p class="text-muted mb-0">Browse our transparent charity drives and make a direct impact today.</p>
    </div>
</div>

<div class="container py-4">
    <!-- Status Filter Pills -->
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        <div class="btn-group" role="group">
            <a href="campaigns.php?status=active" class="btn <?= $currentStatus === 'active' ? 'btn-success' : 'btn-outline-secondary' ?>">Active Drives</a>
            <a href="campaigns.php?status=completed" class="btn <?= $currentStatus === 'completed' ? 'btn-success' : 'btn-outline-secondary' ?>">Completed</a>
            <a href="campaigns.php?status=all" class="btn <?= $currentStatus === 'all' ? 'btn-success' : 'btn-outline-secondary' ?>">All Campaigns</a>
        </div>
    </div>

    <!-- Campaigns Grid -->
    <div class="row g-4">
        <?php if (empty($campaigns)): ?>
            <div class="col-12 text-center py-5">
                <i class="fa-solid fa-bullseye fa-3x text-muted mb-3"></i>
                <p class="text-muted">No campaigns found under the selected category.</p>
            </div>
        <?php else: ?>
            <?php foreach ($campaigns as $c):
                $pct = calculate_percentage($c['raised_amount'], $c['goal_amount']);
                $img = !empty($c['image_url']) ? $c['image_url'] : 'https://images.unsplash.com/photo-1542838132-92c53300491e?auto=format&fit=crop&w=800&q=80';
            ?>
            <div class="col-lg-4 col-md-6">
                <div class="card card-campaign h-100 shadow-sm border-0">
                    <div class="position-relative">
                        <img src="<?= htmlspecialchars($img) ?>" class="card-img-top" alt="<?= htmlspecialchars($c['title']) ?>" style="height: 220px; object-fit: cover;">
                        <span class="position-absolute top-0 end-0 badge bg-<?= $c['status'] === 'active' ? 'success' : ($c['status'] === 'completed' ? 'primary' : 'secondary') ?> m-3 text-uppercase">
                            <?= ucfirst($c['status']) ?>
                        </span>
                    </div>
                    <div class="card-body d-flex flex-column p-4">
                        <h5 class="card-title fw-bold text-dark mb-2"><?= htmlspecialchars($c['title']) ?></h5>
                        <p class="card-text text-muted small flex-grow-1"><?= htmlspecialchars(substr($c['description'], 0, 120)) ?>...</p>

                        <div class="my-3">
                            <div class="d-flex justify-content-between small fw-bold mb-1">
                                <span class="text-success"><?= format_currency($c['raised_amount']) ?> raised</span>
                                <span class="text-muted">Goal: <?= format_currency($c['goal_amount']) ?></span>
                            </div>
                            <div class="progress">
                                <div class="progress-bar bg-success" role="progressbar" style="width: <?= $pct ?>%;" aria-valuenow="<?= $pct ?>" aria-valuemin="0" aria-valuemax="100"></div>
                            </div>
                            <div class="text-end text-muted small mt-1"><?= $pct ?>% funded</div>
                        </div>

                        <a href="campaign-detail.php?slug=<?= urlencode($c['slug']) ?>" class="btn btn-success w-100 rounded-pill fw-bold">
                            <i class="fa-solid fa-circle-info me-1"></i> View Details & Donate
                        </a>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
