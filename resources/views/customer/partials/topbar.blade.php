@php
    $cart = \App\Models\Cart::where('session_id', session()->getId())->first();
    $cartCount = $cart ? $cart->items()->sum('quantity') : 0;
@endphp

<div class="fixed sm:absolute top-0 inset-x-0 z-40 bg-[#F4F3EB]/95 backdrop-blur-md border-b border-neutral-200/50 py-3 px-4 shadow-sm">
    <div class="flex items-center justify-between">
        <!-- Logo -->
        <a href="{{ route('customer.welcome') }}" class="flex items-center">
            <img src="{{ asset('images/logo.png') }}" class="h-15 w-auto object-contain" alt="Pahlawan Kesorean Logo">
        </a>

        <!-- Cart Icon -->
        <a href="{{ route('customer.cart') }}" class="relative p-2 text-neutral-800 hover:text-emerald-950 transition-colors">
            <i class="ri-shopping-cart-2-line text-2xl"></i>
            <span class="topbar-cart-badge absolute top-0.5 right-0.5 bg-red-600 text-white text-[10px] font-extrabold w-4 h-4 rounded-full flex items-center justify-center" style="{{ $cartCount > 0 ? '' : 'display: none;' }}">
                {{ $cartCount }}
            </span>
        </a>
    </div>
</div>
