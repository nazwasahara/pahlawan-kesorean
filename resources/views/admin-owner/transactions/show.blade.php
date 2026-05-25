@extends('admin-owner.layouts.app')

@section('title', 'Detail Transaksi - Pahlawan Kesorean')

@section('navbar-title', 'MANAJEMEN TRANSAKSI')

@section('admin-owner-content')
<div class="space-y-6">
    <!-- Back Button & Status Badge Header -->
    <div class="flex justify-between items-center w-full">
        <!-- Back Button -->
        <a href="{{ route('admin-owner.transactions.index') }}" class="flex items-center gap-2 text-xs font-black text-gray-900 hover:text-[#125E34] transition duration-150">
            <i class="ri-arrow-left-line text-lg"></i>
            <span>Kembali ke Daftar Transaksi</span>
        </a>

        <!-- Status Badge -->
        <div>
            @if($order->status === 'pending')
                <span class="inline-block px-6 py-1.5 rounded-xl text-xs font-black tracking-wide shadow-sm" style="background-color: #FFFDE7; color: #856404; border: 1px solid #FFF59D;">
                    Menunggu Pembayaran
                </span>
            @elseif($order->status === 'paid')
                <span class="inline-block px-6 py-1.5 rounded-xl text-xs font-black tracking-wide shadow-sm" style="background-color: #E8F5E9; color: #1B5E20; border: 1px solid #C8E6C9;">
                    Sudah Dibayar
                </span>
            @elseif($order->status === 'processing')
                <span class="inline-block px-6 py-1.5 rounded-xl text-xs font-black tracking-wide shadow-sm" style="background-color: #E3F2FD; color: #0D47A1; border: 1px solid #BBDEFB;">
                    Diproses
                </span>
            @elseif($order->status === 'completed')
                <span class="inline-block px-6 py-1.5 rounded-xl text-xs font-black tracking-wide shadow-sm" style="background-color: #E3F2FD; color: #0D47A1; border: 1px solid #BBDEFB;">
                    Selesai
                </span>
            @elseif($order->status === 'cancelled')
                <span class="inline-block px-6 py-1.5 rounded-xl text-xs font-black tracking-wide shadow-sm" style="background-color: #FFEBEE; color: #C62828; border: 1px solid #FFCDD2;">
                    Dibatalkan
                </span>
            @endif
        </div>
    </div>

    <!-- Main Detail Panel -->
    <div class="flex flex-col lg:flex-row gap-6">
        <!-- Left: Detail Card -->
        <div class="flex-1 bg-white rounded-[1rem] border border-gray-200 shadow-sm p-6 md:p-8 flex flex-col justify-between">
            <div class="w-full">
                <!-- Upper Info Grid -->
                <div class="grid grid-cols-2 md:grid-cols-5 gap-6 pb-6 border-b border-gray-200">
                    <!-- No. Order -->
                    <div>
                        <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wider">No. Order</p>
                        <p class="text-sm md:text-base font-black text-gray-900 mt-1">#{{ $order->order_number }}</p>
                    </div>
                    <!-- Pelanggan -->
                    <div>
                        <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wider">Pelanggan</p>
                        <p class="text-sm md:text-base font-black text-gray-900 mt-1">{{ $order->customer_name }}</p>
                    </div>
                    <!-- Tipe -->
                    <div>
                        <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wider">Tipe</p>
                        <p class="text-sm md:text-base font-black text-gray-900 mt-1">
                            {{ $order->order_type === 'dine_in' ? 'Dine In' : 'Takeaway' }}
                        </p>
                    </div>
                    <!-- Meja -->
                    <div>
                        <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wider">Meja</p>
                        <p class="text-sm md:text-base font-black text-gray-900 mt-1">
                            {{ $order->table_number ?: '-' }}
                        </p>
                    </div>
                    <!-- Waktu -->
                    <div>
                        <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wider">Waktu</p>
                        <p class="text-sm md:text-base font-black text-gray-900 mt-1">
                            {{ $order->created_at->format('d M Y, H.i') }}
                        </p>
                    </div>
                </div>

                <!-- Products List Table -->
                <div class="w-full mt-6 overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="border-b border-gray-200">
                                <th class="pb-3 text-xs font-extrabold text-gray-950">Produk</th>
                                <th class="pb-3 text-xs font-extrabold text-gray-950 text-center w-24">Qty</th>
                                <th class="pb-3 text-xs font-extrabold text-gray-950 text-right w-36">Harga</th>
                                <th class="pb-3 text-xs font-extrabold text-gray-950 text-right w-36">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach($order->items as $item)
                                <tr>
                                    <td class="py-4 text-xs font-bold text-gray-800">
                                        <div class="flex items-center gap-3">
                                            <!-- Product Image -->
                                            <div class="w-10 h-10 bg-gray-50 rounded-lg flex items-center justify-center shrink-0 border border-gray-250/60 overflow-hidden shadow-inner">
                                                @if($item->menu && $item->menu->image)
                                                    <img src="{{ Str::startsWith($item->menu->image, ['http://', 'https://']) ? $item->menu->image : asset('storage/' . $item->menu->image) }}" alt="{{ $item->menu_name }}" class="w-full h-full object-cover">
                                                @else
                                                    <i class="ri-image-line text-lg text-gray-300"></i>
                                                @endif
                                            </div>
                                            <!-- Product Info -->
                                            <div>
                                                <div class="font-extrabold text-gray-900">{{ $item->menu_name }}</div>
                                                @if($item->note)
                                                    <div class="text-[10px] text-gray-450 italic font-normal mt-0.5">
                                                        Note: {{ $item->note }}
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                    <td class="py-4 text-xs font-bold text-gray-800 text-center">
                                        {{ $item->quantity }}
                                    </td>
                                    <td class="py-4 text-xs font-bold text-gray-800 text-right">
                                        Rp{{ number_format($item->price, 0, ',', '.') }}
                                    </td>
                                    <td class="py-4 text-xs font-bold text-gray-800 text-right">
                                        Rp{{ number_format($item->subtotal, 0, ',', '.') }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Summary & Totals -->
            <div class="border-t border-gray-200 pt-6 mt-6">
                <div class="flex flex-col gap-2.5 max-w-sm ml-auto">
                    <!-- Subtotal -->
                    <div class="flex justify-between items-center text-xs font-bold text-gray-800">
                        <span>Subtotal</span>
                        <span>Rp{{ number_format($order->subtotal, 0, ',', '.') }}</span>
                    </div>
                    <!-- Discount if any -->
                    @if($order->discount > 0)
                        <div class="flex justify-between items-center text-xs font-bold text-red-600">
                            <span>Diskon</span>
                            <span>-Rp{{ number_format($order->discount, 0, ',', '.') }}</span>
                        </div>
                    @endif
                    <!-- Total -->
                    <div class="flex justify-between items-center border-t border-gray-200 pt-3 text-base font-black text-gray-900">
                        <span>Total Akhir</span>
                        <span>Rp{{ number_format($order->total, 0, ',', '.') }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Side Panel: Payment Details & Shift Info -->
        <div class="w-full lg:w-80 space-y-6">
            <!-- Payment Info Box -->
            <div class="bg-white rounded-[1rem] border border-gray-200 p-6 shadow-sm">
                <h3 class="text-sm font-black text-gray-900 border-b border-gray-150 pb-3 mb-4">Informasi Pembayaran</h3>
                <div class="space-y-4 text-xs font-bold text-gray-800">
                    <div class="flex justify-between">
                        <span class="text-gray-400">Metode</span>
                        <span class="text-gray-900 uppercase">{{ $order->payment_method }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-400">Status Bayar</span>
                        <span class="text-gray-900">
                            @if($order->status === 'pending')
                                Belum Dibayar
                            @else
                                Lunas
                            @endif
                        </span>
                    </div>
                    @if($order->shift)
                        <div class="flex justify-between">
                            <span class="text-gray-400">Kasir Pembayar</span>
                            <span class="text-gray-900">{{ $order->shift->user->name ?? '-' }}</span>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Shift Info Box -->
            @if($order->shift)
                <div class="bg-white rounded-[1rem] border border-gray-200 p-6 shadow-sm">
                    <h3 class="text-sm font-black text-gray-900 border-b border-gray-150 pb-3 mb-4">Informasi Shift</h3>
                    <div class="space-y-4 text-xs font-bold text-gray-800">
                        <div class="flex justify-between">
                            <span class="text-gray-400">Tanggal Shift</span>
                            <span class="text-gray-900">{{ \Carbon\Carbon::parse($order->shift->tanggal)->format('d M Y') }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-400">Mulai Shift</span>
                            <span class="text-gray-900">{{ $order->shift->jam_mulai ? date('H.i', strtotime($order->shift->jam_mulai)) : '-' }}</span>
                        </div>
                        @if($order->shift->jam_selesai)
                            <div class="flex justify-between">
                                <span class="text-gray-400">Selesai Shift</span>
                                <span class="text-gray-900">{{ date('H.i', strtotime($order->shift->jam_selesai)) }}</span>
                            </div>
                        @endif
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
