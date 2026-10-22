<?php
// admin/index.php - CMS Admin Dashboard

$activeNav = 'dashboard';
require_once __DIR__ . '/includes/admin_header.php';
require_once __DIR__ . '/../controllers/AdminController.php';

$data = AdminController::getDashboardMetrics();
$campaignStats = $data['campaign_stats'];
$donationStats = $data['donation_stats'];
$beneficiaryStats = $data['beneficiary_stats'];
$recentDonations = $data['recent_donations'];
$recentBeneficiaries = $data['recent_beneficiaries'];
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="fw-bold text-dark mb-0">Dashboard Overview</h2>
        <p class="text-muted small mb-0">Welcome back! Here is a summary of fundraising performance and activity.</p>
    </div>
    <div class="d-flex gap-2">
        <a href="campaigns.php?action=new" class="btn btn-success fw-bold shadow-sm">
            <i class="fa-solid fa-plus me-1"></i> New Campaign
        </a>
        <a href="posts.php?action=new" class="btn btn-primary fw-bold shadow-sm">
            <i class="fa-solid fa-pen me-1"></i> New Post
        </a>
    </div>
</div>

<!-- Key Stat Cards -->
<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="card border-0 shadow-sm rounded-4 p-3 bg-white">
            <div class="d-flex align-items-center">
                <div class="stat-icon bg-success-subtle text-success me-3">
                    <i class="fa-solid fa-hand-holding-dollar"></i>
                </div>
                <div>
                    <span class="text-muted small fw-bold text-uppercase d-block">Total Raised</span>
                    <h3 class="fw-bold text-dark mb-0"><?= format_currency($donationStats['total_amount']) ?></h3>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm rounded-4 p-3 bg-white">
            <div class="d-flex align-items-center">
                <div class="stat-icon bg-primary-subtle text-primary me-3">
                    <i class="fa-solid fa-bullseye"></i>
                </div>
                <div>
                    <span class="text-muted small fw-bold text-uppercase d-block">Campaign Drives</span>
                    <h3 class="fw-bold text-dark mb-0"><?= number_format($campaignStats['total_campaigns']) ?></h3>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm rounded-4 p-3 bg-white">
            <div class="d-flex align-items-center">
                <div class="stat-icon bg-warning-subtle text-warning me-3">
                    <i class="fa-solid fa-clock-rotate-left"></i>
                </div>
                <div>
                    <span class="text-muted small fw-bold text-uppercase d-block">Pending Aid Requests</span>
                    <h3 class="fw-bold text-dark mb-0"><?= number_format($beneficiaryStats['pending_requests']) ?></h3>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm rounded-4 p-3 bg-white">
            <div class="d-flex align-items-center">
                <div class="stat-icon bg-info-subtle text-info me-3">
                    <i class="fa-solid fa-heart"></i>
                </div>
                <div>
                    <span class="text-muted small fw-bold text-uppercase d-block">Total Donors</span>
                    <h3 class="fw-bold text-dark mb-0"><?= number_format($donationStats['total_donors']) ?></h3>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Main Grid: Recent Donations & Pending Beneficiaries -->
<div class="row g-4">
    <!-- Recent Incoming Funds Ledger -->
    <div class="col-lg-7">
        <div class="card border-0 shadow-sm rounded-4 bg-white">
            <div class="card-header bg-white border-0 pt-4 px-4 pb-0 d-flex justify-content-between align-items-center">
                <h5 class="fw-bold text-dark mb-0"><i class="fa-solid fa-receipt text-success me-2"></i>Recent Contributions</h5>
                <a href="donations.php" class="btn btn-sm btn-outline-success rounded-pill">View All Ledger</a>
            </div>
            <div class="card-body p-4">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Transaction ID</th>
                                <th>Donor</th>
                                <th>Campaign</th>
                                <th>Amount</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($recentDonations)): ?>
                                <tr><td colspan="5" class="text-center text-muted py-3">No recent donations logged.</td></tr>
                            <?php else: ?>
                                <?php foreach ($recentDonations as $d): ?>
                                <tr>
                                    <td class="font-monospace small fw-bold"><?= htmlspecialchars($d['transaction_id']) ?></td>
                                    <td><?= !empty($d['donor_name']) ? htmlspecialchars($d['donor_name']) : '<span class="text-muted italic">Anonymous</span>' ?></td>
                                    <td class="small"><?= htmlspecialchars($d['campaign_title']) ?></td>
                                    <td class="fw-bold text-success"><?= format_currency($d['amount']) ?></td>
                                    <td><span class="badge bg-success-subtle text-success px-2 py-1"><?= htmlspecialchars($d['payment_status']) ?></span></td>
                                </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Beneficiary Review Summary -->
    <div class="col-lg-5">
        <div class="card border-0 shadow-sm rounded-4 bg-white">
            <div class="card-header bg-white border-0 pt-4 px-4 pb-0 d-flex justify-content-between align-items-center">
                <h5 class="fw-bold text-dark mb-0"><i class="fa-solid fa-hand-holding-hand text-warning me-2"></i>Aid Applications</h5>
                <a href="beneficiaries.php" class="btn btn-sm btn-outline-warning rounded-pill">Review Workflow</a>
            </div>
            <div class="card-body p-4">
                <div class="list-group list-group-flush">
                    <?php if (empty($recentBeneficiaries)): ?>
                        <p class="text-muted small">No beneficiary requests submitted.</p>
                    <?php else: ?>
                        <?php foreach (array_slice($recentBeneficiaries, 0, 5) as $b): ?>
                        <div class="list-group-item bg-transparent px-0 py-3 d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="mb-0 fw-bold text-dark"><?= htmlspecialchars($b['full_name']) ?></h6>
                                <span class="text-muted small"><?= htmlspecialchars($b['assistance_type']) ?> • <?= format_currency($b['requested_amount']) ?></span>
                            </div>
                            <span class="badge bg-<?= $b['status'] === 'approved' ? 'success' : ($b['status'] === 'rejected' ? 'danger' : 'warning') ?> text-capitalize">
                                <?= $b['status'] ?>
                            </span>
                        </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/admin_footer.php'; ?>
