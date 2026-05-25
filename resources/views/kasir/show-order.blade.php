@extends('kasir.layouts.app')

@section('title', 'Detail Order - Pahlawan Kesorean')

@section('kasir-content')
<style>
    @media print {
        /* Set page size and margins for thermal receipt printing */
        @page {
            size: 80mm auto;
            margin: 0;
        }

        /* Hide all document elements for print */
        body * {
            visibility: hidden;
        }

        /* Make only the printArea visible */
        #printArea, 
        #printArea * {
            visibility: visible;
        }

        /* Reset layouts & containers for thermal print */
        body, 
        html {
            background: white !important;
            color: #000 !important;
            width: 80mm !important;
            margin: 0 auto !important;
            padding: 0 !important;
        }

        #printArea {
            position: absolute;
            left: 0;
            top: 0;
            width: 80mm !important;
            max-width: 80mm !important;
            background: white !important;
            padding: 4mm !important;
            margin: 0 !important;
            border: none !important;
            box-shadow: none !important;
            font-size: 11px !important;
            font-family: 'Courier New', Courier, monospace !important;
            display: block !important;
        }

        /* Force table columns layout for narrow print */
        table {
            width: 100% !important;
            border-collapse: collapse !important;
            font-size: 10px !important;
        }

        table thead tr {
            border-top: 1px solid #000 !important;
            border-bottom: 1px solid #000 !important;
        }

        th {
            padding: 8px 0 !important;
            border: none !important;
        }

        td {
            padding: 8px 0 !important;
            border-bottom: 1px dashed #ccc !important;
        }

        /* Clean titles and grids for print */
        .grid {
            display: block !important;
        }
        
        .grid > div {
            margin-bottom: 6px !important;
            border-bottom: 1px dashed #ccc !important;
            padding-bottom: 4px !important;
            display: flex !important;
            justify-content: space-between !important;
        }

        .grid > div > p:first-child {
            font-weight: bold !important;
            color: #000 !important;
            text-transform: uppercase !important;
        }

        .grid > div > p:last-child {
            font-weight: bold !important;
            color: #000 !important;
            margin-top: 0 !important;
        }

        /* Summary borders for print */
        .border-t.border-gray-200.pt-6 {
            border-top: 1px solid #000 !important;
            padding-top: 12px !important;
            margin-top: 12px !important;
        }

        .border-t.border-gray-200.pt-3 {
            border-top: 1px solid #000 !important;
            padding-top: 8px !important;
        }
    }
