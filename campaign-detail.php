<?php
// campaign-detail.php - Campaign Detail View & Embedded Donation Form

require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/controllers/CampaignController.php';

$slug = $_GET['slug'] ?? '';
$data = CampaignController::detail($slug);

if (!$data):
?>
<div class="container py-5 my-5 text-center">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <i class="fa-solid fa-triangle-exclamation fa-4x text-warning mb-3"></i>
            <h2 class="fw-bold text-dark">Campaign Not Found</h2>
            <p class="text-muted">The requested fundraising campaign could not be located.</p>
            <a href="campaigns.php" class="btn btn-success rounded-pill px-4 mt-2">Back to Campaigns</a>
        </div>
    </div>
</div>
<?php
else:
    $c = $data['campaign'];
    $recentDonations = $data['recent_donations'];
    $pct = calculate_percentage($c['raised_amount'], $c['goal_amount']);
    $img = !empty($c['image_url']) ? $c['image_url'] : 'https://images.unsplash.com/photo-1542838132-92c53300491e?auto=format&fit=crop&w=800&q=80';
?>

<div class="bg-white border-bottom py-4">
    <div class="container">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-1 small">
                <li class="breadcrumb-item"><a href="index.php" class="text-success text-decoration-none">Home</a></li>
                <li class="breadcrumb-item"><a href="campaigns.php" class="text-success text-decoration-none">Campaigns</a></li>
                <li class="breadcrumb-item active" aria-current="page"><?= htmlspecialchars($c['title']) ?></li>
            </ol>
        </nav>
        <h1 class="fw-bold text-dark mb-0"><?= htmlspecialchars($c['title']) ?></h1>
    </div>
</div>

<div class="container py-5">
    <div class="row g-4">
        <!-- Left Column: Story & Details -->
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4 bg-white">
                <img src="<?= htmlspecialchars($img) ?>" class="img-fluid w-100" style="max-height: 400px; object-fit: cover;" alt="<?= htmlspecialchars($c['title']) ?>">
                <div class="card-body p-4 p-md-5">
                    <h4 class="fw-bold text-dark mb-3">About This Campaign</h4>
                    <p class="text-dark lh-lg mb-4" style="white-space: pre-line;"><?= htmlspecialchars($c['description']) ?></p>
                </div>
            </div>

            <!-- Recent Donors Ledger -->
            <div class="card border-0 shadow-sm rounded-4 p-4 bg-white">
                <h5 class="fw-bold text-dark mb-3"><i class="fa-solid fa-heart text-danger me-2"></i>Recent Generous Donors</h5>
                <?php if (empty($recentDonations)): ?>
                    <p class="text-muted small">Be the first to donate to this worthy cause!</p>
                <?php else: ?>
                    <div class="list-group list-group-flush">
                        <?php foreach ($recentDonations as $d): ?>
                        <div class="list-group-item d-flex justify-content-between align-items-center bg-transparent px-0 py-3">
                            <div class="d-flex align-items-center">
                                <div class="bg-success text-white rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 40px; height: 40px;">
                                    <i class="fa-solid fa-user"></i>
                                </div>
                                <div>
                                    <h6 class="mb-0 fw-bold text-dark"><?= !empty($d['donor_name']) ? htmlspecialchars($d['donor_name']) : 'Anonymous Supporter' ?></h6>
                                    <span class="text-muted small"><?= date('M j, Y - g:i A', strtotime($d['donated_at'])) ?></span>
                                </div>
                            </div>
                            <span class="badge bg-success-subtle text-success fw-bold fs-6 border border-success-subtle rounded-pill px-3 py-2">
                                <?= format_currency($d['amount']) ?>
                            </span>
                        </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Right Column: Progress Card & Donation Form -->
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm rounded-4 p-4 sticky-top bg-white" style="top: 90px;">
                <span class="badge bg-<?= $c['status'] === 'active' ? 'success' : 'secondary' ?> text-uppercase mb-2 w-auto me-auto px-3 py-2">
                    <?= ucfirst($c['status']) ?> Drive
                </span>

                <h3 class="fw-bold text-success mb-1"><?= format_currency($c['raised_amount']) ?></h3>
                <p class="text-muted small mb-3">raised of <?= format_currency($c['goal_amount']) ?> target goal</p>

                <div class="progress mb-2" style="height: 12px;">
                    <div class="progress-bar bg-success" role="progressbar" style="width: <?= $pct ?>%;" aria-valuenow="<?= $pct ?>" aria-valuemin="0" aria-valuemax="100"></div>
                </div>
                <div class="d-flex justify-content-between small text-muted mb-4 fw-medium">
                    <span><?= $pct ?>% Complete</span>
                    <span>Goal: <?= format_currency($c['goal_amount']) ?></span>
                </div>

                <hr class="my-3">

                <h5 class="fw-bold text-dark mb-3">Make a Donation</h5>

                <?php if ($c['status'] !== 'active'): ?>
                    <div class="alert alert-secondary">This campaign is currently <?= htmlspecialchars($c['status']) ?> and is not accepting new donations.</div>
                <?php else: ?>
                    <form action="donate.php" method="POST">
                        <?= csrf_input() ?>
                        <input type="hidden" name="campaign_id" value="<?= $c['id'] ?>">

                        <!-- Amount Presets -->
                        <div class="mb-3">
                            <label class="form-label fw-bold small text-muted">Select Amount</label>
                            <div class="row g-2 mb-2">
                                <div class="col-4"><button type="button" class="btn btn-outline-success w-100 preset-btn" onclick="selectAmount(25)">$25</button></div>
                                <div class="col-4"><button type="button" class="btn btn-outline-success w-100 preset-btn" onclick="selectAmount(50)">$50</button></div>
                                <div class="col-4"><button type="button" class="btn btn-outline-success w-100 preset-btn" onclick="selectAmount(100)">$100</button></div>
                            </div>
                        </div>

                        <!-- Custom Amount -->
                        <div class="mb-3">
                            <label for="customAmount" class="form-label fw-bold small text-muted">Or Enter Custom Amount ($)</label>
                            <div class="input-group">
                                <span class="input-group-text">$</span>
                                <input type="number" step="0.01" min="1" class="form-control" id="customAmount" name="amount" placeholder="e.g. 75.00" required>
                            </div>
                        </div>

                        <!-- Anonymous Checkbox -->
                        <div class="form-check mb-4">
                            <input class="form-check-input" type="checkbox" id="is_anonymous" name="is_anonymous" value="1">
                            <label class="form-check-label small text-muted" for="is_anonymous">
                                Keep my donation anonymous
                            </label>
                        </div>

                        <button type="submit" class="btn btn-success btn-lg w-100 rounded-pill fw-bold shadow-sm">
                            <i class="fa-solid fa-lock me-2"></i> Proceed to Checkout
                        </button>
                    </form>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<script>
function selectAmount(val) {
    document.getElementById('customAmount').value = val;
}
</script>

<?php
endif;
require_once __DIR__ . '/includes/footer.php';
?>
