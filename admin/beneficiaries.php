<?php
// admin/beneficiaries.php - Beneficiary Review Workflow

$activeNav = 'beneficiaries';
require_once __DIR__ . '/includes/admin_header.php';
require_once __DIR__ . '/../models/BeneficiaryModel.php';
require_once __DIR__ . '/../controllers/AdminController.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    AdminController::handleBeneficiaryStatusUpdate();
    exit;
}

$filterStatus = $_GET['status'] ?? 'all';
$validStatuses = ['pending', 'approved', 'rejected'];
$beneficiaries = BeneficiaryModel::getAll(in_array($filterStatus, $validStatuses) ? $filterStatus : null);
$stats = BeneficiaryModel::getStats();
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="fw-bold text-dark mb-0">Beneficiary Assistance Review</h2>
        <p class="text-muted small mb-0">Evaluate and manage public aid applications submitted by individuals in need.</p>
    </div>
    <div class="d-flex gap-2">
        <span class="badge bg-warning text-dark px-3 py-2 fs-6 rounded-pill">Pending: <?= $stats['pending_requests'] ?></span>
        <span class="badge bg-success px-3 py-2 fs-6 rounded-pill">Approved: <?= $stats['approved_requests'] ?></span>
    </div>
</div>

<!-- Filters -->
<div class="mb-4">
    <div class="btn-group" role="group">
        <a href="beneficiaries.php?status=all" class="btn <?= $filterStatus === 'all' ? 'btn-dark' : 'btn-outline-secondary' ?>">All Applications</a>
        <a href="beneficiaries.php?status=pending" class="btn <?= $filterStatus === 'pending' ? 'btn-warning' : 'btn-outline-secondary' ?>">Pending Review</a>
        <a href="beneficiaries.php?status=approved" class="btn <?= $filterStatus === 'approved' ? 'btn-success' : 'btn-outline-secondary' ?>">Approved</a>
        <a href="beneficiaries.php?status=rejected" class="btn <?= $filterStatus === 'rejected' ? 'btn-danger' : 'btn-outline-secondary' ?>">Rejected</a>
    </div>
</div>

<!-- Applications List -->
<div class="card border-0 shadow-sm rounded-4 bg-white">
    <div class="card-body p-4">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Applicant Name</th>
                        <th>Contact Number</th>
                        <th>Category</th>
                        <th>Amount Requested</th>
                        <th>Hardship Details</th>
                        <th>Status</th>
                        <th class="text-end">Update Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($beneficiaries)): ?>
                        <tr><td colspan="7" class="text-center text-muted py-4">No beneficiary applications found.</td></tr>
                    <?php else: ?>
                        <?php foreach ($beneficiaries as $b): ?>
                        <tr>
                            <td class="fw-bold text-dark"><?= htmlspecialchars($b['full_name']) ?></td>
                            <td><i class="fa-solid fa-phone me-1 text-muted small"></i> <?= htmlspecialchars($b['contact_number']) ?></td>
                            <td><span class="badge bg-light text-dark border"><?= htmlspecialchars($b['assistance_type']) ?></span></td>
                            <td class="fw-bold text-success"><?= format_currency($b['requested_amount']) ?></td>
                            <td style="max-width: 280px;" class="small text-muted">
                                <?= htmlspecialchars(substr($b['details'], 0, 100)) ?>...
                            </td>
                            <td>
                                <span class="badge bg-<?= $b['status'] === 'approved' ? 'success' : ($b['status'] === 'rejected' ? 'danger' : 'warning') ?> text-uppercase">
                                    <?= $b['status'] ?>
                                </span>
                            </td>
                            <td class="text-end">
                                <form action="beneficiaries.php" method="POST" class="d-inline-flex gap-1">
                                    <?= csrf_input() ?>
                                    <input type="hidden" name="id" value="<?= $b['id'] ?>">
                                    <button type="submit" name="status" value="approved" class="btn btn-sm btn-outline-success" title="Approve Request">
                                        <i class="fa-solid fa-check"></i> Approve
                                    </button>
                                    <button type="submit" name="status" value="rejected" class="btn btn-sm btn-outline-danger" title="Reject Request">
                                        <i class="fa-solid fa-xmark"></i> Reject
                                    </button>
                                </form>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/admin_footer.php'; ?>
