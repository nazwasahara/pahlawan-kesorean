<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Keuangan - Pahlawan Kesorean</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;750;800;900&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
        }
        @media print {
            .no-print {
                display: none !important;
            }
            body {
                background: white;
                color: black;
                padding: 0;
                margin: 0;
            }
            .print-card {
                border: 1px solid #e5e7eb !important;
                box-shadow: none !important;
                padding: 0 !important;
                border-width: 0 !important;
            }
        }
    </style>
</head>
<body class="bg-gray-50 min-h-screen p-8 antialiased">
    <!-- Top toolbar (only visible on screen) -->
    <div class="max-w-4xl mx-auto mb-8 flex justify-between items-center bg-white p-4 rounded-2xl border border-gray-200 shadow-sm no-print">
        <div class="flex items-center gap-2">
            <span class="w-2.5 h-2.5 rounded-full bg-emerald-600 animate-pulse"></span>
            <span class="text-xs font-bold text-gray-600">Dokumen Cetak Laporan</span>
        </div>
        <div class="flex items-center gap-2">
            <button onclick="window.close()" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-800 text-xs font-bold rounded-xl transition duration-200 cursor-pointer">
                Tutup Halaman
            </button>
            <button onclick="window.print()" class="px-5 py-2.5 bg-[#125E34] hover:bg-[#1c4e30] text-white text-xs font-bold rounded-xl shadow-md transition duration-200 cursor-pointer">
                Cetak / Simpan PDF
            </button>
        </div>
    </div>

    <!-- Printable Content Container -->
    <div class="max-w-4xl mx-auto bg-white p-10 rounded-[1rem] border border-gray-200 shadow-sm print-card">
        <!-- Logo & Header -->
        <div class="flex justify-between items-start border-b border-gray-200 pb-8 mb-8">
            <div>
                <h1 class="text-2xl font-black text-gray-900 tracking-tight">LAPORAN KEUANGAN</h1>
                <p class="text-xs font-bold text-[#125E34] uppercase tracking-wider mt-1">Pahlawan Kesorean Coffee & Kitchen</p>
                <div class="flex flex-col gap-1 mt-4 text-xs text-gray-500 font-semibold">
                    <div><span class="text-gray-400">Tipe Laporan:</span> <span class="text-gray-800 font-bold uppercase">{{ $type }}</span></div>
                    <div><span class="text-gray-400">Periode:</span> <span class="text-gray-800 font-bold">{{ $periodLabel }}</span></div>
                </div>
            </div>
            <div class="text-right">
                <h2 class="text-lg font-black text-gray-800">PAHLAWAN KESOREAN</h2>
                <p class="text-xs text-gray-400 font-semibold mt-1">Jl. Pahlawan No. 25, Medan</p>
                <p class="text-xs text-gray-400 font-semibold">sore food.co</p>
                <p class="text-[10px] text-gray-400 font-medium mt-4">Dicetak pada: {{ now()->translatedFormat('d F Y H:i') }}</p>
            </div>
        </div>

        <!-- Financial Summary Grid -->
        <div class="grid grid-cols-3 gap-6 mb-10">
            <!-- Pemasukan -->
            <div class="bg-emerald-50/50 border border-emerald-100 rounded-2xl p-6">
                <span class="text-[9px] font-extrabold text-emerald-600 tracking-wider uppercase block">Total Pemasukan</span>
                <span class="text-xl font-black text-emerald-800 block mt-2">Rp{{ number_format($revenue, 0, ',', '.') }}</span>
                <span class="text-[10px] font-bold text-emerald-700/80 block mt-1">{{ $transactionsCount }} Transaksi</span>
            </div>

            <!-- Pengeluaran -->
            <div class="bg-red-50/50 border border-red-100 rounded-2xl p-6">
                <span class="text-[9px] font-extrabold text-red-600 tracking-wider uppercase block">Total Pengeluaran</span>
                <span class="text-xl font-black text-red-800 block mt-2">Rp{{ number_format($expenses, 0, ',', '.') }}</span>
                <span class="text-[10px] font-bold text-red-700/80 block mt-1">{{ $invoiceCount }} Invoice</span>
            </div>

            <!-- Laba Bersih -->
            <div class="bg-blue-50/50 border border-blue-100 rounded-2xl p-6">
                <span class="text-[9px] font-extrabold text-blue-600 tracking-wider uppercase block">Laba Bersih</span>
                <span class="text-xl font-black text-blue-800 block mt-2">Rp{{ number_format($netProfit, 0, ',', '.') }}</span>
                <span class="text-[10px] font-bold text-blue-700/80 block mt-1">Margin: {{ number_format($profitMargin, 2, ',', '.') }}%</span>
            </div>
        </div>

        <!-- Detailed Metrics Table -->
        <div class="border border-gray-200 rounded-2xl overflow-hidden mb-10">
            <table class="w-full text-left border-collapse text-xs">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-200">
                        <th class="p-4 font-bold text-gray-600 uppercase tracking-wider">Parameter Keuangan</th>
                        <th class="p-4 font-bold text-gray-600 uppercase tracking-wider text-right">Detail / Nilai</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-150 font-semibold text-gray-700">
                    <tr>
                        <td class="p-4">Total Pendapatan Kotor (Revenue)</td>
                        <td class="p-4 text-right text-gray-900 font-extrabold">Rp{{ number_format($revenue, 0, ',', '.') }}</td>
                    </tr>
                    <tr>
                        <td class="p-4">Rata-rata Nilai Pesanan (Average Ticket Size)</td>
                        <td class="p-4 text-right">Rp{{ number_format($averageOrderValue, 0, ',', '.') }}</td>
                    </tr>
                    <tr>
                        <td class="p-4">Total Biaya Pengeluaran Operasional (Expenses)</td>
                        <td class="p-4 text-right text-gray-900 font-extrabold">Rp{{ number_format($expenses, 0, ',', '.') }}</td>
                    </tr>
                    <tr>
                        <td class="p-4">Jumlah Transaksi Penjualan</td>
                        <td class="p-4 text-right">{{ $transactionsCount }} kali</td>
                    </tr>
                    <tr>
                        <td class="p-4">Jumlah Invoice Pengeluaran Terdaftar</td>
                        <td class="p-4 text-right">{{ $invoiceCount }} invoice</td>
                    </tr>
                    <tr>
                        <td class="p-4">Jumlah Kategori Pengeluaran Unik</td>
                        <td class="p-4 text-right">{{ $expensesCategoriesCount }} kategori</td>
                    </tr>
                    <tr class="bg-gray-50/50 font-extrabold text-gray-900 border-t-2 border-gray-200">
                        <td class="p-4 text-sm text-[#125E34]">Laba Bersih (Net Profit)</td>
                        <td class="p-4 text-right text-sm text-[#125E34]">Rp{{ number_format($netProfit, 0, ',', '.') }}</td>
                    </tr>
                    <tr class="bg-gray-50/50 font-extrabold text-gray-900">
                        <td class="p-4 text-sm text-blue-700">Profit Margin Persentase</td>
                        <td class="p-4 text-right text-sm text-blue-700">{{ number_format($profitMargin, 2, ',', '.') }}%</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Details Tables -->
        <div class="space-y-8 mt-10">
            <!-- Pemasukan Table -->
            <div class="border border-gray-200 rounded-2xl overflow-hidden">
                <div class="bg-gray-50 border-b border-gray-200 px-6 py-4">
                    <h3 class="text-xs font-black text-gray-800 uppercase tracking-wider">Detail Pemasukan Harian</h3>
                </div>
                <table class="w-full text-left border-collapse text-xs">
                    <thead>
                        <tr class="bg-gray-50/50 border-b border-gray-150 text-[10px] text-gray-400 font-extrabold uppercase">
                            <th class="p-3 pl-6">Tanggal</th>
                            <th class="p-3">Jumlah Transaksi</th>
                            <th class="p-3 pr-6 text-right">Total Pemasukan</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-150 font-semibold text-gray-700">
                        @forelse($orders as $dayRevenue)
                            <tr>
                                <td class="p-3 pl-6 font-bold text-gray-900">
                                    {{ \Carbon\Carbon::parse($dayRevenue->date)->translatedFormat('d M Y') }}
                                </td>
                                <td class="p-3 text-gray-500 font-bold">
                                    {{ $dayRevenue->count }} Transaksi
                                </td>
                                <td class="p-3 pr-6 text-right text-emerald-800 font-bold">Rp{{ number_format($dayRevenue->total, 0, ',', '.') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="p-6 text-center text-gray-400">Tidak ada data pemasukan pada periode ini.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pengeluaran Table -->
            <div class="border border-gray-200 rounded-2xl overflow-hidden">
                <div class="bg-gray-50 border-b border-gray-200 px-6 py-4">
                    <h3 class="text-xs font-black text-gray-800 uppercase tracking-wider">Detail Pengeluaran</h3>
                </div>
                <table class="w-full text-left border-collapse text-xs">
                    <thead>
                        <tr class="bg-gray-50/50 border-b border-gray-150 text-[10px] text-gray-400 font-extrabold uppercase">
                            <th class="p-3 pl-6">Tanggal</th>
                            <th class="p-3">Keterangan</th>
                            <th class="p-3 pr-6 text-right">Jumlah</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-150 font-semibold text-gray-700">
                        @forelse($expenseList as $expense)
                            <tr>
                                <td class="p-3 pl-6 text-gray-500">{{ $expense->date->format('d M Y') }}</td>
                                <td class="p-3 text-gray-800">{{ $expense->description }}</td>
                                <td class="p-3 pr-6 text-right text-red-800 font-bold">Rp{{ number_format($expense->amount, 0, ',', '.') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="p-6 text-center text-gray-400">Tidak ada data pengeluaran pada periode ini.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Total Summary Section -->
        <div class="max-w-md ml-auto mt-10 space-y-4">
            <div class="flex items-center justify-between text-sm font-bold text-gray-800">
                <span>Total Pemasukan:</span>
                <span class="text-[#125E34] text-base font-extrabold">Rp{{ number_format($revenue, 0, ',', '.') }}</span>
            </div>
            
            <div class="flex items-center justify-between text-sm font-bold text-gray-800">
                <span>Total Pengeluaran:</span>
                <span class="text-[#E53935] text-base font-extrabold">Rp{{ number_format($expenses, 0, ',', '.') }}</span>
            </div>
            
            <div class="bg-gray-50 border border-gray-200 rounded-2xl p-4 flex flex-col">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-black text-gray-900 uppercase tracking-wider">LABA BERSIH:</span>
                    <span class="text-xl font-black text-[#125E34]">Rp{{ number_format($netProfit, 0, ',', '.') }}</span>
                </div>
                <div class="text-right mt-1">
                    <span class="text-[10px] font-bold text-gray-400">Profit Margin: {{ number_format($profitMargin, 2, '.', ',') }}%</span>
                </div>
            </div>
        </div>

        <!-- Footer Signature / Note -->
        <div class="flex justify-between items-center mt-16 pt-8 border-t border-gray-150 text-[11px] text-gray-400 font-bold">
            <div>
                <p>Catatan: Laporan keuangan ini dihasilkan secara otomatis oleh sistem POS Pahlawan Kesorean.</p>
            </div>
        </div>
    </div>

    <!-- Auto-trigger print dialog when loading from PDF link -->
    <script>
        window.onload = function() {
            const urlParams = new URLSearchParams(window.location.search);
            if (urlParams.get('export') === 'pdf' || window.location.pathname.includes('print') || true) {
                setTimeout(() => {
                    window.print();
                }, 500);
            }
        }
    </script>
</body>
</html>
