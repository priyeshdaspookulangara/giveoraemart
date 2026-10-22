<?php
// donate.php - Standalone Donation Checkout Flow & Processor

require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/helpers.php';
require_once __DIR__ . '/controllers/DonationController.php';
require_once __DIR__ . '/models/CampaignModel.php';

// If POST request, delegate to controller handler
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    DonationController::handleCheckout();
    exit;
}

// GET Request: Render standalone donation checkout page
$activeCampaigns = CampaignModel::getAll('active');
$selectedCampaignId = (int)($_GET['campaign_id'] ?? 0);

require_once __DIR__ . '/includes/header.php';
?>

<div class="bg-white border-bottom py-4">
    <div class="container">
        <h1 class="fw-bold text-dark mb-1"><i class="fa-solid fa-heart text-danger me-2"></i>Make a Secure Donation</h1>
        <p class="text-muted mb-0">Your contribution goes directly towards life-changing community programs.</p>
    </div>
</div>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm rounded-4 p-4 p-md-5 bg-white">
                <form action="donate.php" method="POST">
                    <?= csrf_input() ?>

                    <!-- Select Campaign -->
                    <div class="mb-4">
                        <label for="campaign_id" class="form-label fw-bold">1. Select Campaign Drive</label>
                        <select class="form-select form-select-lg" id="campaign_id" name="campaign_id" required>
                            <option value="" disabled <?= $selectedCampaignId === 0 ? 'selected' : '' ?>>-- Choose a Campaign --</option>
                            <?php foreach ($activeCampaigns as $c): ?>
                                <option value="<?= $c['id'] ?>" <?= $selectedCampaignId === (int)$c['id'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($c['title']) ?> (Target: <?= format_currency($c['goal_amount']) ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <!-- Preset Amount Options -->
                    <div class="mb-4">
                        <label class="form-label fw-bold">2. Select or Enter Donation Amount ($)</label>
                        <div class="row g-2 mb-3">
                            <div class="col-6 col-sm-3"><button type="button" class="btn btn-outline-success w-100 py-2 fw-bold" onclick="setDonateAmount(25)">$25</button></div>
                            <div class="col-6 col-sm-3"><button type="button" class="btn btn-outline-success w-100 py-2 fw-bold" onclick="setDonateAmount(50)">$50</button></div>
                            <div class="col-6 col-sm-3"><button type="button" class="btn btn-outline-success w-100 py-2 fw-bold" onclick="setDonateAmount(100)">$100</button></div>
                            <div class="col-6 col-sm-3"><button type="button" class="btn btn-outline-success w-100 py-2 fw-bold" onclick="setDonateAmount(250)">$250</button></div>
                        </div>

                        <div class="input-group input-group-lg">
                            <span class="input-group-text">$</span>
                            <input type="number" step="0.01" min="1" class="form-control" id="donation_amount" name="amount" placeholder="50.00" required>
                        </div>
                    </div>

                    <!-- Payment Details Simulation -->
                    <div class="mb-4 border rounded-3 p-3 bg-light">
                        <label class="form-label fw-bold mb-2"><i class="fa-regular fa-credit-card me-1"></i> Payment Method (Simulated)</label>
                        <div class="d-flex gap-3 mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="payment_method" id="pay_card" value="card" checked>
                                <label class="form-check-label fw-medium" for="pay_card"><i class="fa-solid fa-credit-card me-1 text-primary"></i> Credit / Debit Card</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="payment_method" id="pay_paypal" value="paypal">
                                <label class="form-check-label fw-medium" for="pay_paypal"><i class="fa-brands fa-paypal me-1 text-info"></i> PayPal</label>
                            </div>
                        </div>

                        <div class="row g-2">
                            <div class="col-12">
                                <input type="text" class="form-control" placeholder="Cardholder Name (e.g. John Doe)" value="<?= is_logged_in() ? htmlspecialchars($_SESSION['user_name']) : '' ?>">
                            </div>
                            <div class="col-12">
                                <input type="text" class="form-control" placeholder="Card Number (e.g. 4000 1234 5678 9010)">
                            </div>
                            <div class="col-6">
                                <input type="text" class="form-control" placeholder="MM/YY">
                            </div>
                            <div class="col-6">
                                <input type="text" class="form-control" placeholder="CVC / CVV">
                            </div>
                        </div>
                    </div>

                    <!-- Anonymous Checkbox -->
                    <div class="form-check mb-4">
                        <input class="form-check-input" type="checkbox" id="is_anonymous" name="is_anonymous" value="1">
                        <label class="form-check-label text-muted" for="is_anonymous">
                            Make this donation anonymous on public donor lists.
                        </label>
                    </div>

                    <button type="submit" class="btn btn-success btn-lg w-100 rounded-pill fw-bold shadow">
                        <i class="fa-solid fa-lock me-2"></i> Complete Donation Now
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
function setDonateAmount(val) {
    document.getElementById('donation_amount').value = val;
}
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
