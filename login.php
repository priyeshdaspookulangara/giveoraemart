<?php
// login.php
require_once __DIR__ . '/includes/functions.php';

$error = '';
$redirect = sanitize($_GET['redirect'] ?? '');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = sanitize($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if (!empty($email) && !empty($password)) {
        $pdo = get_db_connection();
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user'] = [
                'id' => $user['id'],
                'name' => $user['name'],
                'email' => $user['email'],
                'role' => $user['role']
            ];

            if ($redirect === 'checkout') {
                header('Location: /checkout.php');
            } elseif ($user['role'] === 'ADMIN') {
                header('Location: /admin/index.php');
            } else {
                header('Location: /account.php');
            }
            exit;
        } else {
            $error = 'Invalid email or password.';
        }
    } else {
        $error = 'Please fill in all fields.';
    }
}

$pageTitle = 'Customer Login - Giveoraemart';
require_once __DIR__ . '/includes/header.php';
?>

<div class="max-w-md mx-auto my-16 bg-slate-900 border border-slate-800 rounded-3xl p-8 shadow-2xl">
    <div class="text-center mb-8">
        <h1 class="text-2xl font-bold text-white">Sign In To Your Account</h1>
        <p class="text-xs text-slate-400 mt-1">Access order history, saved addresses, and repurchase list</p>
    </div>

    <?php if ($error): ?>
        <div class="mb-6 p-4 bg-rose-500/10 border border-rose-500/30 text-rose-400 text-xs rounded-xl flex items-center gap-3">
            <i class="fa-solid fa-circle-exclamation text-sm"></i>
            <span><?= $error ?></span>
        </div>
    <?php endif; ?>

    <form method="POST" class="space-y-4">
        <div>
            <label class="block text-xs font-semibold uppercase tracking-wider text-slate-400 mb-2">Email Address</label>
            <input type="email" name="email" value="john@example.com" required
                   class="w-full bg-slate-950 border border-slate-800 rounded-xl px-4 py-3 text-white placeholder-slate-500 text-xs focus:outline-none focus:border-emerald-500">
        </div>

        <div>
            <label class="block text-xs font-semibold uppercase tracking-wider text-slate-400 mb-2">Password</label>
            <input type="password" name="password" value="customer123" required
                   class="w-full bg-slate-950 border border-slate-800 rounded-xl px-4 py-3 text-white placeholder-slate-500 text-xs focus:outline-none focus:border-emerald-500">
        </div>

        <button type="submit" class="w-full bg-emerald-500 hover:bg-emerald-600 text-slate-950 font-bold py-3.5 px-4 rounded-xl transition text-xs shadow-lg shadow-emerald-500/20">
            Sign In
        </button>
    </form>

    <div class="mt-6 text-center text-xs text-slate-400">
        Don't have an account? <a href="/register.php" class="text-emerald-400 font-bold hover:underline">Register Now</a>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
