@extends('customer.layouts.app')

@section('title', 'Promo Spesial - Pahlawan Kesorean')

@section('customer-content')
<div class="py-10">
    <!-- Header with Back -->
    <div class="flex items-center justify-between mb-6">
        <a href="{{ route('customer.welcome') }}" class="text-neutral-800 hover:text-neutral-600">
            <i class="ri-arrow-left-line text-xl"></i>
        </a>
        <h2 class="text-lg font-extrabold text-neutral-800">Promo & Voucher</h2>
        <div class="w-6"></div>
    </div>

    @if($promos->count() === 0)
        <!-- Empty Promos Placeholder -->
        <div class="bg-white rounded-[2rem] p-8 shadow-sm border border-neutral-200/50 text-center flex flex-col items-center justify-center my-6">
            <div class="bg-[#133122]/10 p-6 rounded-full text-[#133122] mb-4">
                <i class="ri-coupon-3-line text-5xl"></i>
            </div>
            <h3 class="font-extrabold text-neutral-800 text-lg">Belum Ada Promo</h3>
            <p class="text-xs text-neutral-500 mt-2 max-w-[240px] leading-relaxed">
                Saat ini belum ada promo aktif. Pantau terus halaman ini untuk mendapatkan penawaran menarik berikutnya!
            </p>

            <a href="{{ route('customer.menu') }}" class="mt-6 px-6 py-3 bg-[#133122] text-[#F4F3EB] font-bold text-xs rounded-xl shadow-md hover:bg-[#1c4430] transition-all duration-200 uppercase tracking-wider">
                Lihat Menu Favorit
            </a>
        </div>
    @else
        <!-- Promos List -->
        <div class="space-y-4">
            @foreach($promos as $promo)
                <div class="flex bg-white rounded-2xl border border-dashed border-[#133122]/35 shadow-sm overflow-hidden relative transition-transform duration-200 hover:scale-[1.01]">
                    <!-- Left ticket notch & color strip -->
                    <div class="w-3 bg-[#133122] flex-shrink-0 relative">
                        <div class="absolute top-1/2 -translate-y-1/2 -left-1.5 w-3.5 h-3.5 bg-[#F4F3EB] rounded-full border border-neutral-200/50"></div>
                    </div>
                    
                    <!-- Content -->
                    <div class="p-4 flex-grow flex flex-col justify-between">
                        <div class="flex justify-between items-start">
                            <div>
                                <span class="text-xs font-bold text-neutral-400 uppercase tracking-wider">Voucher Diskon</span>
                                <h3 class="text-lg font-extrabold text-[#133122] mt-0.5 leading-tight">
                                    @if($promo->type === 'percentage')
                                        Diskon {{ intval($promo->value) }}%
                                    @else
                                        Potongan Rp {{ number_format($promo->value, 0, ',', '.') }}
                                    @endif
                                </h3>
                                @if($promo->max_discount !== null)
                                    <p class="text-[10px] text-[#A93226] font-bold mt-0.5">
                                        Maks. Potongan Rp {{ number_format($promo->max_discount, 0, ',', '.') }}
                                    </p>
                                @endif
                            </div>
                            
                            @if($promo->expired_at)
                                <div class="bg-red-50 text-red-700 text-[9px] font-bold px-2 py-0.5 rounded-full border border-red-100 flex items-center gap-1">
                                    <i class="ri-time-line"></i>
                                    <span>s/d {{ $promo->expired_at->format('d M Y') }}</span>
                                </div>
                            @endif
                        </div>

                        <!-- Requirements -->
                        <div class="mt-3 text-[11px] text-neutral-600 flex flex-col gap-1">
                            <div class="flex items-center gap-1.5 font-medium">
                                <i class="ri-shopping-bag-line text-[#133122] text-xs"></i>
                                <span>Min. Belanja: Rp {{ number_format($promo->minimum_transaction, 0, ',', '.') }}</span>
                            </div>
                            @if($promo->quota !== null)
                                <div class="flex items-center gap-1.5 font-medium">
                                    <i class="ri-user-line text-[#133122] text-xs"></i>
                                    <span>Kuota Tersisa: {{ max(0, $promo->quota - $promo->used_count) }}</span>
                                </div>
                            @endif
                        </div>

                        <!-- Coupon Action -->
                        <div class="flex items-center justify-between mt-4 pt-3 border-t border-neutral-100">
                            <div>
                                <span class="text-[10px] text-neutral-400 font-bold block uppercase">Kode Promo</span>
                                <span class="text-sm font-extrabold text-neutral-800 bg-neutral-100 rounded-lg px-2.5 py-1 border border-neutral-200 select-all tracking-wide">
                                    {{ $promo->code }}
                                </span>
                            </div>
                            
                            <button onclick="copyPromoCode('{{ $promo->code }}', this)" class="bg-[#133122] hover:bg-[#1a4430] text-[#F4F3EB] font-bold text-xs px-4 py-2.5 rounded-xl shadow-sm flex items-center gap-1.5 transition-all duration-200 hover:scale-[1.02] cursor-pointer">
                                <i class="ri-file-copy-2-line text-sm"></i>
                                <span>Salin Kode</span>
                            </button>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>

@push('scripts')
<script>
    function copyPromoCode(code, button) {
        navigator.clipboard.writeText(code).then(() => {
            const originalHTML = button.innerHTML;
            button.innerHTML = '<i class="ri-check-line text-emerald-600 text-sm"></i> <span class="text-emerald-600">Disalin!</span>';
            button.classList.remove('bg-[#133122]', 'hover:bg-[#1a4430]', 'text-[#F4F3EB]');
            button.classList.add('bg-emerald-50', 'text-emerald-800', 'border', 'border-emerald-200');
            button.disabled = true;
            setTimeout(() => {
                button.innerHTML = originalHTML;
                button.classList.remove('bg-emerald-50', 'text-emerald-800', 'border', 'border-emerald-200');
                button.classList.add('bg-[#133122]', 'hover:bg-[#1a4430]', 'text-[#F4F3EB]');
                button.disabled = false;
            }, 2000);
        }).catch(err => {
            console.error('Gagal menyalin: ', err);
        });
    }
</script>
@endpush
@endsection