</style>
<div class="h-full flex flex-col">
    <!-- Header Section (Back & Status) -->
    <div class="flex justify-between items-center w-full print:hidden">
        <!-- Back Button -->
        <a href="{{ route('kasir.orders') }}" class="flex items-center gap-2 text-sm font-bold text-gray-900 hover:text-green-800 transition duration-150">
            <i class="ri-arrow-left-line text-lg"></i> Kembali
        </a>

        <!-- Status Badge -->
        <div>
            @if($order->status === 'pending')
                <span class="inline-block bg-amber-100 text-amber-800 border border-amber-200 px-6 py-1.5 rounded-xl text-xs font-bold shadow-sm">
                    Pending
                </span>
            @elseif($order->status === 'processing')
                <span class="inline-block bg-blue-100 text-blue-800 border border-blue-200 px-6 py-1.5 rounded-xl text-xs font-bold shadow-sm">
                    Diproses
                </span>
            @elseif($order->status === 'paid')
                <span class="inline-block bg-emerald-100 text-emerald-800 border border-emerald-200 px-6 py-1.5 rounded-xl text-xs font-bold shadow-sm">
                    Sudah Bayar
                </span>
            @elseif($order->status === 'completed')
                <span class="inline-block bg-[#355b34] text-white px-6 py-1.5 rounded-xl text-xs font-bold shadow-sm">
                    Selesai
                </span>
            @elseif($order->status === 'cancelled')
                <span class="inline-block bg-red-100 text-red-800 border border-red-200 px-6 py-1.5 rounded-xl text-xs font-bold shadow-sm">
                    Dibatalkan
                </span>
            @endif
        </div>
    </div>

    <!-- Notifications -->
    @if(session('success'))
        <div class="mt-4 p-4 rounded-xl bg-green-50 border border-green-200 text-green-800 text-sm font-semibold flex items-center gap-2 print:hidden">
            <i class="ri-checkbox-circle-line text-lg text-green-600"></i>
            {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="mt-4 p-4 rounded-xl bg-red-50 border border-red-200 text-red-800 text-sm font-semibold flex items-center gap-2 print:hidden">
            <i class="ri-error-warning-line text-lg text-red-600"></i>
            {{ session('error') }}
        </div>
    @endif

    <div class="flex flex-col lg:flex-row gap-6 mt-4 flex-1">
        <!-- Left: Detail Card -->
        <div id="printArea" class="flex-1 bg-white rounded-2xl border border-gray-200 shadow-sm p-6 md:p-8 flex flex-col justify-between print:border-none">
            <div class="w-full">
                <!-- Upper Info Grid -->
                <div class="grid grid-cols-2 md:grid-cols-5 gap-6 pb-6 border-b border-gray-200">
                    <!-- No. Order -->
                    <div>
                        <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">No. Order</p>
                        <p class="text-base md:text-lg font-bold text-gray-900 mt-1">#{{ $order->order_number }}</p>
                    </div>
                    <!-- Pelanggan -->
                    <div>
                        <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Pelanggan</p>
                        <p class="text-sm md:text-base font-bold text-gray-900 mt-1">{{ $order->customer_name }}</p>
                    </div>
                    <!-- Tipe -->
                    <div>
                        <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Tipe</p>
                        <p class="text-sm md:text-base font-bold text-gray-900 mt-1">
                            {{ $order->order_type === 'dine_in' ? 'Dine In' : 'Take Away' }}
                        </p>
                    </div>
                    <!-- Meja -->
                    <div>
                        <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Meja</p>
                        <p class="text-sm md:text-base font-bold mt-1 {{ !$order->table_number && $order->order_type === 'dine_in' ? 'text-red-500 font-extrabold animate-pulse' : 'text-gray-900' }}">
                            @if($order->order_type === 'dine_in')
                                {{ $order->table_number ?? 'Belum Ada Meja' }}
                            @else
                                -
                            @endif
                        </p>
                    </div>
                    <!-- Waktu -->
                    <div>
                        <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Waktu</p>
                        <p class="text-sm md:text-base font-bold text-gray-900 mt-1">
                            {{ $order->created_at->translatedFormat('d M Y H.i') }}
                        </p>
                    </div>
                </div>

                <!-- Products List Table -->
                <div class="w-full mt-6">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="border-b border-gray-200">
                                <th class="pb-3 text-sm font-bold text-gray-900">Produk</th>
                                <th class="pb-3 text-sm font-bold text-gray-900 text-center w-24">Qty</th>
                                <th class="pb-3 text-sm font-bold text-gray-900 text-right w-36">Harga</th>
                                <th class="pb-3 text-sm font-bold text-gray-900 text-right w-36">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach($order->items as $item)
                                <tr>
                                    <td class="py-4 text-sm font-semibold text-gray-900">
                                        <div class="flex items-center gap-3">
                                            <!-- Product Image -->
                                            <div class="w-10 h-10 bg-gray-50 rounded-lg flex items-center justify-center shrink-0 border border-gray-250/60 overflow-hidden shadow-inner print:hidden">
                                                @if($item->menu && $item->menu->image)
                                                    <img src="{{ Str::startsWith($item->menu->image, ['http://', 'https://']) ? $item->menu->image : asset('storage/' . $item->menu->image) }}" alt="{{ $item->menu_name }}" class="w-full h-full object-cover">
                                                @else
                                                    <i class="ri-image-line text-lg text-gray-300"></i>
                                                @endif
                                            </div>
                                            <!-- Product Info -->
                                            <div>
                                                <div class="font-extrabold text-gray-950">{{ $item->menu_name }}</div>
                                                @if($item->note)
                                                    <div class="text-xs text-gray-450 italic font-normal mt-0.5">
                                                        Note: {{ $item->note }}
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                    <td class="py-4 text-sm font-semibold text-gray-900 text-center">
                                        {{ $item->quantity }}
                                    </td>
                                    <td class="py-4 text-sm font-semibold text-gray-900 text-right">
                                        Rp. {{ number_format($item->price, 0, ',', '.') }}
                                    </td>
                                    <td class="py-4 text-sm font-semibold text-gray-900 text-right">
                                        Rp. {{ number_format($item->subtotal, 0, ',', '.') }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Summary & Totals -->
            <div class="border-t border-gray-200 pt-6 mt-6">
                <div class="flex flex-col gap-3 max-w-md ml-auto">
                    <!-- Subtotal -->
                    <div class="flex justify-between items-center text-sm font-semibold text-gray-900">
                        <span>Subtotal</span>
                        <span>Rp. {{ number_format($order->subtotal, 0, ',', '.') }}</span>
                    </div>
                    <!-- Total -->
                    <div class="flex justify-between items-center border-t border-gray-200 pt-3 text-lg font-bold text-gray-900">
                        <span>Total</span>
                        <span class="text-xl">Rp. {{ number_format($order->total, 0, ',', '.') }}</span>
                    </div>
                    <!-- Metode Pembayaran -->
                    <div class="flex justify-between items-center text-sm font-semibold text-gray-900">
                        <span>Metode Pembayaran</span>
                        <span class="uppercase font-bold text-gray-900">{{ $order->payment_method }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right: Action / Control Sidebar -->
        <div class="w-full lg:w-80 shrink-0 flex flex-col gap-6 print:hidden">
            <!-- Kelola Pesanan Card -->
            <div class="bg-white rounded-2xl border border-gray-200 p-6 shadow-sm flex flex-col gap-5">
                <div class="flex items-center gap-2 border-b border-gray-100 pb-3">
                    <i class="ri-settings-4-line text-lg text-[#355b34]"></i>
                    <h3 class="text-sm font-bold text-gray-900">Kelola Pesanan</h3>
                </div>

                <form action="{{ route('kasir.orders.update', $order->id) }}" method="POST" class="flex flex-col gap-4">
                    @csrf

                    <!-- Update Status -->
                    <div class="flex flex-col gap-2">
                        <label for="status" class="text-xs font-bold text-gray-500 uppercase tracking-wider">Status Pesanan</label>
                        @php
                            $isCancelled = $order->status === 'cancelled';
                            $currentWeight = match($order->status) {
                                'pending' => 1,
                                'paid' => 2,
                                'processing' => 3,
                                'completed' => 4,
                                'cancelled' => 5,
                            };
                        @endphp
                        <select 
                            name="status" 
                            id="status" 
                            {{ $isCancelled ? 'disabled' : '' }}
                            class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm font-semibold {{ $isCancelled ? 'text-gray-400 bg-gray-50 cursor-not-allowed' : 'text-gray-900 bg-white cursor-pointer' }} focus:outline-none focus:ring-1 focus:ring-[#355b34] focus:border-[#355b34]"
                        >
                            <option value="pending" {{ $order->status === 'pending' ? 'selected' : '' }} {{ $currentWeight > 1 ? 'disabled' : '' }}>Pending</option>
                            <option value="paid" {{ $order->status === 'paid' ? 'selected' : '' }} {{ $currentWeight > 2 ? 'disabled' : '' }}>Sudah Bayar</option>
                            <option value="processing" {{ $order->status === 'processing' ? 'selected' : '' }} {{ $currentWeight > 3 ? 'disabled' : '' }}>Diproses</option>
                            <option value="completed" {{ $order->status === 'completed' ? 'selected' : '' }} {{ $currentWeight > 4 ? 'disabled' : '' }}>Selesai</option>
                            <option value="cancelled" {{ $order->status === 'cancelled' ? 'selected' : '' }}>Dibatalkan</option>
                        </select>
                    </div>

                    <!-- Input Nomor Meja -->
                    @if($order->order_type === 'dine_in')
                        <div class="flex flex-col gap-2">
                            <label for="table_number" class="text-xs font-bold text-gray-500 uppercase tracking-wider">
                                Nomor Meja
                                @if(!$order->table_number)
                                    <span class="text-[10px] text-red-500 font-extrabold uppercase ml-1">(Belum Ada)</span>
                                @endif
                            </label>
                            @php
                                $hasTable = !empty($order->table_number);
                                $isTableDisabled = $hasTable || $isCancelled;
                            @endphp
                            <select
                                name="table_number"
                                id="table_number"
                                {{ $isTableDisabled ? 'disabled' : '' }}
                                class="w-full border {{ !$order->table_number ? 'border-red-300 bg-red-50/10' : 'border-gray-200' }} rounded-xl px-3.5 py-2.5 text-sm font-semibold {{ $isTableDisabled ? 'text-gray-400 bg-gray-50 cursor-not-allowed' : 'text-gray-950 bg-white cursor-pointer' }} focus:outline-none focus:ring-1 focus:ring-[#355b34] focus:border-[#355b34] transition duration-150"
                            >
                                <option value="">Pilih Meja</option>
                                @for ($i = 1; $i <= 30; $i++)
                                    @php
                                        $isOccupied = in_array($i, $occupiedTables ?? []);
                                        $isSelected = (string)$order->table_number === (string)$i;
                                    @endphp
                                    <option value="{{ $i }}" {{ $isSelected ? 'selected' : '' }} {{ $isOccupied ? 'disabled' : '' }} class="{{ $isOccupied ? 'text-red-500 bg-red-50' : 'text-gray-900' }}">
                                        Meja {{ $i }} {{ $isOccupied ? '(Terisi)' : '' }}
                                    </option>
                                @endfor
                            </select>
                        </div>
                    @else
                        <div class="flex flex-col gap-2">
                            <label class="text-xs font-bold text-gray-500 uppercase tracking-wider">Nomor Meja</label>
                            <input 
                                type="text" 
                                readonly 
                                value="Take Away (Tidak Butuh Meja)" 
                                class="w-full border border-gray-200 rounded-xl px-3.5 py-2.5 text-sm font-semibold text-gray-400 bg-gray-50 cursor-not-allowed"
                            >
                        </div>
                    @endif

                    @if(!$isCancelled)
                        <button type="submit" class="w-full bg-[#355b34] hover:bg-[#2d4a2a] text-white font-bold py-3.5 px-4 rounded-xl text-sm transition duration-150 shadow-sm flex items-center justify-center gap-2 mt-2 cursor-pointer">
                            <i class="ri-save-line text-base"></i> Simpan Perubahan
                        </button>
                    @endif
                </form>
            </div>

            <!-- Print Card -->
            <button type="button" onclick="window.print()" class="w-full bg-white hover:bg-gray-50 border border-gray-200 text-gray-900 font-bold py-3.5 px-4 rounded-xl text-sm transition duration-150 shadow-sm flex items-center justify-center gap-2 cursor-pointer">
                <i class="ri-printer-line text-base"></i> Cetak Ulang Struk
            </button>
        </div>
    </div>
</div>
@endsection
