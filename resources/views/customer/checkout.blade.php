@extends('customer.layouts.app')

@section('title', 'Ringkasan Pesanan - Pahlawan Kesorean')

@section('customer-content')
<div class="py-10">
    <!-- Header with Back and Close -->
    <div class="flex items-center justify-between mb-4 relative">
        <a href="{{ route('customer.cart') }}" class="text-neutral-800 hover:text-neutral-600 z-10">
            <i class="ri-arrow-left-line text-xl"></i>
        </a>
        <h2 class="text-lg font-bold text-neutral-800 absolute inset-x-0 text-center pointer-events-none">Ringkasan Pesanan</h2>
        <a href="{{ route('customer.menu') }}" class="text-neutral-800 hover:text-neutral-600 z-10">
            <i class="ri-close-line text-xl"></i>
        </a>
    </div>

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

    <form id="checkout-form" action="{{ route('customer.checkout.store') }}" method="POST" class="space-y-4" onsubmit="handleCheckoutSubmit(event)">
        @csrf
        
        <!-- Order Items White Card -->
        <div class="bg-white rounded-3xl p-4 shadow-sm border border-neutral-200/50 space-y-4">
            <!-- Count of items -->
            <div class="text-[#133122] text-xs font-bold">
                {{ $items->sum('quantity') }} Item
            </div>

            <!-- Items List -->
            <div class="space-y-3.5">
                @foreach($items as $item)
                    <div class="flex items-center justify-between gap-3">
                        <div class="flex items-center gap-3 min-w-0">
                            <!-- Image container -->
                            <div class="w-12 h-12 bg-neutral-50 rounded-xl flex items-center justify-center shrink-0 border border-neutral-100 shadow-inner overflow-hidden">
                                @if($item->menu->image)
                                    <img src="{{ Str::startsWith($item->menu->image, ['http://', 'https://']) ? $item->menu->image : asset('storage/' . $item->menu->image) }}" alt="{{ $item->menu->name }}" class="w-full h-full object-cover">
                                @else
                                    <i class="ri-image-line text-lg text-neutral-300"></i>
                                @endif
                            </div>
                            
                            <!-- Name and Quantity -->
                            <div class="min-w-0">
                                <h4 class="font-extrabold text-[#133122] text-xs truncate">
                                    {{ $item->menu->name }}
                                </h4>
                                <div class="flex items-center gap-2 mt-0.5">
                                    <span class="text-[10px] text-neutral-500 font-semibold">
                                        {{ $item->quantity }} x
                                    </span>
                                    @if($item->note)
                                        <span class="text-[9px] text-neutral-400 italic truncate max-w-[120px]">
                                            Note: {{ $item->note }}
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- Price -->
                        <span class="font-bold text-[#133122] text-xs shrink-0">
                            Rp {{ number_format($item->price, 0, ',', '.') }}
                        </span>
                    </div>
                @endforeach
            </div>

            <!-- Divider -->
            <div class="border-t border-neutral-100 pt-3 flex flex-col gap-2 text-xs text-neutral-600">
                <div class="flex justify-between items-center">
                    <span>Subtotal :</span>
                    <span class="font-bold text-neutral-800">Rp. {{ number_format($subtotal, 0, ',', '.') }}</span>
                </div>
                <div class="flex justify-between items-center">
                    <span>Diskon :</span>
                    <span class="font-bold text-neutral-800">Rp. {{ number_format($discount, 0, ',', '.') }}</span>
                </div>
                
                <div class="flex justify-between items-center text-xs font-bold text-[#133122] pt-1">
                    <span>Total Pembayaran :</span>
                    <span>Rp. {{ number_format($total, 0, ',', '.') }}</span>
                </div>
            </div>
        </div>

        <!-- Form Card: Informasi Pemesan -->
        <div class="bg-white rounded-3xl p-4 shadow-sm border border-neutral-200/50 space-y-4">
            <h3 class="font-bold text-[#133122] text-xs">Informasi Pemesan</h3>
            
            <!-- Nama Pemesan -->
            <div class="space-y-1">
                <label for="customer_name" class="text-[10px] font-bold text-neutral-500 uppercase">Nama Pemesan :</label>
                <input 
                    type="text" 
                    id="customer_name" 
                    name="customer_name" 
                    required 
                    placeholder="Masukkan nama pemesan" 
                    class="w-full bg-white text-neutral-800 text-sm px-4 py-3 rounded-2xl border border-[#133122]/30 focus:outline-none focus:border-[#133122] focus:ring-1 focus:ring-[#133122]/30"
                >
            </div>

            <!-- Dine in / Take Away Dropdown -->
            <div class="space-y-1">
                <label for="order_type" class="text-[10px] font-bold text-neutral-500 uppercase">Dine in / Take Away :</label>
                <div class="relative">
                    <select 
                        id="order_type" 
                        name="order_type" 
                        required
                        class="custom-select w-full bg-white text-neutral-800 text-sm px-4 py-3 pr-10 rounded-2xl border border-[#133122]/30 appearance-none focus:outline-none focus:border-[#133122] focus:ring-1 focus:ring-[#133122]/30"
                    >
                        <option value="dine_in">Dine in</option>
                        <option value="take_away">Take Away</option>
                    </select>
                    <div class="absolute inset-y-0 right-4 flex items-center pointer-events-none text-neutral-400">
                        <i class="ri-arrow-down-s-line text-xl"></i>
                    </div>
                </div>
            </div>

            <!-- Metode Pembayaran Dropdown -->
            <div class="space-y-1">
                <label for="payment_method" class="text-[10px] font-bold text-neutral-500 uppercase">Metode Pembayaran :</label>
                <div class="relative">
                    <select 
                        id="payment_method" 
                        name="payment_method" 
                        required
                        class="custom-select w-full bg-white text-neutral-800 text-sm px-4 py-3 pr-10 rounded-2xl border border-[#133122]/30 appearance-none focus:outline-none focus:border-[#133122] focus:ring-1 focus:ring-[#133122]/30"
                    >
                        <option value="qris">Qris</option>
                        <option value="cash">Cash</option>
                    </select>
                    <div class="absolute inset-y-0 right-4 flex items-center pointer-events-none text-neutral-400">
                        <i class="ri-arrow-down-s-line text-xl"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Submit Button -->
        <button type="submit" class="w-full bg-[#133122] hover:bg-[#1c4430] text-[#F4F3EB] font-bold text-xs py-3.5 px-4 rounded-xl shadow-md transition-all duration-200 active:scale-98 flex items-center justify-between uppercase tracking-wider cursor-pointer">
            <span>Konfirmasi Pesanan</span>
            <i class="ri-arrow-right-s-line text-base"></i>
        </button>
    </form>
</div>

<script>
    let formSubmitted = false;
    
    function handleCheckoutSubmit(event) {
        if (!formSubmitted) {
            event.preventDefault();
            // Show modal
            document.getElementById('check-order-modal').classList.remove('hidden');
        }
    }
    
    function closeCheckModal() {
        document.getElementById('check-order-modal').classList.add('hidden');
    }
    
    function submitCheckoutForm() {
        formSubmitted = true;
        document.getElementById('checkout-form').submit();
    }
</script>

@push('styles')
<style>
    /* Force-disable default select styling across browsers */
    select.custom-select {
        -webkit-appearance: none !important;
        -moz-appearance: none !important;
        appearance: none !important;
        background-image: none !important;
    }
    select.custom-select::-ms-expand {
        display: none !important;
    }
</style>
@endpush
@endsection
