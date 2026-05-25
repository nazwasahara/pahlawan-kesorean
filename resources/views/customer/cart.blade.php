@extends('customer.layouts.app')

@section('title', 'Keranjang Belanja - Pahlawan Kesorean')

@section('customer-content')
<div class="py-10">
    <!-- Header with Back and Close -->
    <div class="flex items-center justify-between mb-6">
        <a href="{{ route('customer.menu') }}" class="text-neutral-800 hover:text-neutral-600">
            <i class="ri-arrow-left-line text-xl"></i>
        </a>
        <h2 class="text-lg font-extrabold text-neutral-800">Keranjang Saya</h2>
        <a href="{{ route('customer.menu') }}" class="text-neutral-800 hover:text-neutral-600">
            <i class="ri-close-line text-xl"></i>
        </a>
    </div>

    @if(session('error'))
        <div class="mb-4 p-3 rounded-2xl bg-red-50 border border-red-200 text-red-800 text-xs font-semibold flex items-center gap-2">
            <i class="ri-error-warning-line text-base text-red-600"></i>
            {{ session('error') }}
        </div>
    @endif
    @if(session('success'))
        <div class="mb-4 p-3 rounded-2xl bg-green-50 border border-green-200 text-green-800 text-xs font-semibold flex items-center gap-2">
            <i class="ri-checkbox-circle-line text-base text-green-600"></i>
            {{ session('success') }}
        </div>
    @endif

    @if($items->count() === 0)
        <!-- Empty Cart Placeholder -->
        <div class="bg-white rounded-[2rem] p-8 shadow-sm border border-neutral-200/50 text-center flex flex-col items-center justify-center my-6">
            <div class="bg-[#133122]/10 p-6 rounded-full text-[#133122] mb-4">
                <i class="ri-shopping-cart-2-line text-5xl"></i>
            </div>
            <h3 class="font-extrabold text-neutral-800 text-lg">Keranjangmu Kosong</h3>
            <p class="text-xs text-neutral-500 mt-2 max-w-[240px] leading-relaxed">
                Sepertinya kamu belum memilih hidangan lezat. Yuk, lihat menu favorit kami sekarang!
            </p>

            <a href="{{ route('customer.menu') }}" class="mt-6 px-6 py-3 bg-[#133122] text-[#F4F3EB] font-bold text-xs rounded-xl shadow-md hover:bg-[#1c4430] hover:scale-105 transition-all duration-200 uppercase tracking-wider">
                Lihat Menu Kami
            </a>
        </div>
    @else
        <!-- Notification Banner -->
        <div class="bg-[#e5ebe4] rounded-2xl p-3 flex items-center gap-3 mb-4 border border-[#d6dfd4]">
            <div class="bg-[#133122] text-white p-2 rounded-xl">
                <i class="ri-restaurant-2-line text-lg"></i>
            </div>
            <div>
                <h4 class="font-extrabold text-[#133122] text-[11px] leading-tight">Yuk, Selesaikan Pesananmu!</h4>
                <p class="text-[9px] text-[#133122]/80 mt-0.5 font-semibold">Makanan Enak Sedang Menunggumu!!</p>
            </div>
        </div>

        <!-- Cart Items List -->
        <div id="cart-items-container" class="space-y-3 mb-5">
            @foreach($items as $item)
                <div id="cart-item-card-{{ $item->id }}" class="bg-white rounded-3xl p-3 shadow-sm border border-neutral-200/50 flex flex-row items-center justify-between gap-3 relative min-h-[110px]">
                    <!-- Left Group: Image & Info -->
                    <div class="flex items-center gap-3 min-w-0 flex-grow">
                        <!-- Product Image Placeholder -->
                        <div class="w-20 h-20 bg-neutral-50 rounded-2xl flex items-center justify-center shrink-0 border border-neutral-100 shadow-inner overflow-hidden">
                            @if($item->menu->image)
                                <img src="{{ Str::startsWith($item->menu->image, ['http://', 'https://']) ? $item->menu->image : asset('storage/' . $item->menu->image) }}" alt="{{ $item->menu->name }}" class="w-full h-full object-cover">
                            @else
                                <i class="ri-image-line text-2xl text-neutral-300"></i>
                            @endif
                        </div>

                        <!-- Info details -->
                        <div class="min-w-0 flex flex-col justify-center">
                            <h4 class="font-extrabold text-neutral-800 text-xs sm:text-sm truncate">
                                {{ $item->menu->name }}
                            </h4>
                            <p class="text-[#133122] font-extrabold text-xs mt-1">
                                RP {{ number_format($item->price, 0, ',', '.') }}
                            </p>
                            <!-- Note Input -->
                            <div class="mt-2 flex items-center gap-1.5 bg-neutral-50 px-2 py-0.5 rounded-xl border border-neutral-100 focus-within:border-[#133122]/30 transition-all duration-200">
                                <span class="text-[8px] font-bold text-neutral-400 uppercase shrink-0">Note:</span>
                                <input 
                                    type="text" 
                                    id="note-input-{{ $item->id }}" 
                                    value="{{ $item->note }}" 
                                    placeholder="Tambah catatan..." 
                                    onblur="updateNote({{ $item->id }})"
                                    onkeydown="if(event.key === 'Enter') { this.blur(); }"
                                    class="w-full bg-transparent text-[10px] text-neutral-700 placeholder-neutral-400 font-semibold focus:outline-none"
                                >
                            </div>
                        </div>
                    </div>

                    <!-- Right Group: Actions (Delete & Qty) -->
                    <div class="flex flex-col items-end justify-between shrink-0 h-20 self-center z-10">
                        <!-- Delete Button -->
                        <button onclick="removeItem({{ $item->id }})" class="text-emerald-800 hover:text-emerald-950 transition-colors p-1 cursor-pointer">
                            <i class="ri-delete-bin-6-line text-lg"></i>
                        </button>

                        <!-- Quantity Adjuster -->
                        <div class="flex items-center gap-2 bg-neutral-50 rounded-full px-2 py-0.5 border border-neutral-100 select-none">
                            <button onclick="decrementQty({{ $item->id }})" class="text-neutral-400 hover:text-[#133122] transition-colors cursor-pointer">
                                <i class="ri-indeterminate-circle-line text-lg"></i>
                            </button>
                            <span id="qty-text-{{ $item->id }}" class="font-extrabold text-neutral-800 text-xs w-4 text-center">
                                {{ $item->quantity }}
                            </span>
                            <button onclick="incrementQty({{ $item->id }})" class="text-neutral-400 hover:text-[#133122] transition-colors cursor-pointer">
                                <i class="ri-add-circle-line text-lg"></i>
                            </button>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Promo Code Card -->
        <div class="bg-white rounded-2xl p-3.5 border border-neutral-200/50 shadow-sm mb-4">
            <div onclick="togglePromoInput()" class="flex items-center justify-between cursor-pointer select-none">
                <div class="flex items-center gap-3">
                    <div class="text-[#133122]">
                        <i class="ri-coupon-3-fill text-2xl"></i>
                    </div>
                    <div>
                        <h4 id="promo-display-title" class="font-extrabold text-neutral-800 text-xs">
                            {{ $promoCode ? "Promo Terpasang: $promoCode" : "Punya kode promo" }}
                        </h4>
                        <p id="promo-display-desc" class="text-[9px] text-neutral-400 mt-0.5">
                            {{ $promoCode ? "Diskon telah dikurangkan dari total" : "Gunakan kode untuk mendapatkan diskon" }}
                        </p>
                    </div>
                </div>
                <i class="ri-arrow-right-s-line text-lg text-neutral-400"></i>
            </div>
            
            <!-- Promo input collapse section -->
            <div id="promo-input-container" class="{{ $promoCode ? 'block' : 'hidden' }} mt-3 pt-3 border-t border-neutral-100">
                <div id="promo-applied-block" class="{{ $promoCode ? 'flex' : 'hidden' }} items-center justify-between bg-emerald-50 text-emerald-800 text-[10px] px-3 py-2 rounded-xl">
                    <span>Kode <strong id="applied-code-text">{{ $promoCode }}</strong> aktif</span>
                    <button onclick="removePromoCode()" class="text-red-500 hover:text-red-700 font-bold uppercase tracking-wider text-[9px] cursor-pointer">Hapus</button>
                </div>
                
                <div id="promo-input-block" class="{{ $promoCode ? 'hidden' : 'flex' }} gap-2">
                    <input 
                        type="text" 
                        id="promo-code-input" 
                        placeholder="Contoh: HEMAT10" 
                        class="flex-grow bg-neutral-50 text-neutral-800 text-xs px-3 py-2 rounded-xl border border-neutral-200 focus:outline-none focus:border-[#133122] uppercase font-bold"
                    >
                    <button onclick="applyPromoCode()" class="bg-[#133122] text-[#F4F3EB] text-xs font-bold px-4 py-2 rounded-xl hover:bg-[#1a4430] cursor-pointer">Gunakan</button>
                </div>
                <p id="promo-error-msg" class="text-red-500 text-[9px] mt-1 hidden"></p>
            </div>
        </div>

        <!-- Pricing Summary Card -->
        <div class="bg-white rounded-3xl p-4 shadow-sm border border-neutral-200/50 flex flex-col gap-2.5 text-xs text-neutral-600 mb-5">
            <div class="flex justify-between items-center">
                <span>Subtotal (<span id="summary-items-count">{{ $items->sum('quantity') }}</span> item) :</span>
                <span class="font-extrabold text-neutral-800">Rp. <span id="summary-subtotal">{{ number_format($subtotal, 0, ',', '.') }}</span></span>
            </div>
            <div class="flex justify-between items-center">
                <span>Diskon :</span>
                <span class="font-extrabold text-[#A93226]">Rp. <span id="summary-discount">{{ number_format($discount, 0, ',', '.') }}</span></span>
            </div>
            
            <!-- Dashed Divider -->
            <div class="border-t border-dashed border-neutral-200 my-1.5"></div>

            <div class="flex justify-between items-center text-sm font-extrabold text-neutral-800">
                <span>Total Pembayaran :</span>
                <span class="text-[#133122]">Rp. <span id="summary-total">{{ number_format($total, 0, ',', '.') }}</span></span>
            </div>
        </div>

        <!-- Checkout Button -->
        <a href="{{ route('customer.checkout') }}" class="bg-[#133122] hover:bg-[#1c4430] text-[#F4F3EB] rounded-2xl w-full py-3.5 px-5 flex items-center justify-between shadow-md font-bold text-xs tracking-wide transition-all duration-200 active:scale-98">
            <span class="uppercase tracking-wider">Chekout</span>
            <i class="ri-arrow-right-s-line text-lg"></i>
        </a>
    @endif
