@extends('admin-owner.layouts.app')

@section('title', 'Manajemen Transaksi - Pahlawan Kesorean')

@section('navbar-title', 'MANAJEMEN TRANSAKSI')

@section('admin-owner-content')
<div class="space-y-6">
    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-extrabold text-gray-900 tracking-tight">Data Transaksi</h1>
            <p class="text-xs font-bold text-gray-500 mt-1">Kelola dan pantau seluruh data transaksi dalam sistem.</p>
        </div>
    </div>

    <!-- Filters Section -->
    <div class="flex justify-end">
        <form action="{{ route('admin-owner.transactions.index') }}" method="GET" class="flex flex-wrap items-center gap-3 w-full max-w-2xl justify-end">
            <!-- Search Input -->
            <div class="relative w-64">
                <input type="text" 
                       name="search" 
                       value="{{ $search }}"
                       placeholder="Cari Pelanggan/Id Order" 
                       class="w-full bg-white border border-gray-200 rounded-xl py-2 px-5 pr-10 text-xs font-semibold text-gray-800 placeholder-gray-400 focus:outline-none focus:ring-1 focus:ring-[#125E34] focus:border-[#125E34]">
                @if($search)
                    <a href="{{ route('admin-owner.transactions.index') }}" class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-450 hover:text-gray-700 flex items-center justify-center">
                        <i class="ri-close-line text-sm"></i>
                    </a>
                @endif
            </div>
            
            <!-- Search Button -->
            <button type="submit" class="w-9 h-9 bg-[#125E34] hover:bg-[#0E4A28] text-white rounded-xl flex items-center justify-center shadow transition-colors shrink-0">
                <i class="ri-search-line text-base"></i>
            </button>

            <!-- Status Dropdown -->
            <div class="relative">
                <select name="status" 
                        onchange="this.form.submit()" 
                        class="bg-white border border-gray-200 rounded-xl py-2 pl-5 pr-10 text-xs font-bold text-gray-800 focus:outline-none focus:ring-1 focus:ring-[#125E34] focus:border-[#125E34] appearance-none cursor-pointer">
                    <option value="all" {{ $status === 'all' || !$status ? 'selected' : '' }}>Status Order</option>
                    <option value="pending" {{ $status === 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="paid" {{ $status === 'paid' ? 'selected' : '' }}>Paid</option>
                    <option value="processing" {{ $status === 'processing' ? 'selected' : '' }}>Diproses</option>
                    <option value="completed" {{ $status === 'completed' ? 'selected' : '' }}>Selesai</option>
                    <option value="cancelled" {{ $status === 'cancelled' ? 'selected' : '' }}>Dibatalkan</option>
                </select>
                <div class="absolute inset-y-0 right-3 flex items-center pr-1 pointer-events-none">
                    <i class="ri-arrow-down-s-line text-xs font-black"></i>
                </div>
            </div>

            <!-- Date Picker -->
            <div class="relative">
                <input type="date" 
                       name="date" 
                       value="{{ $date }}"
                       onchange="this.form.submit()"
                       class="bg-white border border-gray-200 rounded-xl py-2 px-5 text-xs font-bold text-gray-800 focus:outline-none focus:ring-1 focus:ring-[#125E34] focus:border-[#125E34] cursor-pointer">
            </div>
        </form>
    </div>

    <!-- Data Table Card Container -->
    <div class="bg-white border border-gray-200/80 rounded-xl p-6 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse border border-gray-200">
                <thead>
                    <tr class="bg-[#F2F2F2] border-b border-gray-200 text-xs font-extrabold text-gray-900 tracking-wider">
                        <th class="py-3 px-4 border border-gray-200 text-center w-36">ID Order</th>
                        <th class="py-3 px-4 border border-gray-200 text-center">Nama Pelanggan</th>
                        <th class="py-3 px-4 border border-gray-200 text-center w-32">Kasir</th>
                        <th class="py-3 px-4 border border-gray-200 text-center w-24">Waktu</th>
                        <th class="py-3 px-4 border border-gray-200 text-center w-28">Tipe</th>
                        <th class="py-3 px-4 border border-gray-200 text-center w-36">Total Harga</th>
                        <th class="py-3 px-4 border border-gray-200 text-center w-36">Status Order</th>
                        <th class="py-3 px-4 border border-gray-200 text-center w-20">Detail</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-150 text-xs font-bold text-gray-800 bg-white">
                    @forelse($orders as $order)
                        <tr class="hover:bg-gray-50/50 transition">
                            <!-- ID Order -->
                            <td class="py-3 px-4 border border-gray-200 text-center text-gray-900">
                                #{{ $order->order_number }}
                            </td>
                            
                            <!-- Nama Pelanggan -->
                            <td class="py-3 px-4 border border-gray-200 text-gray-900 font-bold">
                                {{ $order->customer_name }}
                            </td>

                            <!-- Kasir -->
                            <td class="py-3 px-4 border border-gray-200 text-center text-gray-800 font-semibold">
                                {{ $order->shift->user->name ?? '-' }}
                            </td>
                            
                            <!-- Waktu -->
                            <td class="py-3 px-4 border border-gray-200 text-center font-medium text-gray-800">
                                {{ $order->created_at->translatedFormat('H.i') }}
                            </td>
                            
                            <!-- Tipe -->
                            <td class="py-3 px-4 border border-gray-200 text-center text-gray-800">
                                {{ $order->order_type === 'dine_in' ? 'Dine In' : 'Takeaway' }}
                            </td>
                            
                            <!-- Total Harga -->
                            <td class="py-3 px-4 border border-gray-200 text-left pl-6 text-gray-900 font-bold">
                                Rp{{ number_format($order->total, 0, ',', '.') }}
                            </td>
                            
                            <!-- Status Order Badge -->
                            <td class="py-3 px-4 border border-gray-200 text-center">
                                @if($order->status === 'pending')
                                    <span class="inline-block px-4 py-1 rounded-full text-[10px] font-black tracking-wide shadow-sm" style="background-color: #FFFDE7; color: #856404; border: 1px solid #FFF59D;">
                                        Menunggu Pembayaran
                                    </span>
                                @elseif($order->status === 'paid')
                                    <span class="inline-block px-4 py-1 rounded-full text-[10px] font-black tracking-wide shadow-sm" style="background-color: #E8F5E9; color: #1B5E20; border: 1px solid #C8E6C9;">
                                        Sudah Dibayar
                                    </span>
                                @elseif($order->status === 'processing')
                                    <span class="inline-block px-4 py-1 rounded-full text-[10px] font-black tracking-wide shadow-sm" style="background-color: #E3F2FD; color: #0D47A1; border: 1px solid #BBDEFB;">
                                        Diproses
                                    </span>
                                @elseif($order->status === 'completed')
                                    <span class="inline-block px-4 py-1 rounded-full text-[10px] font-black tracking-wide shadow-sm" style="background-color: #E3F2FD; color: #0D47A1; border: 1px solid #BBDEFB;">
                                        Selesai
                                    </span>
                                @elseif($order->status === 'cancelled')
                                    <span class="inline-block px-4 py-1 rounded-full text-[10px] font-black tracking-wide shadow-sm" style="background-color: #FFEBEE; color: #C62828; border: 1px solid #FFCDD2;">
                                        Dibatalkan
                                    </span>
                                @endif
                            </td>
                            
                            <!-- Detail Action Button -->
                            <td class="py-3 px-4 border border-gray-200 text-center">
                                <a href="{{ route('admin-owner.transactions.show', $order->id) }}" 
                                   class="w-7 h-7 bg-[#125E34] hover:bg-[#0E4A28] text-white rounded-full flex items-center justify-center transition shadow-sm mx-auto"
                                   title="Lihat Detail Transaksi">
                                    <i class="ri-eye-line text-xs"></i>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="py-12 text-center text-gray-400 font-bold border border-gray-200">
                                Tidak ada data transaksi ditemukan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination Links -->
        @if($orders->hasPages())
            <div class="px-6 py-4 bg-gray-50 border-t border-gray-100 mt-4">
                {{ $orders->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
