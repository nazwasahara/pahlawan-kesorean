@extends('customer.layouts.app')

@section('title', 'Lokasi Cafe - Pahlawan Kesorean')

@section('customer-content')
<div class="py-10">
    <!-- Search Bar (Static but redirects to menu on submit) -->
    <form action="{{ route('customer.menu') }}" method="GET" class="relative w-full mb-4">
        <input 
            type="text" 
            name="search"
            placeholder="Cari menu favoritmu..." 
            class="w-full bg-white text-neutral-800 placeholder-neutral-400 text-sm pl-5 pr-12 py-3 rounded-full border border-neutral-200 shadow-sm focus:outline-none focus:border-[#133122] focus:ring-1 focus:ring-[#133122]"
        >
        <button type="submit" class="absolute right-4 top-1/2 -translate-y-1/2 text-neutral-400">
            <i class="ri-search-line text-lg"></i>
        </button>
    </form>

    <!-- Title -->
    <h2 class="text-xl font-extrabold text-[#A93226] font-playfair italic text-center mb-4 tracking-wide">
        Temukan Lokasi Cafe Kami!
    </h2>

    <!-- Map Card Container -->
    <div class="bg-white rounded-3xl p-3 shadow-md border border-neutral-200/50 mb-5 flex flex-col">
        <div class="rounded-2xl overflow-hidden w-full h-[220px]">
            <iframe 
                src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3981.7200346615477!2d98.47345159999999!3d3.651162!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3030d500171a7019%3A0x18286a60d4c3c7b3!2sKESOREAN.CO%202%2C%20BINJAI%20LANGKAT!5e0!3m2!1sid!2sid!4v1779359989632!5m2!1sid!2sid" 
                width="100%"
                height="100%"
                style="border:0;"
                allowfullscreen="" 
                loading="lazy" 
                referrerpolicy="no-referrer-when-downgrade">
            </iframe>
        </div>
        
        <!-- Telusuri Button -->
        <div class="flex justify-end mt-3">
            <a 
                href="https://maps.google.com/?q=KESOREAN.CO+2,+BINJAI+LANGKAT" 
                target="_blank" 
                class="bg-[#133122] hover:bg-[#1c4430] text-white rounded-full py-1.5 px-5 flex items-center gap-1.5 text-[11px] font-extrabold shadow-sm transition-all duration-200 hover:scale-105 active:scale-95 cursor-pointer uppercase tracking-wider"
            >
                <span>Telusuri</span>
                <span class="font-normal text-[10px] tracking-tighter">&gt;&gt;</span>
            </a>
        </div>
    </div>

    <!-- Address & Details Card -->
    <div class="bg-white rounded-3xl p-5 shadow-sm border border-neutral-200/50 flex flex-col divide-y divide-neutral-100">
        
        <!-- Alamat -->
        <div class="flex items-start gap-4 pb-4">
            <div class="text-[#133122] mt-0.5 shrink-0">
                <i class="ri-map-pin-2-fill text-2xl"></i>
            </div>
            <div>
                <h4 class="font-extrabold text-[#133122] text-sm">Alamat</h4>
                <p class="text-[11px] text-neutral-800 mt-1 leading-relaxed">
                    Persimpangan 3, Jl. H. Agus Salim Jl. Cut Nyak Dhien, Tanah Tinggi, Kec. Binjai Tim., Kota Binjai, Sumatera Utara 20731
                </p>
            </div>
        </div>

        <!-- Jam Buka -->
        <div class="flex items-start gap-4 py-4">
            <div class="text-[#133122] mt-0.5 shrink-0">
                <i class="ri-time-fill text-2xl"></i>
            </div>
            <div>
                <h4 class="font-extrabold text-[#133122] text-sm">Jam Buka</h4>
                <p class="text-[11px] text-neutral-800 mt-1 leading-relaxed">
                    Buka Setiap Hari : 06.00 - 23.30
                </p>
            </div>
        </div>

        <!-- Telepon -->
        <div class="flex items-start gap-4 py-4">
            <div class="text-[#133122] mt-0.5 shrink-0">
                <i class="ri-phone-fill text-2xl"></i>
            </div>
            <div>
                <h4 class="font-extrabold text-[#133122] text-sm">Telepon</h4>
                <p class="text-[11px] text-neutral-800 mt-1 leading-relaxed">
                    0821-6542-1066
                </p>
            </div>
        </div>

        <!-- Free Wifi -->
        <div class="flex items-center gap-4 pt-4">
            <div class="text-[#133122] shrink-0">
                <i class="ri-wifi-line text-2xl"></i>
            </div>
            <div>
                <h4 class="font-extrabold text-[#133122] text-sm">Free Wifi</h4>
            </div>
        </div>

    </div>
</div>
@endsection
