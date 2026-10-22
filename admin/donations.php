<?php
// admin/donations.php - Master Ledger for Donations & Incoming Funds

$activeNav = 'donations';
require_once __DIR__ . '/includes/admin_header.php';
require_once __DIR__ . '/../models/DonationModel.php';

$filterStatus = $_GET['status'] ?? 'all';
$validStatuses = ['completed', 'pending', 'failed'];
$donations = DonationModel::getAll(null, in_array($filterStatus, $validStatuses) ? $filterStatus : null);
$stats = DonationModel::getStats();
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="fw-bold text-dark mb-0">Donation Master Ledger</h2>
        <p class="text-muted small mb-0">Track all incoming donor contributions, transactions, and payment statuses.</p>
    </div>
    <div class="card border-0 shadow-sm px-4 py-2 bg-success text-white rounded-pill">
        <span class="small opacity-90">Total Completed Funds</span>
        <span class="fs-5 fw-bold ms-2"><?= format_currency($stats['total_amount']) ?></span>
    </div>
</div>

<!-- Filters -->
<div class="mb-4">
    <div class="btn-group" role="group">
        <a href="donations.php?status=all" class="btn <?= $filterStatus === 'all' ? 'btn-dark' : 'btn-outline-secondary' ?>">All Transactions</a>
        <a href="donations.php?status=completed" class="btn <?= $filterStatus === 'completed' ? 'btn-success' : 'btn-outline-secondary' ?>">Completed</a>
        <a href="donations.php?status=pending" class="btn <?= $filterStatus === 'pending' ? 'btn-warning' : 'btn-outline-secondary' ?>">Pending</a>
        <a href="donations.php?status=failed" class="btn <?= $filterStatus === 'failed' ? 'btn-danger' : 'btn-outline-secondary' ?>">Failed</a>
    </div>
</div>

<!-- Ledger Table -->
<div class="card border-0 shadow-sm rounded-4 bg-white">
    <div class="card-body p-4">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Transaction ID</th>
                        <th>Donor Name / Email</th>
                        <th>Campaign</th>
                        <th>Amount</th>
                        <th>Status</th>
                        <th>Date & Time</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($donations)): ?>
                        <tr><td colspan="6" class="text-center text-muted py-4">No donation transactions found under this filter.</td></tr>
                    <?php else: ?>
                        <?php foreach ($donations as $d): ?>
                        <tr>
                            <td class="font-monospace fw-bold small"><?= htmlspecialchars($d['transaction_id']) ?></td>
                            <td>
                                <div class="fw-bold text-dark"><?= !empty($d['donor_name']) ? htmlspecialchars($d['donor_name']) : '<span class="text-muted italic">Anonymous Supporter</span>' ?></div>
                                <?php if (!empty($d['donor_email'])): ?>
                                    <div class="small text-muted"><?= htmlspecialchars($d['donor_email']) ?></div>
                                <?php endif; ?>
                            </td>
                            <td class="fw-medium text-dark"><?= htmlspecialchars($d['campaign_title']) ?></td>
                            <td class="fw-bold text-success fs-6"><?= format_currency($d['amount']) ?></td>
                            <td>
                                <span class="badge bg-<?= $d['payment_status'] === 'completed' ? 'success' : ($d['payment_status'] === 'pending' ? 'warning' : 'danger') ?> text-uppercase">
                                    <?= htmlspecialchars($d['payment_status']) ?>
                                </span>
                            </td>
                            <td class="small text-muted"><?= date('M j, Y - g:i A', strtotime($d['donated_at'])) ?></td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/admin_footer.php'; ?>
