@extends('customer.layouts.app')

@section('title', 'Riwayat Pesanan - Pahlawan Kesorean')

@section('customer-content')
<div class="py-10">
    <!-- Header -->
    <h2 class="text-xl font-extrabold text-[#133122] font-playfair italic text-center mb-6 tracking-wide">
        Riwayat Pesanan Anda
    </h2>

    <!-- Loading Spinner -->
    <div id="history-loading" class="flex flex-col items-center justify-center py-12">
        <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-[#133122] mb-3"></div>
        <p class="text-xs text-neutral-500 font-semibold">Memuat riwayat...</p>
    </div>

    <!-- Empty State -->
    <div id="history-empty" class="hidden text-center py-12 px-6">
        <div class="w-16 h-16 bg-[#e5ebe4] rounded-full flex items-center justify-center text-[#133122] mx-auto mb-4">
            <i class="ri-file-list-3-line text-2xl"></i>
        </div>
        <h4 class="font-extrabold text-[#133122] text-sm">Belum Ada Riwayat</h4>
        <p class="text-[10px] text-neutral-500 mt-2 font-semibold leading-relaxed">
            Anda belum pernah melakukan pemesanan online. Silakan pesan menu favorit Anda sekarang!
        </p>
        <a href="{{ route('customer.menu') }}" class="inline-block bg-[#133122] hover:bg-[#1c4430] text-white rounded-xl py-2 px-6 text-xs font-bold shadow-sm transition-all duration-200 mt-5">
            Pesan Sekarang
        </a>
    </div>

    <!-- Orders List -->
    <div id="history-list" class="hidden space-y-4 pb-4">
        <!-- Dynamically populated -->
    </div>
</div>

<script>
    window.addEventListener('DOMContentLoaded', () => {
        const loadingEl = document.getElementById('history-loading');
        const emptyEl = document.getElementById('history-empty');
        const listEl = document.getElementById('history-list');

        let orderNumbers = [];
        try {
            orderNumbers = JSON.parse(localStorage.getItem('customer_order_history') || '[]');
        } catch (e) {
            console.error('Error parsing order history from localStorage', e);
        }

        if (orderNumbers.length === 0) {
            loadingEl.classList.add('hidden');
            emptyEl.classList.remove('hidden');
            return;
        }

        // Fetch order details from database
        fetch('{{ route("customer.history.orders") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ order_numbers: orderNumbers })
        })
        .then(res => res.json())
        .then(orders => {
            loadingEl.classList.add('hidden');

            if (orders.length === 0) {
                emptyEl.classList.remove('hidden');
                return;
            }

            let listHtml = '';
            orders.forEach(order => {
                const date = new Date(order.created_at);
                const formattedDate = date.toLocaleString('id-ID', {
                    day: 'numeric',
                    month: 'short',
                    year: 'numeric',
                    hour: '2-digit',
                    minute: '2-digit'
                });

                // Status Badge styling
                let statusBadge = '';
                if (order.status === 'pending') {
                    statusBadge = '<span class="bg-amber-100 text-amber-850 border border-amber-200 px-2.5 py-0.5 rounded-full text-[9px] font-bold">Menunggu Pembayaran</span>';
                } else if (order.status === 'processing') {
                    statusBadge = '<span class="bg-blue-105 text-blue-800 border border-blue-200 px-2.5 py-0.5 rounded-full text-[9px] font-bold">Diproses</span>';
                } else if (order.status === 'paid') {
                    statusBadge = '<span class="bg-emerald-100 text-emerald-800 border border-emerald-200 px-2.5 py-0.5 rounded-full text-[9px] font-bold">Sudah Dibayar</span>';
                } else if (order.status === 'completed') {
                    statusBadge = '<span class="bg-[#e5ebe4] text-[#133122] border border-[#d6dfd4] px-2.5 py-0.5 rounded-full text-[9px] font-bold">Selesai</span>';
                } else if (order.status === 'cancelled') {
                    statusBadge = '<span class="bg-red-100 text-red-800 border border-red-200 px-2.5 py-0.5 rounded-full text-[9px] font-bold">Dibatalkan</span>';
                }

                // Compile items text
                const itemsList = order.items.map(item => `${item.quantity}x ${item.menu_name}`).join(', ');

                // Detail link
                const detailUrl = '{{ route("customer.checkout.success", ":order_number") }}'.replace(':order_number', order.order_number);

                listHtml += `
                    <div class="bg-white rounded-3xl p-4 border border-neutral-200/50 shadow-sm flex flex-col gap-3">
                        <div class="flex justify-between items-start">
                            <div>
                                <span class="text-[9px] font-bold text-neutral-400 uppercase tracking-wide">ID Order</span>
                                <h4 class="font-extrabold text-[#133122] text-sm">#${order.order_number}</h4>
                            </div>
                            ${statusBadge}
                        </div>

                        <div class="border-t border-neutral-100 pt-2.5">
                            <span class="text-[9px] font-bold text-neutral-400 uppercase tracking-wide">Menu Dipesan</span>
                            <p class="text-xs text-neutral-800 font-semibold truncate mt-0.5">${itemsList}</p>
                        </div>

                        <div class="flex justify-between items-center border-t border-neutral-100 pt-2.5 mt-0.5">
                            <div>
                                <span class="text-[9px] font-bold text-neutral-400 uppercase tracking-wide">Waktu & Total</span>
                                <p class="text-[10px] text-neutral-500 font-semibold">${formattedDate}</p>
                                <p class="text-xs font-bold text-[#133122] mt-0.5">Rp ${Number(order.total).toLocaleString('id-ID')}</p>
                            </div>
                            
                            <a href="${detailUrl}" class="bg-[#133122] hover:bg-[#1c4430] text-white rounded-xl py-1.5 px-4 text-[10px] font-bold shadow-sm transition-all duration-200 flex items-center gap-1">
                                <span>Lihat Detail</span>
                                <i class="ri-arrow-right-s-line"></i>
                            </a>
                        </div>
                    </div>
                `;
            });

            listEl.innerHTML = listHtml;
            listEl.classList.remove('hidden');
        })
        .catch(err => {
            console.error('Error fetching order history:', err);
            loadingEl.classList.add('hidden');
            emptyEl.classList.remove('hidden');
        });
    });
</script>
@endsection
