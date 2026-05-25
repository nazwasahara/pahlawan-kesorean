@extends('layouts.app')

@section('content')
<div class="h-[100dvh] sm:h-screen sm:min-h-screen bg-neutral-900 sm:bg-neutral-800 flex items-center justify-center overflow-hidden sm:overflow-y-auto sm:py-8 font-jakarta">
    <!-- Phone Mockup Container -->
    <div class="w-full sm:max-w-[412px] h-full sm:h-[846px] bg-[#F4F3EB] sm:rounded-[40px] sm:shadow-[0_24px_60px_rgba(0,0,0,0.6)] border-0 sm:border-[8px] sm:border-neutral-950 relative overflow-hidden flex flex-col justify-between">
        
        <!-- Top Bar Component -->
        @if(!request()->routeIs('customer.welcome'))
            @include('customer.partials.topbar')
        @endif

        <!-- Main Content Area -->
        <div class="flex-grow {{ request()->routeIs('customer.welcome') ? 'pt-0 pb-0 px-0' : 'pt-16 pb-20 px-4' }} overflow-y-auto custom-scroll relative">
            @yield('customer-content')
        </div>

        <!-- Bottom Nav Bar Component -->
        @if(!request()->routeIs('customer.welcome'))
            @include('customer.partials.navbar')
        @endif

        <!-- Global Modals Area (absolute inset-0 to cover topbar & navbar) -->
        
        <!-- Modal Periksa Kembali (Frame 434) -->
        <div id="check-order-modal" class="hidden absolute inset-0 z-50 bg-neutral-900/60 backdrop-blur-sm flex items-center justify-center p-5">
            <div class="bg-white rounded-[2rem] w-full max-w-[300px] p-6 shadow-2xl border border-neutral-100 flex flex-col items-center relative transition-transform duration-300">
                <!-- Close button x -->
                <button type="button" onclick="closeCheckModal()" class="absolute top-4 right-4 text-neutral-400 hover:text-neutral-600">
                    <i class="ri-close-line text-xl"></i>
                </button>

                <!-- Icon Checklist Document -->
                <div class="w-14 h-14 bg-[#e5ebe4] rounded-full flex items-center justify-center text-[#133122] mb-4">
                    <i class="ri-file-list-3-line text-2xl"></i>
                </div>

                <!-- Title -->
                <h3 class="text-neutral-800 font-extrabold text-sm text-center leading-tight">
                    Periksa Kembali<br>Pesanan Anda
                </h3>

                <!-- Description -->
                <p class="text-neutral-500 text-[10px] font-semibold text-center mt-3 leading-relaxed max-w-[200px]">
                    Pastikan menu, jumlah pesanan, dan detail lainnya sudah benar sebelum Checkout
                </p>

                <!-- Actions -->
                <div class="w-full space-y-2 mt-6">
                    <button type="button" onclick="closeCheckModal()" class="w-full py-2.5 rounded-xl border border-[#133122] text-[#133122] font-bold text-xs hover:bg-[#133122]/5 transition-colors cursor-pointer text-center">
                        Kembali
                    </button>
                    <button type="button" onclick="submitCheckoutForm()" class="w-full py-2.5 rounded-xl bg-[#133122] hover:bg-[#1c4430] text-[#F4F3EB] font-bold text-xs transition-colors cursor-pointer text-center shadow-md">
                        Lanjutkan Pembayaran
                    </button>
                </div>
            </div>
        </div>

        <!-- Modal Hapus Item (Frame 612) -->
        <div id="delete-confirm-modal" class="hidden absolute inset-0 z-50 bg-neutral-900/60 backdrop-blur-sm flex items-center justify-center p-5">
            <div class="bg-white rounded-[2rem] w-full max-w-[300px] p-6 shadow-2xl border border-neutral-100 flex flex-col items-center relative transition-transform duration-300">
                <!-- Close button x -->
                <button type="button" onclick="closeDeleteModal()" class="absolute top-4 right-4 text-neutral-400 hover:text-neutral-600">
                    <i class="ri-close-line text-xl"></i>
                </button>

                <!-- Icon Trash Can -->
                <div class="w-14 h-14 bg-[#e5ebe4] rounded-full flex items-center justify-center text-red-600 mb-4">
                    <i class="ri-delete-bin-6-line text-2xl"></i>
                </div>

                <!-- Title -->
                <h3 class="text-neutral-800 font-extrabold text-sm text-center leading-tight">
                    Apakah anda yakin<br>mengapus item ini?
                </h3>

                <!-- Actions -->
                <div class="w-full space-y-2 mt-6">
                    <button type="button" onclick="confirmDelete()" class="w-full py-2.5 rounded-xl border border-[#133122] text-[#133122] font-bold text-xs hover:bg-[#133122]/5 transition-colors cursor-pointer text-center">
                        Hapus
                    </button>
                    <button type="button" onclick="closeDeleteModal()" class="w-full py-2.5 rounded-xl bg-[#133122] hover:bg-[#1c4430] text-[#F4F3EB] font-bold text-xs transition-colors cursor-pointer text-center shadow-md">
                        Tidak
                    </button>
                </div>
            </div>
        </div>

        <!-- Modal Sukses Tambah (Frame 613) -->
        <div id="add-success-modal" class="hidden absolute inset-0 z-50 bg-neutral-900/60 backdrop-blur-sm flex items-center justify-center p-5">
            <div class="bg-white rounded-[2rem] w-full max-w-[300px] p-6 shadow-2xl border border-neutral-100 flex flex-col items-center relative transition-transform duration-300">
                <!-- Close button x -->
                <button type="button" onclick="closeSuccessModal()" class="absolute top-4 right-4 text-neutral-400 hover:text-neutral-600">
                    <i class="ri-close-line text-xl"></i>
                </button>

                <!-- Icon Restaurant -->
                <div class="w-14 h-14 bg-[#e5ebe4] rounded-full flex items-center justify-center text-[#133122] mb-4">
                    <i class="ri-restaurant-2-line text-2xl"></i>
                </div>

                <!-- Title -->
                <h3 class="text-neutral-800 font-extrabold text-sm text-center leading-tight">
                    Menu Berhasil di<br>Tambahkan ke keranjang
                </h3>

                <!-- Description -->
                <p class="text-neutral-500 text-[10px] font-semibold text-center mt-3 leading-relaxed max-w-[200px]">
                    Yeay! Menu sudah masuk ke keranjang.
                </p>

                <!-- Actions -->
                <div class="w-full space-y-2 mt-6">
                    <button type="button" onclick="closeSuccessModal()" class="w-full py-2.5 rounded-xl border border-[#133122] text-[#133122] font-bold text-xs hover:bg-[#133122]/5 transition-colors cursor-pointer text-center">
                        Kembali
                    </button>
                    <a href="{{ route('customer.cart') }}" class="block w-full py-2.5 rounded-xl bg-[#133122] hover:bg-[#1c4430] text-[#F4F3EB] font-bold text-xs transition-colors cursor-pointer text-center shadow-md">
                        Lihat Keranjang Saya
                    </a>
                </div>
            </div>
        </div>

    </div>
</div>
@endsection

@push('styles')
<style>
    @import url('https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@1,600;1,700&family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap');

    .font-jakarta {
        font-family: 'Plus Jakarta Sans', sans-serif;
    }
    
    .font-playfair {
        font-family: 'Playfair Display', serif;
    }

    .custom-scroll {
        -ms-overflow-style: none;  /* IE and Edge */
        scrollbar-width: none;  /* Firefox */
    }
    .custom-scroll::-webkit-scrollbar {
        display: none; /* Chrome, Safari and Opera */
    }
</style>
@endpush