</div>

<script>
    // Toggle promo code collapse
    function togglePromoInput() {
        const container = document.getElementById('promo-input-container');
        if (container.classList.contains('hidden')) {
            container.classList.remove('hidden');
            container.classList.add('block');
        } else {
            // Keep it visible if a promo is applied
            const appliedBlock = document.getElementById('promo-applied-block');
            if (appliedBlock.classList.contains('hidden')) {
                container.classList.remove('block');
                container.classList.add('hidden');
            }
        }
    }

    // Apply Promo Code
    function applyPromoCode() {
        const codeInput = document.getElementById('promo-code-input');
        const code = codeInput.value.trim();
        const errorMsg = document.getElementById('promo-error-msg');
        
        if (!code) {
            errorMsg.textContent = 'Silakan masukkan kode promo.';
            errorMsg.classList.remove('hidden');
            return;
        }

        fetch('{{ route("customer.cart.apply-promo") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ code: code })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Update display titles
                document.getElementById('promo-display-title').textContent = 'Promo Terpasang: ' + data.promo_code;
                document.getElementById('promo-display-desc').textContent = 'Diskon telah dikurangkan dari total';
                
                // Show applied block, hide input block
                document.getElementById('applied-code-text').textContent = data.promo_code;
                document.getElementById('promo-applied-block').classList.remove('hidden');
                document.getElementById('promo-applied-block').classList.add('flex');
                document.getElementById('promo-input-block').classList.add('hidden');
                errorMsg.classList.add('hidden');
                
                // Recalculate totals
                document.getElementById('summary-discount').textContent = formatPrice(data.discount);
                document.getElementById('summary-total').textContent = formatPrice(data.total);
            } else {
                errorMsg.textContent = data.message;
                errorMsg.classList.remove('hidden');
            }
        })
        .catch(err => {
            console.error(err);
            errorMsg.textContent = 'Terjadi kesalahan saat memproses promo.';
            errorMsg.classList.remove('hidden');
        });
    }

    // Remove Promo Code
    function removePromoCode() {
        fetch('{{ route("customer.cart.remove-promo") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Reset text titles
                document.getElementById('promo-display-title').textContent = 'Punya kode promo';
                document.getElementById('promo-display-desc').textContent = 'Gunakan kode untuk mendapatkan diskon';
                
                // Show input block, hide applied block
                document.getElementById('promo-applied-block').classList.remove('flex');
                document.getElementById('promo-applied-block').classList.add('hidden');
                document.getElementById('promo-input-block').classList.remove('hidden');
                document.getElementById('promo-input-block').classList.add('flex');
                
                document.getElementById('promo-code-input').value = '';
                document.getElementById('promo-error-msg').classList.add('hidden');

                // Recalculate totals
                document.getElementById('summary-discount').textContent = '0';
                document.getElementById('summary-total').textContent = formatPrice(data.total);
            }
        })
        .catch(err => console.error(err));
    }

    // Decrement item quantity
    function decrementQty(itemId) {
        const qtyText = document.getElementById('qty-text-' + itemId);
        let qty = parseInt(qtyText.textContent);
        
        if (qty > 0) {
            qty--;
            updateQuantity(itemId, qty);
        }
    }

    // Increment item quantity
    function incrementQty(itemId) {
        const qtyText = document.getElementById('qty-text-' + itemId);
        let qty = parseInt(qtyText.textContent);
        qty++;
        updateQuantity(itemId, qty);
    }

    // AJAX call to update quantity
    function updateQuantity(itemId, qty) {
        fetch('{{ route("customer.cart.update") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                cart_item_id: itemId,
                quantity: qty
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Update badge count in topbar
                const badge = document.querySelector('.topbar-cart-badge');
                if (badge) {
                    if (data.cart_count > 0) {
                        badge.textContent = data.cart_count;
                        badge.style.display = 'flex';
                    } else {
                        badge.style.display = 'none';
                    }
                }

                // If deleted (qty == 0), remove item row
                if (data.deleted) {
                    const card = document.getElementById('cart-item-card-' + itemId);
                    if (card) {
                        card.remove();
                    }
                    // If cart count is 0, reload to show empty state
                    if (data.cart_count === 0) {
                        window.location.reload();
                        return;
                    }
                } else {
                    // Update item quantity display
                    document.getElementById('qty-text-' + itemId).textContent = qty;
                }

                // If promo code was automatically removed due to subtotal check
                if (data.promo_removed) {
                    // Reset promo card display
                    document.getElementById('promo-display-title').textContent = 'Punya kode promo';
                    document.getElementById('promo-display-desc').textContent = 'Gunakan kode untuk mendapatkan diskon';
                    document.getElementById('promo-applied-block').classList.add('hidden');
                    document.getElementById('promo-input-block').classList.remove('hidden');
                    document.getElementById('promo-input-block').classList.add('flex');
                    document.getElementById('promo-code-input').value = '';
                }

                // Update summary block
                document.getElementById('summary-items-count').textContent = data.cart_count;
                document.getElementById('summary-subtotal').textContent = formatPrice(data.subtotal);
                document.getElementById('summary-discount').textContent = formatPrice(data.discount);
                document.getElementById('summary-total').textContent = formatPrice(data.total);
            }
        })
        .catch(err => console.error(err));
    }

    let itemToDeleteId = null;

    function removeItem(itemId) {
        itemToDeleteId = itemId;
        document.getElementById('delete-confirm-modal').classList.remove('hidden');
    }

    function closeDeleteModal() {
        document.getElementById('delete-confirm-modal').classList.add('hidden');
        itemToDeleteId = null;
    }

    function confirmDelete() {
        if (!itemToDeleteId) return;

        fetch('{{ route("customer.cart.remove") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                cart_item_id: itemToDeleteId
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Update badge count
                const badge = document.querySelector('.topbar-cart-badge');
                if (badge) {
                    if (data.cart_count > 0) {
                        badge.textContent = data.cart_count;
                        badge.style.display = 'flex';
                    } else {
                        badge.style.display = 'none';
                    }
                }

                // Remove card element
                const card = document.getElementById('cart-item-card-' + itemToDeleteId);
                if (card) {
                    card.remove();
                }

                // If cart count is 0, reload to show empty state
                if (data.cart_count === 0) {
                    window.location.reload();
                    return;
                }

                // If promo code was automatically removed due to subtotal check
                if (data.promo_removed) {
                    document.getElementById('promo-display-title').textContent = 'Punya kode promo';
                    document.getElementById('promo-display-desc').textContent = 'Gunakan kode untuk mendapatkan diskon';
                    document.getElementById('promo-applied-block').classList.add('hidden');
                    document.getElementById('promo-input-block').classList.remove('hidden');
                    document.getElementById('promo-input-block').classList.add('flex');
                    document.getElementById('promo-code-input').value = '';
                }

                // Update summary block
                document.getElementById('summary-items-count').textContent = data.cart_count;
                document.getElementById('summary-subtotal').textContent = formatPrice(data.subtotal);
                document.getElementById('summary-discount').textContent = formatPrice(data.discount);
                document.getElementById('summary-total').textContent = formatPrice(data.total);
            }
            closeDeleteModal();
        })
        .catch(err => {
            console.error(err);
            closeDeleteModal();
        });
    }

    // AJAX call to update note
    function updateNote(itemId) {
        const noteInput = document.getElementById('note-input-' + itemId);
        const note = noteInput.value;
        const qtyText = document.getElementById('qty-text-' + itemId);
        const qty = parseInt(qtyText.textContent);

        fetch('{{ route("customer.cart.update") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                cart_item_id: itemId,
                quantity: qty,
                note: note
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Flash success style to note input border
                const container = noteInput.closest('div');
                container.classList.remove('border-neutral-100');
                container.classList.add('border-emerald-500', 'bg-emerald-50/50');
                setTimeout(() => {
                    container.classList.remove('border-emerald-500', 'bg-emerald-50/50');
                    container.classList.add('border-neutral-100');
                }, 1000);
            }
        })
        .catch(err => console.error(err));
    }

    // Helper to format number as Rupiah
    function formatPrice(number) {
        return new Intl.NumberFormat('id-ID').format(number);
    }
</script>
@endsection
