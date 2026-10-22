<?php
// donation-success.php - Confirmation & Receipt Page

require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/models/DonationModel.php';

$txnId = $_GET['txn'] ?? '';
$donation = !empty($txnId) ? DonationModel::getByTransactionId($txnId) : null;

if (!$donation):
?>
<div class="container py-5 my-5 text-center">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <i class="fa-solid fa-circle-question fa-4x text-muted mb-3"></i>
            <h2 class="fw-bold text-dark">Transaction Not Found</h2>
            <p class="text-muted">We could not retrieve details for transaction ID: <code><?= htmlspecialchars($txnId) ?></code></p>
            <a href="index.php" class="btn btn-success rounded-pill px-4 mt-2">Return Home</a>
        </div>
    </div>
</div>
<?php else: ?>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-7">
            <div class="card border-0 shadow-lg rounded-4 overflow-hidden bg-white">
                <div class="bg-success text-white p-4 text-center">
                    <i class="fa-solid fa-circle-check fa-4x mb-2"></i>
                    <h2 class="fw-bold mb-1">Thank You For Your Support!</h2>
                    <p class="lead mb-0 opacity-90">Your donation has been successfully processed.</p>
                </div>
                <div class="card-body p-4 p-md-5">
                    <h5 class="fw-bold text-dark border-bottom pb-2 mb-3"><i class="fa-solid fa-receipt me-2 text-success"></i>Donation Receipt</h5>

                    <div class="row g-3 mb-4">
                        <div class="col-6">
                            <span class="text-muted small d-block">Transaction ID:</span>
                            <span class="fw-bold text-dark font-monospace"><?= htmlspecialchars($donation['transaction_id']) ?></span>
                        </div>
                        <div class="col-6">
                            <span class="text-muted small d-block">Date & Time:</span>
                            <span class="fw-bold text-dark"><?= date('M j, Y - g:i A', strtotime($donation['donated_at'])) ?></span>
                        </div>
                        <div class="col-6">
                            <span class="text-muted small d-block">Campaign:</span>
                            <a href="campaign-detail.php?slug=<?= urlencode($donation['campaign_slug']) ?>" class="fw-bold text-success text-decoration-none">
                                <?= htmlspecialchars($donation['campaign_title']) ?>
                            </a>
                        </div>
                        <div class="col-6">
                            <span class="text-muted small d-block">Amount Donated:</span>
                            <span class="fw-bold text-success fs-5"><?= format_currency($donation['amount']) ?></span>
                        </div>
                        <div class="col-6">
                            <span class="text-muted small d-block">Donor Name:</span>
                            <span class="fw-bold text-dark"><?= !empty($donation['donor_name']) ? htmlspecialchars($donation['donor_name']) : 'Anonymous Supporter' ?></span>
                        </div>
                        <div class="col-6">
                            <span class="text-muted small d-block">Payment Status:</span>
                            <span class="badge bg-success text-uppercase px-3 py-2"><?= htmlspecialchars($donation['payment_status']) ?></span>
                        </div>
                    </div>

                    <div class="alert alert-light border rounded-3 p-3 mb-4 small text-muted">
                        <i class="fa-solid fa-info-circle me-1 text-primary"></i> A confirmation receipt has been sent to your email address on file. Thank you for making a real impact today.
                    </div>

                    <div class="d-flex flex-wrap gap-2">
                        <a href="campaigns.php" class="btn btn-outline-success rounded-pill px-4 flex-grow-1">Browse More Campaigns</a>
                        <a href="index.php" class="btn btn-success rounded-pill px-4 flex-grow-1 fw-bold">Return Home</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
endif;
require_once __DIR__ . '/includes/footer.php';
?>
