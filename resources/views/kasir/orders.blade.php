@extends('kasir.layouts.app')

@section('title', 'Order - Pahlawan Kesorean')

@section('kasir-content')
<div class="h-full flex flex-col">
    <!-- Header/Filter Area -->
    <form method="GET" action="{{ route('kasir.orders') }}" id="filterForm" class="w-full flex flex-col gap-4">
        <!-- Search Input -->
        <div class="relative w-full">
            <span class="absolute inset-y-0 left-0 flex items-center pl-4 pointer-events-non">
                <i class="ri-search-line text-xl"></i>
            </span>
            <input type="text" name="search" id="orderSearchInput" placeholder="Cari Order/Nama..." value="{{ request('search') }}" class="w-full pl-12 pr-4 py-2.5 border border-gray-200 rounded-xl text-sm md:text-sm bg-white shadow-sm focus:outline-none focus:ring-2 focus:ring-[#355b34] transition duration-150">
        </div>

        <!-- Filter Row -->
        <div class="flex justify-between items-center gap-3 w-full">
            <!-- Hidden Status Input -->
            <input type="hidden" name="status" id="statusInput" value="{{ request('status', 'all') }}">

            <!-- Status Tabs (Pills) -->
            <div class="flex flex-wrap gap-2">
                @php
                    $currentStatus = request('status', 'all');
                @endphp
                <button type="button" onclick="filterStatus('all')" class="px-5 py-1.5 rounded-full text-xs font-semibold shadow-sm transition duration-150 {{ $currentStatus === 'all' ? 'bg-[#355b34] text-white' : 'bg-white border border-gray-200 text-gray-700 hover:bg-gray-50' }}">
                    Semua
                </button>
                <button type="button" onclick="filterStatus('menunggu')" class="px-5 py-1.5 rounded-full text-xs font-semibold shadow-sm transition duration-150 {{ $currentStatus === 'menunggu' ? 'bg-[#355b34] text-white' : 'bg-white border border-gray-200 text-gray-700 hover:bg-gray-50' }}">
                    Menunggu
                </button>
                <button type="button" onclick="filterStatus('diproses')" class="px-5 py-1.5 rounded-full text-xs font-semibold shadow-sm transition duration-150 {{ $currentStatus === 'diproses' ? 'bg-[#355b34] text-white' : 'bg-white border border-gray-200 text-gray-700 hover:bg-gray-50' }}">
                    Diproses
                </button>
                <button type="button" onclick="filterStatus('selesai')" class="px-5 py-1.5 rounded-full text-xs font-semibold shadow-sm transition duration-150 {{ $currentStatus === 'selesai' ? 'bg-[#355b34] text-white' : 'bg-white border border-gray-200 text-gray-700 hover:bg-gray-50' }}">
                    Selesai
                </button>
                <button type="button" onclick="filterStatus('dibatalkan')" class="px-5 py-1.5 rounded-full text-xs font-semibold shadow-sm transition duration-150 {{ $currentStatus === 'dibatalkan' ? 'bg-[#355b34] text-white' : 'bg-white border border-gray-200 text-gray-700 hover:bg-gray-50' }}">
                    Dibatalkan
                </button>
            </div>

            <!-- Time Filter Dropdown -->
            <div class="flex items-center gap-2 flex-shrink-0">
                <select name="time" onchange="document.getElementById('filterForm').submit()" class="bg-white border border-gray-200 text-gray-900 rounded-xl px-4 py-1.5 text-xs font-bold shadow-sm focus:outline-none focus:ring-1 focus:ring-[#355b34] focus:border-[#355b34] transition duration-150 cursor-pointer">
                    <option value="today" {{ $time === 'today' ? 'selected' : '' }}>Hari ini</option>
                    <option value="7_days" {{ $time === '7_days' ? 'selected' : '' }}>7 Hari Terakhir</option>
                    <option value="this_month" {{ $time === 'this_month' ? 'selected' : '' }}>Bulan ini</option>
                    <option value="all" {{ $time === 'all' ? 'selected' : '' }}>Semua</option>
                </select>
            </div>
        </div>
    </form>

    <!-- Orders Table Container -->
    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden flex-1 flex flex-col mt-4 min-h-0">
        <div class="overflow-x-auto flex-1">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="border-b border-gray-100 bg-gray-50/50">
                        <th class="px-6 py-4 text-sm font-bold text-gray-900">No. Order</th>
                        <th class="px-6 py-4 text-sm font-bold text-gray-900">Pelanggan</th>
                        <th class="px-6 py-4 text-sm font-bold text-gray-900">Tipe</th>
                        <th class="px-6 py-4 text-sm font-bold text-gray-900">Total</th>
                        <th class="px-6 py-4 text-sm font-bold text-gray-900 text-center">Status</th>
                        <th class="px-6 py-4 text-sm font-bold text-gray-900">Waktu</th>
                        <th class="px-6 py-4 text-sm font-bold text-gray-900 w-16 text-center"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($orders as $order)
                        <tr class="hover:bg-gray-50/50 transition duration-150">
                            <!-- No. Order -->
                            <td class="px-6 py-4 text-sm font-bold text-gray-900">
                                #{{ $order->order_number }}
                            </td>
                            
                            <!-- Pelanggan -->
                            <td class="px-6 py-4 text-sm text-gray-700 font-medium">
                                {{ $order->customer_name }}
                            </td>
                            
                            <!-- Tipe -->
                            <td class="px-6 py-4 text-sm text-gray-700 font-medium">
                                {{ $order->order_type === 'dine_in' ? 'Dine In' : 'Take Away' }}
                            </td>
                            
                            <!-- Total -->
                            <td class="px-6 py-4 text-sm font-bold text-gray-900">
                                Rp. {{ number_format($order->total, 0, ',', '.') }}
                            </td>
                            
                            <!-- Status Badges -->
                            <td class="px-6 py-4 text-center">
                                @if($order->status === 'pending')
                                    <span class="inline-block bg-amber-100 text-amber-800 border border-amber-200 px-3.5 py-1 rounded-full text-xs font-bold shadow-sm">
                                        Menunggu - {{ strtoupper($order->payment_method) }}
                                    </span>
                                @elseif($order->status === 'processing')
                                    <span class="inline-block bg-blue-100 text-blue-800 border border-blue-200 px-3.5 py-1 rounded-full text-xs font-bold shadow-sm">
                                        Diproses
                                    </span>
                                @elseif($order->status === 'paid')
                                    <span class="inline-block bg-emerald-100 text-emerald-800 border border-emerald-200 px-3.5 py-1 rounded-full text-xs font-bold shadow-sm">
                                        Sudah Bayar
                                    </span>
                                @elseif($order->status === 'completed')
                                    <span class="inline-block bg-[#355b34] text-white px-3.5 py-1 rounded-full text-xs font-bold shadow-sm">
                                        Selesai
                                    </span>
                                @elseif($order->status === 'cancelled')
                                    <span class="inline-block bg-red-100 text-red-800 border border-red-200 px-3.5 py-1 rounded-full text-xs font-bold shadow-sm">
                                        Dibatalkan
                                    </span>
                                @else
                                    <span class="inline-block bg-gray-100 text-gray-800 border border-gray-200 px-3.5 py-1 rounded-full text-xs font-bold shadow-sm">
                                        {{ ucfirst($order->status) }}
                                    </span>
                                @endif
                            </td>
                            
                            <!-- Waktu -->
                            <td class="px-6 py-4 text-sm text-gray-600 font-medium">
                                {{ $order->created_at->format('H.i') }}
                            </td>
                            
                            <!-- Action (Eye button) -->
                            <td class="px-6 py-4 text-center">
                                <a href="{{ route('kasir.orders.show', $order->id) }}" class="w-8 h-8 rounded-full bg-[#1e3f20] hover:bg-[#152e17] text-white flex items-center justify-center shadow transition duration-150">
                                    <i class="ri-eye-line text-sm"></i>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center text-gray-500">
                                <div class="flex flex-col items-center justify-center gap-2">
                                    <i class="ri-inbox-line text-4xl text-gray-400"></i>
                                    <p class="text-sm font-semibold">Tidak ada transaksi ditemukan.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <!-- Pagination Links -->
        <div class="px-6 py-4 bg-gray-50 border-t border-gray-100">
            {{ $orders->links() }}
        </div>
    </div>
</div>

<script>
    // Status Pills handler
    function filterStatus(status) {
        document.getElementById('statusInput').value = status;
        document.getElementById('filterForm').submit();
    }
</script>
@endsection
