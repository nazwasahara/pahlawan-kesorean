@extends('customer.layouts.app')

@section('title', 'Selamat Datang - Pahlawan Kesorean')

@push('styles')
<style>
    @keyframes float {
        0% { transform: translateY(0px); }
        50% { transform: translateY(-8px); }
        100% { transform: translateY(0px); }
    }

    .animate-float {
        animation: float 6s ease-in-out infinite;
    }
</style>
@endpush

@section('customer-content')
<div class="flex flex-col justify-between min-h-[100dvh] sm:min-h-[830px] relative overflow-hidden bg-[#F4F3EB]">
    
    <!-- Halal Logo (Top Right) -->
    <div class="absolute top-4 right-4 z-30">
        <img src="{{ asset('images/halal-logo.png') }}" class="h-10 w-auto object-contain" alt="Halal Indonesia">
    </div>

    <!-- Main Content Container -->
    <div class="flex flex-col items-center text-center px-6 pt-6 pb-2 flex-grow">
        
        <!-- Main Logo & Slogan -->
        <div class="w-full max-w-[160px] flex flex-col items-center justify-center mt-2">
            <img src="{{ asset('images/logo.png') }}" class="h-40 w-auto object-contain" alt="Pahlawan Kesorean Logo">
        </div>
        
        <!-- Hero Typography -->
        <h1 class="text-base font-extrabold text-[#133122] tracking-wider uppercase leading-none mt-4 font-jakarta">
            Nikmati Hidangan Lezat
        </h1>
        <h2 class="text-3xl font-playfair italic font-bold text-[#133122] mt-0.5">
            Setiap Saat
        </h2>
        <p class="text-[11px] text-[#133122]/85 font-normal max-w-[280px] mt-1.5 leading-normal font-jakarta">
            Aneka makanan dan minuman favoritmu,<br>siap menemani harimu. ❤️
        </p>

        <!-- Promo Banner Widget -->
        @if($promos->count() > 0)
        <div class="w-full mt-4 px-2 relative z-20">
            <a href="{{ route('customer.promos') }}" class="block bg-gradient-to-r from-[#133122] to-[#1e4933] rounded-2xl p-4 text-left shadow-md hover:shadow-lg transition-all duration-300 transform hover:scale-[1.01] group relative overflow-hidden">
                <!-- Decorative background circles -->
                <div class="absolute -right-6 -bottom-6 w-24 h-24 bg-white/5 rounded-full"></div>
                <div class="absolute -right-2 -top-10 w-20 h-20 bg-white/5 rounded-full"></div>
                
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="bg-white/10 p-2.5 rounded-xl text-white">
                            <i class="ri-coupon-3-line text-xl"></i>
                        </div>
                        <div>
                            <span class="text-[9px] font-extrabold text-[#F4F3EB]/70 uppercase tracking-widest block">Diskon Spesial</span>
                            <h4 class="font-extrabold text-white text-xs mt-0.5">Ada {{ $promos->count() }} Promo Aktif Hari Ini! 🥳</h4>
                            <p class="text-[9px] text-[#F4F3EB]/80 mt-0.5">Gunakan voucher untuk hemat belanjaanmu.</p>
                        </div>
                    </div>
                    <div class="bg-white text-[#133122] w-7 h-7 rounded-full flex items-center justify-center shadow-md group-hover:translate-x-1 transition-transform duration-300">
                        <i class="ri-arrow-right-line text-sm font-bold"></i>
                    </div>
                </div>
            </a>
        </div>
        @endif

        <!-- Product Image (with higher z-index so it overlaps the wave) -->
        <div class="w-full flex items-center justify-center py-2 z-20 relative overflow-visible mt-2 pointer-events-none">
            <img src="{{ asset('images/landing-customer.png') }}" 
                 class="w-[82%] max-w-[260px] object-contain drop-shadow-[0_15px_20px_rgba(0,0,0,0.18)] animate-float pointer-events-auto" 
                 alt="Pahlawan Kesorean Menu Collage">
        </div>
    </div>

    <!-- Bottom Curved Wave & Footer Section (with negative margin so it overlaps behind the product) -->
    <div class="relative w-full z-10 shrink-0 -mt-32">
        <!-- SVG Wave -->
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1440 320" class="w-full h-16 block" preserveAspectRatio="none" style="margin-bottom: -1px;">
            <path fill="#133122" fill-opacity="1" d="M0,288L60,272C120,256,240,224,360,186.7C480,149,600,107,720,96C840,85,960,107,1080,138.7C1200,171,1320,213,1380,234.7L1440,256L1440,320L1380,320C1320,320,1200,320,1080,320C960,320,840,320,720,320C600,320,480,320,360,320C240,320,120,320,60,320L0,320Z"></path>
        </svg>

        <!-- Bottom Banner Content -->
        <div class="bg-[#133122] px-6 pb-6 pt-3 flex flex-col items-center text-white">
            
            <!-- Action Button -->
            <div class="w-full flex justify-center mb-6 z-30">
                <a href="{{ route('customer.menu') }}" 
                   class="w-full max-w-[280px] bg-white text-neutral-900 font-extrabold text-sm py-3.5 px-6 rounded-full flex items-center justify-between shadow-lg hover:scale-[1.03] transition-all duration-300 group cursor-pointer">
                    <span class="tracking-wide pl-2">Mulai Pesan</span>
                    <i class="ri-arrow-right-line text-lg pr-2 transition-transform duration-300 group-hover:translate-x-1.5"></i>
                </a>
            </div>

            <!-- Footer Text & Social/Phone Links -->
            <div class="w-full max-w-md flex flex-row items-center justify-between text-[9px] text-white/70 border-t border-white/10 pt-3">
                <!-- Left: Slogan/Text -->
                <p class="max-w-[140px] leading-tight font-light text-left">
                    Nikmati pengalaman ngopi yang nyaman.
                </p>
                
                <!-- Right: Social & Contact -->
                <div class="flex flex-col gap-1 items-end">
                    <a href="https://instagram.com/kesorean.co" target="_blank" class="flex items-center gap-1 hover:text-white transition-colors">
                        <i class="ri-instagram-line text-[11px] text-white/80"></i>
                        <span>@kesorean.co</span>
                    </a>
                    <a href="tel:082165421066" class="flex items-center gap-1 hover:text-white transition-colors">
                        <i class="ri-phone-line text-[11px] text-white/80"></i>
                        <span>0821-6542-1066</span>
                    </a>
                </div>
            </div>

        </div>
    </div>

</div>
@endsection
