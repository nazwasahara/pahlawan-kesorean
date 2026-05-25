@php
    $currentRoute = request()->route()->getName();
@endphp

<div class="fixed sm:absolute bottom-0 inset-x-0 z-50 bg-[#F4F3EB] border-t-2 border-[#133122] shadow-[0_-4px_12px_rgba(0,0,0,0.05)] rounded-t-3xl">
    <div class="flex items-center justify-around py-3 px-4">
        
        <!-- Home Link -->
        <a href="{{ route('customer.welcome') }}" class="flex flex-col items-center justify-center w-12 h-12 transition-all duration-200 {{ $currentRoute == 'customer.welcome' ? 'text-[#133122] scale-110 font-semibold' : 'text-neutral-500 hover:text-[#133122]' }}">
            <i class="{{ $currentRoute == 'customer.welcome' ? 'ri-home-5-fill' : 'ri-home-5-line' }} text-2xl"></i>
        </a>

        <!-- Menu Link -->
        <a href="{{ route('customer.menu') }}" class="flex flex-col items-center justify-center w-12 h-12 transition-all duration-200 {{ $currentRoute == 'customer.menu' ? 'text-[#133122] scale-110 font-semibold' : 'text-neutral-500 hover:text-[#133122]' }}">
            <i class="{{ $currentRoute == 'customer.menu' ? 'ri-restaurant-fill' : 'ri-restaurant-line' }} text-2xl"></i>
        </a>

        <!-- Cart Link -->
        <a href="{{ route('customer.cart') }}" class="flex flex-col items-center justify-center w-12 h-12 transition-all duration-200 {{ $currentRoute == 'customer.cart' ? 'text-[#133122] scale-110 font-semibold' : 'text-neutral-500 hover:text-[#133122]' }}">
            <i class="{{ $currentRoute == 'customer.cart' ? 'ri-shopping-bag-3-fill' : 'ri-shopping-bag-3-line' }} text-2xl"></i>
        </a>

        <!-- History Link -->
        <a href="{{ route('customer.history') }}" class="flex flex-col items-center justify-center w-12 h-12 transition-all duration-200 {{ $currentRoute == 'customer.history' ? 'text-[#133122] scale-110 font-semibold' : 'text-neutral-500 hover:text-[#133122]' }}">
            <i class="{{ $currentRoute == 'customer.history' ? 'ri-history-fill' : 'ri-history-line' }} text-2xl"></i>
        </a>

        <!-- Location Link -->
        <a href="{{ route('customer.location') }}" class="flex flex-col items-center justify-center w-12 h-12 transition-all duration-200 {{ $currentRoute == 'customer.location' ? 'text-[#133122] scale-110 font-semibold' : 'text-neutral-500 hover:text-[#133122]' }}">
            <i class="{{ $currentRoute == 'customer.location' ? 'ri-map-pin-fill' : 'ri-map-pin-line' }} text-2xl"></i>
        </a>

    </div>
</div>
