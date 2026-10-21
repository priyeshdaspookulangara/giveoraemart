<?php
// admin/login.php
require_once __DIR__ . '/../includes/functions.php';

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = sanitize($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if (!empty($email) && !empty($password)) {
        if ($email === 'admin@giveoraemart.com' && $password === 'admin123') {
            $_SESSION['user'] = [
                'id' => 1,
                'name' => 'Giveorae Admin',
                'email' => 'admin@giveoraemart.com',
                'role' => 'ADMIN'
            ];
            header('Location: /admin/index.php');
            exit;
        } else {
            $error = 'Invalid email or password for Admin portal.';
        }
    } else {
        $error = 'Please fill in all fields.';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - Giveoraemart</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-slate-900 text-slate-100 flex items-center justify-center min-h-screen">

<div class="max-w-md w-full bg-slate-800 border border-slate-700 rounded-2xl p-8 shadow-2xl">
    <div class="text-center mb-8">
        <div class="inline-flex items-center justify-center w-16 h-16 bg-emerald-500/10 text-emerald-400 rounded-2xl mb-4 border border-emerald-500/20">
            <i class="fa-solid fa-user-shield text-2xl"></i>
        </div>
        <h1 class="text-2xl font-bold tracking-tight text-white">Giveoraemart Admin</h1>
        <p class="text-slate-400 text-sm mt-1">Sign in to access management dashboard</p>
    </div>

    <?php if ($error): ?>
        <div class="mb-6 p-4 bg-rose-500/10 border border-rose-500/30 text-rose-400 text-sm rounded-xl flex items-center gap-3">
            <i class="fa-solid fa-circle-exclamation text-base"></i>
            <span><?= $error ?></span>
        </div>
    <?php endif; ?>

    <form method="POST" class="space-y-5">
        <div>
            <label class="block text-xs font-semibold uppercase tracking-wider text-slate-400 mb-2">Email Address</label>
            <div class="relative">
                <i class="fa-regular fa-envelope absolute left-4 top-3.5 text-slate-500"></i>
                <input type="email" name="email" value="admin@giveoraemart.com" required
                       class="w-full bg-slate-900 border border-slate-700 rounded-xl pl-11 pr-4 py-3 text-white placeholder-slate-500 focus:outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 transition">
            </div>
        </div>

        <div>
            <label class="block text-xs font-semibold uppercase tracking-wider text-slate-400 mb-2">Password</label>
            <div class="relative">
                <i class="fa-solid fa-lock absolute left-4 top-3.5 text-slate-500"></i>
                <input type="password" name="password" value="admin123" required
                       class="w-full bg-slate-900 border border-slate-700 rounded-xl pl-11 pr-4 py-3 text-white placeholder-slate-500 focus:outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 transition">
            </div>
        </div>

        <button type="submit" class="w-full bg-emerald-500 hover:bg-emerald-600 text-slate-950 font-semibold py-3 px-4 rounded-xl transition shadow-lg shadow-emerald-500/20 flex items-center justify-center gap-2">
            <span>Sign In to Admin</span>
            <i class="fa-solid fa-arrow-right text-sm"></i>
        </button>
    </form>

    <div class="mt-6 text-center">
        <a href="/" class="text-xs text-slate-400 hover:text-emerald-400 transition inline-flex items-center gap-1">
            <i class="fa-solid fa-store"></i> Return to Storefront
        </a>
    </div>
</div>

</body>
</html>
