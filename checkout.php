<?php
// checkout.php
require_once __DIR__ . '/includes/functions.php';

$pageTitle = 'Checkout - Giveoraemart';
$cartDetails = get_cart_details();
$currentUser = get_logged_in_user();

if (empty($cartDetails['items'])) {
    header('Location: /cart.php');
    exit;
}

$shippingFee = $cartDetails['total'] >= 50 ? 0.00 : 4.99;
$grandTotal = $cartDetails['total'] + $shippingFee;

require_once __DIR__ . '/includes/header.php';
?>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">

    <div class="mb-8 border-b border-slate-800 pb-6">
        <h1 class="text-3xl font-extrabold text-white tracking-tight">Checkout</h1>
        <p class="text-slate-400 text-sm mt-1">Complete your order as guest or logged in user.</p>
    </div>

    <form action="api/checkout.php" method="POST" class="grid grid-cols-1 lg:grid-cols-3 gap-10">

        <!-- Contact & Delivery Details (Left 2 cols) -->
        <div class="lg:col-span-2 space-y-6">

            <div class="bg-slate-900 border border-slate-800 rounded-2xl p-6 space-y-4">
                <div class="flex items-center justify-between border-b border-slate-800 pb-4">
                    <h2 class="text-lg font-bold text-white flex items-center gap-2">
                        <i class="fa-solid fa-address-card text-emerald-400"></i> Contact Information
                    </h2>
                    <?php if (!$currentUser): ?>
                        <span class="text-xs text-slate-400">Already registered? <a href="/login.php?redirect=checkout" class="text-emerald-400 hover:underline">Sign in</a></span>
                    <?php endif; ?>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold uppercase tracking-wider text-slate-400 mb-2">Full Name *</label>
                        <input type="text" name="guest_name" value="<?= htmlspecialchars($currentUser['name'] ?? '') ?>" required placeholder="e.g. Sarah Jenkins"
                               class="w-full bg-slate-950 border border-slate-800 rounded-xl px-4 py-3 text-white placeholder-slate-500 focus:outline-none focus:border-emerald-500 text-sm">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold uppercase tracking-wider text-slate-400 mb-2">Email Address *</label>
                        <input type="email" name="guest_email" value="<?= htmlspecialchars($currentUser['email'] ?? '') ?>" required placeholder="sarah@example.com"
                               class="w-full bg-slate-950 border border-slate-800 rounded-xl px-4 py-3 text-white placeholder-slate-500 focus:outline-none focus:border-emerald-500 text-sm">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-slate-400 mb-2">Phone Number *</label>
                    <input type="tel" name="guest_phone" required placeholder="+1 (555) 000-1234"
                           class="w-full bg-slate-950 border border-slate-800 rounded-xl px-4 py-3 text-white placeholder-slate-500 focus:outline-none focus:border-emerald-500 text-sm">
                </div>
            </div>

            <div class="bg-slate-900 border border-slate-800 rounded-2xl p-6 space-y-4">
                <h2 class="text-lg font-bold text-white flex items-center gap-2 border-b border-slate-800 pb-4">
                    <i class="fa-solid fa-truck-ramp-box text-emerald-400"></i> Delivery Address
                </h2>

                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-slate-400 mb-2">Shipping Street Address *</label>
                    <textarea name="shipping_address" rows="3" required placeholder="123 Orchard Road, Suite 4B, City, Country, ZIP..."
                              class="w-full bg-slate-950 border border-slate-800 rounded-xl px-4 py-3 text-white placeholder-slate-500 focus:outline-none focus:border-emerald-500 text-sm"></textarea>
                </div>
            </div>

            <!-- Payment Selection -->
            <div class="bg-slate-900 border border-slate-800 rounded-2xl p-6 space-y-4">
                <h2 class="text-lg font-bold text-white flex items-center gap-2 border-b border-slate-800 pb-4">
                    <i class="fa-solid fa-credit-card text-emerald-400"></i> Payment Selection
                </h2>

                <div class="space-y-3">
                    <label class="bg-slate-950 p-4 rounded-xl border border-slate-800 flex items-center gap-4 cursor-pointer hover:border-emerald-500/50 transition block">
                        <input type="radio" name="payment_method" value="COD" checked class="w-4 h-4 text-emerald-500 accent-emerald-500">
                        <div>
                            <span class="font-bold text-sm text-white block">Cash On Delivery / Card At Doorstep</span>
                            <span class="text-xs text-slate-400">Pay safely upon delivery fulfillment.</span>
                        </div>
                    </label>

                    <label class="bg-slate-950 p-4 rounded-xl border border-slate-800 flex items-center gap-4 cursor-pointer hover:border-emerald-500/50 transition block">
                        <input type="radio" name="payment_method" value="UPI" class="w-4 h-4 text-emerald-500 accent-emerald-500">
                        <div>
                            <span class="font-bold text-sm text-white block flex items-center gap-2">
                                Instant UPI Payment <span class="bg-emerald-500/20 text-emerald-400 text-[10px] px-2 py-0.5 rounded font-extrabold uppercase">Fast</span>
                            </span>
                            <span class="text-xs text-slate-400">Pay using GPay, PhonePe, Paytm or any UPI App & verify instantly.</span>
                        </div>
                    </label>
                </div>
            </div>

        </div>

        <!-- Order Summary Side Card -->
        <div class="bg-slate-900 border border-slate-800 rounded-2xl p-6 h-fit space-y-6">
            <h2 class="text-lg font-bold text-white border-b border-slate-800 pb-4">Items In Order</h2>

            <div class="space-y-3 max-h-60 overflow-y-auto pr-1">
                <?php foreach ($cartDetails['items'] as $item): ?>
                    <div class="flex items-center gap-3 bg-slate-950 p-2.5 rounded-xl border border-slate-800">
                        <img src="<?= htmlspecialchars($item['image_url']) ?>" class="w-10 h-10 object-cover rounded-lg bg-slate-900">
                        <div class="flex-1 min-w-0">
                            <h4 class="text-xs font-bold text-white truncate"><?= htmlspecialchars($item['name']) ?></h4>
                            <span class="text-[10px] text-slate-400 font-medium">Qty: <?= $item['quantity'] ?></span>
                        </div>
                        <span class="text-xs font-extrabold text-emerald-400"><?= format_price($item['subtotal']) ?></span>
                    </div>
                <?php endforeach; ?>
            </div>

            <div class="space-y-2 text-xs text-slate-300 border-t border-slate-800 pt-4">
                <div class="flex justify-between">
                    <span>Subtotal:</span>
                    <span class="font-bold text-white"><?= format_price($cartDetails['total']) ?></span>
                </div>
                <div class="flex justify-between text-emerald-400">
                    <span>Shipping Fee:</span>
                    <span class="font-bold"><?= $shippingFee == 0 ? 'FREE' : format_price($shippingFee) ?></span>
                </div>
            </div>

            <div class="border-t border-slate-800 pt-4 flex items-center justify-between">
                <span class="font-extrabold text-white text-base">Grand Total:</span>
                <span class="font-black text-2xl text-emerald-400"><?= format_price($grandTotal) ?></span>
            </div>

            <button type="submit" class="w-full bg-emerald-500 hover:bg-emerald-600 text-slate-950 font-black py-4 px-6 rounded-2xl transition text-sm flex items-center justify-center gap-2 shadow-xl shadow-emerald-500/20">
                <i class="fa-solid fa-lock"></i>
                <span>Confirm & Place Order</span>
            </button>
        </div>

    </form>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
