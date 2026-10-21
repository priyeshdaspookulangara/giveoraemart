// assets/js/app.js - Global JavaScript for Cart, Wishlist, Search, and Toast Notifications

document.addEventListener('DOMContentLoaded', () => {
    initWishlistBadge();
    initLiveSearch();
});

// Toast notification helper
function showToast(message, type = 'success') {
    let toastContainer = document.getElementById('toast-container');
    if (!toastContainer) {
        toastContainer = document.createElement('div');
        toastContainer.id = 'toast-container';
        toastContainer.className = 'fixed bottom-5 right-5 z-50 flex flex-col gap-2 max-w-sm pointer-events-none';
        document.body.appendChild(toastContainer);
    }

    const toast = document.createElement('div');
    const bgClass = type === 'success' ? 'bg-emerald-500 text-slate-950' : 'bg-rose-500 text-white';
    toast.className = `${bgClass} p-4 rounded-xl shadow-2xl font-semibold text-xs flex items-center justify-between gap-3 pointer-events-auto transform translate-y-4 opacity-0 transition-all duration-300`;
    toast.innerHTML = `
        <div class="flex items-center gap-2">
            <i class="fa-solid ${type === 'success' ? 'fa-circle-check' : 'fa-circle-exclamation'} text-base"></i>
            <span>${message}</span>
        </div>
    `;

    toastContainer.appendChild(toast);

    setTimeout(() => {
        toast.classList.remove('translate-y-4', 'opacity-0');
    }, 10);

    setTimeout(() => {
        toast.classList.add('translate-y-4', 'opacity-0');
        setTimeout(() => toast.remove(), 300);
    }, 3000);
}

// Add Item to Cart via Fetch API
async function addToCart(type, id, quantity = 1) {
    try {
        const formData = new FormData();
        formData.append('action', 'add');
        formData.append('type', type);
        formData.append('id', id);
        formData.append('quantity', quantity);

        const res = await fetch('/api/cart.php', {
            method: 'POST',
            body: formData
        });

        const data = await res.json();
        if (data.success) {
            // Update Cart Counter Badge in Nav
            const badge = document.getElementById('nav-cart-count');
            if (badge) badge.innerText = data.cart_count;

            showToast(data.message || 'Added to cart successfully!');
        } else {
            showToast(data.message || 'Could not add item to cart.', 'error');
        }
    } catch (err) {
        console.error(err);
        showToast('Network error while updating cart.', 'error');
    }
}

// Wishlist Toggle (Like/Unlike) via Fetch API
async function toggleWishlist(type, id, btnElement) {
    try {
        const formData = new FormData();
        formData.append('type', type);
        formData.append('id', id);

        const res = await fetch('/api/wishlist.php', {
            method: 'POST',
            body: formData
        });

        const data = await res.json();
        if (data.success) {
            if (btnElement) {
                const icon = btnElement.querySelector('i');
                if (data.liked) {
                    btnElement.classList.add('text-rose-500');
                    btnElement.classList.remove('text-slate-400');
                    if (icon) {
                        icon.classList.remove('fa-regular');
                        icon.classList.add('fa-solid');
                    }
                } else {
                    btnElement.classList.remove('text-rose-500');
                    btnElement.classList.add('text-slate-400');
                    if (icon) {
                        icon.classList.remove('fa-solid');
                        icon.classList.add('fa-regular');
                    }
                }
            }

            updateWishlistBadgeCount(data.wishlist_count);
            showToast(data.message);
        }
    } catch (err) {
        console.error(err);
        showToast('Could not update wishlist.', 'error');
    }
}

// Fetch Initial Wishlist Badge Count
async function initWishlistBadge() {
    try {
        const res = await fetch('/api/wishlist.php?action=count');
        const data = await res.json();
        if (data.success) {
            updateWishlistBadgeCount(data.count);
        }
    } catch (e) {}
}

function updateWishlistBadgeCount(count) {
    const badge = document.getElementById('wishlist-badge');
    if (badge) {
        if (count > 0) {
            badge.innerText = count;
            badge.classList.remove('hidden');
        } else {
            badge.classList.add('hidden');
        }
    }
}

// Real-time Store Header Search
function initLiveSearch() {
    const searchInput = document.getElementById('store-search-input');
    const dropdown = document.getElementById('search-results-dropdown');
    const list = document.getElementById('search-results-list');
    const clearBtn = document.getElementById('search-clear-btn');

    if (!searchInput || !dropdown || !list) return;

    let debounceTimer;

    searchInput.addEventListener('input', () => {
        const query = searchInput.value.trim();
        if (clearBtn) clearBtn.classList.toggle('hidden', query === '');

        clearTimeout(debounceTimer);
        if (query.length < 2) {
            dropdown.classList.add('hidden');
            return;
        }

        debounceTimer = setTimeout(async () => {
            try {
                const res = await fetch(`/api/search.php?q=${encodeURIComponent(query)}`);
                const data = await res.json();

                if (data.success && data.results.length > 0) {
                    list.innerHTML = data.results.map(item => `
                        <a href="${item.url}" class="flex items-center gap-3 p-2 hover:bg-slate-800 rounded-xl transition">
                            <img src="${item.image}" class="w-10 h-10 object-cover rounded-lg bg-slate-950 flex-shrink-0">
                            <div class="flex-1 min-w-0">
                                <h4 class="text-xs font-semibold text-white truncate">${item.title}</h4>
                                <span class="text-[10px] text-slate-400 font-medium">${item.type_badge}</span>
                            </div>
                            <span class="text-xs font-bold text-emerald-400">$${item.price}</span>
                        </a>
                    `).join('');
                    dropdown.classList.remove('hidden');
                } else {
                    list.innerHTML = `<div class="p-4 text-center text-xs text-slate-500">No matching items found for "${query}"</div>`;
                    dropdown.classList.remove('hidden');
                }
            } catch (err) {
                console.error(err);
            }
        }, 250);
    });

    if (clearBtn) {
        clearBtn.addEventListener('click', () => {
            searchInput.value = '';
            clearBtn.classList.add('hidden');
            dropdown.classList.add('hidden');
        });
    }

    document.addEventListener('click', (e) => {
        if (!searchInput.contains(e.target) && !dropdown.contains(e.target)) {
            dropdown.classList.add('hidden');
        }
    });
}
