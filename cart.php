<?php
// cart.php
require_once __DIR__ . '/includes/functions.php';

$pageTitle = 'Shopping Cart - Giveoraemart';
$cartDetails = get_cart_details();

require_once __DIR__ . '/includes/header.php';
?>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">

    <div class="mb-8 border-b border-slate-800 pb-6 flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-extrabold text-white tracking-tight">Your Shopping Cart</h1>
            <p class="text-slate-400 text-sm mt-1">Review selected grocery consumables, housewares, and package bundles before checkout.</p>
        </div>
        <a href="/shop.php" class="text-xs font-semibold text-emerald-400 hover:text-emerald-300 transition flex items-center gap-1">
            <i class="fa-solid fa-arrow-left text-[10px]"></i> Continue Shopping
        </a>
    </div>

    <?php if (empty($cartDetails['items'])): ?>
        <div class="bg-slate-900 border border-slate-800 rounded-3xl p-16 text-center max-w-xl mx-auto">
            <div class="w-16 h-16 bg-slate-800 text-slate-500 rounded-full flex items-center justify-center mx-auto mb-4 text-2xl">
                <i class="fa-solid fa-cart-shopping"></i>
            </div>
            <h2 class="text-xl font-bold text-white mb-2">Your cart is currently empty</h2>
            <p class="text-xs text-slate-400 mb-6">Explore our fresh repurchase groceries or promotional package bundles to start shopping.</p>
            <a href="/shop.php" class="inline-flex items-center gap-2 bg-emerald-500 hover:bg-emerald-600 text-slate-950 font-bold px-6 py-3 rounded-xl transition text-xs shadow-lg shadow-emerald-500/20">
                <span>Browse Store Items</span>
                <i class="fa-solid fa-arrow-right"></i>
            </a>
        </div>
    <?php else: ?>
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-10">

            <!-- Cart Items List -->
            <div class="lg:col-span-2 space-y-4">
                <div class="bg-slate-900 border border-slate-800 rounded-2xl p-6 divide-y divide-slate-800">
                    <?php foreach ($cartDetails['items'] as $item): ?>
                        <div class="py-4 first:pt-0 last:pb-0 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">

                            <div class="flex items-center gap-4">
                                <img src="<?= htmlspecialchars($item['image_url']) ?>" class="w-16 h-16 object-cover rounded-xl bg-slate-950 flex-shrink-0 border border-slate-800">
                                <div>
                                    <h3 class="font-bold text-white text-sm line-clamp-1">
                                        <?php if ($item['type'] === 'PRODUCT'): ?>
                                            <a href="/product.php?id=<?= $item['id'] ?>" class="hover:text-emerald-400 transition"><?= htmlspecialchars($item['name']) ?></a>
                                        <?php else: ?>
                                            <a href="/package.php?id=<?= $item['id'] ?>" class="hover:text-emerald-400 transition"><?= htmlspecialchars($item['name']) ?></a>
                                        <?php endif; ?>
                                    </h3>

                                    <div class="flex items-center gap-2 mt-1">
                                        <span class="text-xs text-emerald-400 font-bold"><?= format_price($item['price']) ?></span>
                                        <span class="text-[10px] uppercase font-bold text-slate-500 bg-slate-950 px-2 py-0.5 rounded border border-slate-800">
                                            <?= $item['purchase_type'] ?>
                                        </span>
                                    </div>
                                </div>
                            </div>

                            <!-- Quantity Controls & Subtotal -->
                            <div class="flex items-center justify-between sm:justify-end w-full sm:w-auto gap-6">
                                <div class="flex items-center bg-slate-950 border border-slate-800 rounded-xl px-2 py-1">
                                    <button onclick="updateCartItem('<?= $item['type'] ?>', <?= $item['id'] ?>, <?= $item['quantity'] - 1 ?>)" class="text-slate-400 hover:text-white px-2 py-0.5 text-xs font-bold">-</button>
                                    <span class="text-xs font-bold text-white w-8 text-center"><?= $item['quantity'] ?></span>
                                    <button onclick="updateCartItem('<?= $item['type'] ?>', <?= $item['id'] ?>, <?= $item['quantity'] + 1 ?>)" class="text-slate-400 hover:text-white px-2 py-0.5 text-xs font-bold">+</button>
                                </div>

                                <div class="text-right min-w-[80px]">
                                    <span class="text-sm font-extrabold text-white block"><?= format_price($item['subtotal']) ?></span>
                                    <button onclick="removeCartItem('<?= $item['type'] ?>', <?= $item['id'] ?>)" class="text-[11px] text-slate-500 hover:text-rose-400 transition">
                                        <i class="fa-solid fa-trash mr-1"></i> Remove
                                    </button>
                                </div>
                            </div>

                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Order Summary & Checkout Callout -->
            <div class="bg-slate-900 border border-slate-800 rounded-2xl p-6 h-fit space-y-6">
                <h2 class="text-lg font-bold text-white border-b border-slate-800 pb-4">Order Summary</h2>

                <div class="space-y-3 text-xs text-slate-300">
                    <div class="flex justify-between">
                        <span>Items Subtotal (<?= $cartDetails['count'] ?> items):</span>
                        <span class="font-bold text-white"><?= format_price($cartDetails['total']) ?></span>
                    </div>
                    <div class="flex justify-between text-emerald-400">
                        <span>Estimated Standard Shipping:</span>
                        <span class="font-bold"><?= $cartDetails['total'] >= 50 ? 'FREE' : '$4.99' ?></span>
                    </div>
                </div>

                <div class="border-t border-slate-800 pt-4 flex items-center justify-between">
                    <span class="font-extrabold text-white text-base">Estimated Total:</span>
                    <span class="font-black text-2xl text-emerald-400"><?= format_price($cartDetails['total'] + ($cartDetails['total'] >= 50 ? 0 : 4.99)) ?></span>
                </div>

                <a href="/checkout.php" class="w-full bg-emerald-500 hover:bg-emerald-600 text-slate-950 font-black py-4 px-6 rounded-2xl transition text-sm flex items-center justify-center gap-2 shadow-xl shadow-emerald-500/20">
                    <span>Proceed to Checkout</span>
                    <i class="fa-solid fa-arrow-right"></i>
                </a>

                <div class="text-[11px] text-slate-500 text-center flex items-center justify-center gap-2">
                    <i class="fa-solid fa-lock text-emerald-500"></i> Guest & registered checkout options available
                </div>
            </div>

        </div>
    <?php endif; ?>

</div>

<script>
async function updateCartItem(type, id, newQty) {
    if (newQty < 1) {
        return removeCartItem(type, id);
    }
    const formData = new FormData();
    formData.append('action', 'update');
    formData.append('type', type);
    formData.append('id', id);
    formData.append('quantity', newQty);

    const res = await fetch('/api/cart.php', { method: 'POST', body: formData });
    const data = await res.json();
    if (data.success) {
        window.location.reload();
    }
}

async function removeCartItem(type, id) {
    const formData = new FormData();
    formData.append('action', 'remove');
    formData.append('type', type);
    formData.append('id', id);

    const res = await fetch('/api/cart.php', { method: 'POST', body: formData });
    const data = await res.json();
    if (data.success) {
        window.location.reload();
    }
}
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
