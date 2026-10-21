<?php
// includes/footer.php
?>
    </main>

    <!-- Footer -->
    <footer class="bg-slate-950 border-t border-slate-800 text-slate-400 text-sm mt-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8">

                <!-- Brand Info -->
                <div class="space-y-4">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-2xl bg-emerald-500 text-slate-950 flex items-center justify-center font-black text-xl shadow-lg shadow-emerald-500/20">
                            G
                        </div>
                        <div>
                            <span class="font-extrabold text-lg text-white">Giveoraemart</span>
                        </div>
                    </div>
                    <p class="text-xs text-slate-400 leading-relaxed">
                        Your trusted one-stop marketplace for daily essential repurchase groceries and premium household purchases. Quality checked and delivered fresh to your doorstep.
                    </p>
                </div>

                <!-- Quick Links -->
                <div>
                    <h3 class="text-xs font-bold uppercase tracking-wider text-white mb-4">Quick Navigation</h3>
                    <ul class="space-y-2 text-xs">
                        <li><a href="/shop.php" class="hover:text-emerald-400 transition">Browse Full Catalog</a></li>
                        <li><a href="/shop.php?purchase_type=REPURCHASE" class="hover:text-emerald-400 transition">Daily Repurchase Essentials</a></li>
                        <li><a href="/shop.php?type=package" class="hover:text-emerald-400 transition">Combo Package Deals</a></li>
                        <li><a href="/cart.php" class="hover:text-emerald-400 transition">My Cart</a></li>
                    </ul>
                </div>

                <!-- Customer Account & Guest -->
                <div>
                    <h3 class="text-xs font-bold uppercase tracking-wider text-white mb-4">Account & Support</h3>
                    <ul class="space-y-2 text-xs">
                        <li><a href="/login.php" class="hover:text-emerald-400 transition">Customer Login / Signup</a></li>
                        <li><a href="/account.php" class="hover:text-emerald-400 transition">Order History & Tracking</a></li>
                        <li><a href="/wishlist.php" class="hover:text-emerald-400 transition">Saved Wishlist</a></li>
                        <li><a href="/admin/login.php" class="text-slate-500 hover:text-emerald-400 transition">Admin Portal Access</a></li>
                    </ul>
                </div>

                <!-- Newsletter / Contact -->
                <div>
                    <h3 class="text-xs font-bold uppercase tracking-wider text-white mb-4">Stay Connected</h3>
                    <p class="text-xs text-slate-400 mb-3">Subscribe to get instant alerts on monthly promotional package bundle discounts.</p>
                    <div class="flex gap-2">
                        <input type="email" placeholder="Your email..." class="bg-slate-900 border border-slate-800 rounded-xl px-3 py-2 text-xs text-white focus:outline-none focus:border-emerald-500 w-full">
                        <button class="bg-emerald-500 hover:bg-emerald-600 text-slate-950 font-bold px-3 py-2 rounded-xl text-xs transition">Join</button>
                    </div>
                </div>

            </div>

            <div class="border-t border-slate-800/80 mt-10 pt-6 flex flex-col sm:flex-row items-center justify-between text-xs text-slate-500 gap-4">
                <p>&copy; <?= date('Y') ?> Giveoraemart. All rights reserved.</p>
                <div class="flex items-center gap-4">
                    <span><i class="fa-solid fa-shield-halved text-emerald-500 mr-1"></i> Secure SSL Checkout</span>
                    <span><i class="fa-solid fa-truck-fast text-emerald-500 mr-1"></i> Fast Delivery</span>
                </div>
            </div>
        </div>
    </footer>

    <!-- Global App JS -->
    <script src="/assets/js/app.js"></script>
</body>
</html>
