<?php
// login.php - User & Admin Login

require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/helpers.php';
require_once __DIR__ . '/controllers/AuthController.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    AuthController::handleLogin();
    exit;
}

if (is_logged_in()) {
    $user = current_user();
    if (in_array($user['role'], ['admin', 'editor'])) {
        header('Location: admin/index.php');
    } else {
        header('Location: index.php');
    }
    exit;
}

require_once __DIR__ . '/includes/header.php';
?>

<div class="container py-5 my-4">
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-5">
            <div class="card border-0 shadow-lg rounded-4 overflow-hidden bg-white">
                <div class="bg-success text-white text-center p-4">
                    <i class="fa-solid fa-lock fa-3x mb-2"></i>
                    <h3 class="fw-bold mb-0">Sign In to Your Account</h3>
                </div>
                <div class="card-body p-4 p-md-5">
                    <form action="login.php" method="POST">
                        <?= csrf_input() ?>

                        <div class="mb-3">
                            <label for="email" class="form-label fw-bold">Email Address</label>
                            <input type="email" class="form-control form-control-lg" id="email" name="email" placeholder="admin@charity.org" required>
                        </div>

                        <div class="mb-4">
                            <label for="password" class="form-label fw-bold">Password</label>
                            <input type="password" class="form-control form-control-lg" id="password" name="password" placeholder="••••••••" required>
                        </div>

                        <button type="submit" class="btn btn-success btn-lg w-100 rounded-pill fw-bold mb-3 shadow">
                            <i class="fa-solid fa-right-to-bracket me-2"></i> Log In
                        </button>
                    </form>

                    <div class="text-center mt-3 small text-muted">
                        Don't have an account yet? <a href="register.php" class="text-success fw-bold text-decoration-none">Register here</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
