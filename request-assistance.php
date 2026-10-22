<?php
// request-assistance.php - Public Beneficiary Assistance Application

require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/helpers.php';
require_once __DIR__ . '/controllers/BeneficiaryController.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    BeneficiaryController::handleSubmit();
    exit;
}

require_once __DIR__ . '/includes/header.php';
?>

<div class="bg-white border-bottom py-4">
    <div class="container">
        <h1 class="fw-bold text-dark mb-1"><i class="fa-solid fa-hand-holding-hand text-success me-2"></i>Apply for Aid & Financial Assistance</h1>
        <p class="text-muted mb-0">Submit your request to our review committee for direct community assistance.</p>
    </div>
</div>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <?php if (!empty($_GET['success'])): ?>
                <div class="alert alert-success p-4 rounded-4 shadow-sm mb-4">
                    <h4 class="fw-bold mb-2"><i class="fa-solid fa-circle-check me-2"></i>Application Submitted!</h4>
                    <p class="mb-0">Thank you. Your request for assistance has been logged and sent to our beneficiary review board. We will reach out to you using your provided contact details.</p>
                </div>
            <?php endif; ?>

            <div class="card border-0 shadow-sm rounded-4 p-4 p-md-5 bg-white">
                <form action="request-assistance.php" method="POST">
                    <?= csrf_input() ?>

                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label for="full_name" class="form-label fw-bold">Full Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control form-control-lg" id="full_name" name="full_name" placeholder="e.g. Jane Doe" required>
                        </div>
                        <div class="col-md-6">
                            <label for="contact_number" class="form-label fw-bold">Contact Phone Number <span class="text-danger">*</span></label>
                            <input type="text" class="form-control form-control-lg" id="contact_number" name="contact_number" placeholder="e.g. +1 555-0199" required>
                        </div>
                        <div class="col-md-6">
                            <label for="assistance_type" class="form-label fw-bold">Assistance Category <span class="text-danger">*</span></label>
                            <select class="form-select form-select-lg" id="assistance_type" name="assistance_type" required>
                                <option value="" disabled selected>-- Select Type --</option>
                                <option value="Medical">Medical / Emergency Surgery</option>
                                <option value="Education">Education & Tuition Support</option>
                                <option value="Food Relief">Food & Nutrition Relief</option>
                                <option value="Housing">Housing / Shelter Support</option>
                                <option value="Emergency">Disaster & Emergency Relief</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="requested_amount" class="form-label fw-bold">Requested Amount ($) <span class="text-danger">*</span></label>
                            <div class="input-group input-group-lg">
                                <span class="input-group-text">$</span>
                                <input type="number" step="0.01" min="10" class="form-control" id="requested_amount" name="requested_amount" placeholder="e.g. 1500.00" required>
                            </div>
                        </div>
                        <div class="col-12">
                            <label for="details" class="form-label fw-bold">Detailed Explanation of Hardship & Need <span class="text-danger">*</span></label>
                            <textarea class="form-control" id="details" name="details" rows="5" placeholder="Please describe your current situation, how the funds will be used, and any supporting documentation you have..." required></textarea>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-success btn-lg w-100 rounded-pill fw-bold shadow">
                        <i class="fa-solid fa-paper-plane me-2"></i> Submit Assistance Application
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
