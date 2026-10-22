<?php
// register.php - User Registration

require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/helpers.php';
require_once __DIR__ . '/controllers/AuthController.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    AuthController::handleRegister();
    exit;
}

if (is_logged_in()) {
    header('Location: index.php');
    exit;
}

require_once __DIR__ . '/includes/header.php';
?>

<div class="container py-5 my-4">
    <div class="row justify-content-center">
        <div class="col-md-7 col-lg-6">
            <div class="card border-0 shadow-lg rounded-4 overflow-hidden bg-white">
                <div class="bg-success text-white text-center p-4">
                    <i class="fa-solid fa-user-plus fa-3x mb-2"></i>
                    <h3 class="fw-bold mb-0">Create New Account</h3>
                </div>
                <div class="card-body p-4 p-md-5">
                    <form action="register.php" method="POST">
                        <?= csrf_input() ?>

                        <div class="mb-3">
                            <label for="name" class="form-label fw-bold">Full Name</label>
                            <input type="text" class="form-control form-control-lg" id="name" name="name" placeholder="John Doe" required>
                        </div>

                        <div class="mb-3">
                            <label for="email" class="form-label fw-bold">Email Address</label>
                            <input type="email" class="form-control form-control-lg" id="email" name="email" placeholder="john@example.com" required>
                        </div>

                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label for="password" class="form-label fw-bold">Password</label>
                                <input type="password" class="form-control form-control-lg" id="password" name="password" placeholder="••••••••" required>
                            </div>
                            <div class="col-md-6">
                                <label for="confirm_password" class="form-label fw-bold">Confirm Password</label>
                                <input type="password" class="form-control form-control-lg" id="confirm_password" name="confirm_password" placeholder="••••••••" required>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label for="role" class="form-label fw-bold">I am joining as a:</label>
                            <select class="form-select form-select-lg" id="role" name="role">
                                <option value="donor" selected>Donor / Supporter</option>
                                <option value="beneficiary">Beneficiary / Aid Applicant</option>
                            </select>
                        </div>

                        <button type="submit" class="btn btn-success btn-lg w-100 rounded-pill fw-bold mb-3 shadow">
                            <i class="fa-solid fa-check-circle me-2"></i> Register Account
                        </button>
                    </form>

                    <div class="text-center mt-3 small text-muted">
                        Already registered? <a href="login.php" class="text-success fw-bold text-decoration-none">Log in here</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
