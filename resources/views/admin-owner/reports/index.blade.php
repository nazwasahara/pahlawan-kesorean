@extends('admin-owner.layouts.app')

@section('title', 'Pusat Laporan - Pahlawan Kesorean')

@section('navbar-title', 'PUSAT LAPORAN')

@section('admin-owner-content')
<div class="space-y-6">
    <!-- Header Section -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-6">
        <div>
            <h1 class="text-2xl font-extrabold text-gray-900 tracking-tight">Laporan Penjualan</h1>
            <p class="text-xs font-bold text-gray-500 mt-1">Kelola dan pantau seluruh laporan keuangan penjualan dalam sistem.</p>
        </div>
        <!-- Export Actions -->
        <div class="flex items-center gap-3">
            <!-- Excel Export -->
            <a href="{{ request()->fullUrlWithQuery(['export' => 'excel']) }}" class="bg-emerald-50 hover:bg-emerald-100 text-emerald-800 border border-emerald-200 text-xs font-bold py-3 px-5 rounded-2xl shadow-sm hover:shadow transition duration-200 cursor-pointer flex items-center gap-1.5">
                <i class="ri-file-excel-2-line text-sm"></i>
                <span>Ekspor Excel</span>
            </a>
            <!-- PDF Print -->
            <a href="{{ request()->fullUrlWithQuery(['export' => 'pdf']) }}" target="_blank" class="bg-[#125E34] hover:bg-[#1b5e3a] text-white text-xs font-bold py-3 px-5 rounded-2xl shadow-sm hover:shadow transition duration-200 cursor-pointer flex items-center gap-1.5">
                <i class="ri-printer-line text-sm"></i>
                <span>Cetak PDF</span>
            </a>
        </div>
    </div>

    <!-- Filter Form Section -->
    <div class="bg-white border border-gray-200/80 rounded-[2rem] p-6 shadow-sm mb-6">
        <form action="{{ route('admin-owner.reports.index') }}" method="GET" id="report-filter-form" class="space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
                <!-- Tipe Laporan -->
                <div>
                    <label for="type-select" class="block text-[10px] font-extrabold text-gray-400 uppercase tracking-wider mb-2">Tipe Laporan</label>
                    <select name="type" id="type-select" onchange="toggleFilterInputs()" class="w-full bg-gray-50 border border-gray-200 rounded-2xl px-4 py-3 text-xs font-bold text-gray-800 focus:outline-none focus:border-[#125E34] transition">
                        <option value="harian" {{ $type === 'harian' ? 'selected' : '' }}>Harian</option>
                        <option value="mingguan" {{ $type === 'mingguan' ? 'selected' : '' }}>Mingguan</option>
                        <option value="bulanan" {{ $type === 'bulanan' ? 'selected' : '' }}>Bulanan</option>
                    </select>
                </div>

                <!-- Monthly / Yearly Filters Container -->
                <div id="monthly-filters" class="col-span-2 grid grid-cols-2 gap-4">
                    <!-- Bulan -->
                    <div>
                        <label for="month-select" class="block text-[10px] font-extrabold text-gray-400 uppercase tracking-wider mb-2">Bulan</label>
                        <select name="month" id="month-select" class="w-full bg-gray-50 border border-gray-200 rounded-2xl px-4 py-3 text-xs font-bold text-gray-800 focus:outline-none focus:border-[#125E34] transition">
                            @foreach(range(1, 12) as $m)
                                <option value="{{ $m }}" {{ $month == $m ? 'selected' : '' }}>
                                    {{ Carbon\Carbon::create(null, $m, 1)->translatedFormat('F') }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <!-- Tahun -->
                    <div>
                        <label for="year-select" class="block text-[10px] font-extrabold text-gray-400 uppercase tracking-wider mb-2">Tahun</label>
                        <select name="year" id="year-select" class="w-full bg-gray-50 border border-gray-200 rounded-2xl px-4 py-3 text-xs font-bold text-gray-800 focus:outline-none focus:border-[#125E34] transition">
                            @foreach($availableYears as $y)
                                <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>{{ $y }}</option>
                          @endforeach
                      </select>
                  </div>
              </div>

              <!-- Date Filter (for Daily/Weekly) -->
              <div id="date-filters" class="col-span-2 hidden">
                  <div>
                      <label for="date-input" class="block text-[10px] font-extrabold text-gray-400 uppercase tracking-wider mb-2">Pilih Tanggal</label>
                      <input type="date" name="date" id="date-input" value="{{ $dateStr }}" class="w-full bg-gray-50 border border-gray-200 rounded-2xl px-4 py-3 text-xs font-bold text-gray-800 focus:outline-none focus:border-[#125E34] transition">
                  </div>
              </div>

              <!-- Filter Action Button -->
              <div>
                  <button type="submit" class="w-full bg-[#125E34] hover:bg-[#1b5e3a] text-white text-xs font-bold py-3.5 px-6 rounded-2xl shadow-sm hover:shadow transition duration-200 cursor-pointer flex items-center justify-center gap-1.5">
                      <i class="ri-filter-3-line"></i>
                      <span>Terapkan</span>
                  </button>
              </div>
          </div>
      </form>
    </div>

    <!-- Active Period Indicator -->
    <div class="bg-gray-50 border border-gray-200/80 rounded-2xl p-4 flex items-center gap-3 mb-6">
        <div class="w-10 h-10 rounded-xl bg-[#125E34]/10 text-[#125E34] flex items-center justify-center text-lg">
            <i class="ri-calendar-event-line"></i>
        </div>
        <div>
            <h4 class="text-xs font-extrabold text-gray-800">Periode Laporan Aktif</h4>
            <p class="text-[11px] text-gray-500 font-semibold mt-0.5">{{ $periodLabel }} (Laporan {{ ucfirst($type) }})</p>
        </div>
    </div>

    <!-- Cards Grid -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        
        <!-- CARD 1: PEMASUKAN (REVENUE) -->
        <div class="bg-white border border-gray-200/80 rounded-[2.5rem] p-8 shadow-sm transition hover:shadow-md flex flex-col items-center justify-center text-center">
            <!-- Icon Circle -->
            <div class="w-16 h-16 rounded-full flex items-center justify-center mb-6" style="background-color: #125E34; color: #ffffff;">
                <i class="ri-arrow-up-line text-2xl"></i>
            </div>
            
            <!-- Title -->
            <span class="text-[10px] font-extrabold text-gray-400 tracking-widest uppercase">Pemasukan (Revenue)</span>
            
            <!-- Value -->
            <h2 class="text-3xl font-black tracking-tight mt-2 mb-6" style="color: #125E34;">
                Rp{{ number_format($revenue, 0, ',', '.') }}
            </h2>
            
            <!-- Stats -->
            <div class="grid grid-cols-2 w-full gap-4 text-center">
                <div>
                    <span class="block text-[10px] font-extrabold text-gray-400">Transaksi</span>
                    <span class="block text-sm font-black text-gray-900 mt-1">{{ $transactionsCount }}</span>
                </div>
                <div>
                    <span class="block text-[10px] font-extrabold text-gray-400">Rata-rata</span>
                    <span class="block text-sm font-black text-gray-900 mt-1">Rp{{ number_format($averageOrderValue, 0, ',', '.') }}</span>
                </div>
            </div>
            
            <!-- Divider -->
            <div class="w-full border-t border-gray-150 my-6"></div>
            
            <!-- Comparison -->
            <div class="flex flex-col items-center">
                <span class="text-[10px] font-extrabold text-gray-400 mb-2">{{ $comparisonLabel }}</span>
                @if($revenueChangePercent >= 0)
                    <div class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-black" style="background-color: #E8F5E9; color: #2E7D32;">
                        <span>↑ {{ abs(round($revenueChangePercent, 1)) }}%</span>
                    </div>
                @else
                    <div class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-black" style="background-color: #FFEBEE; color: #C62828;">
                        <span>↓ {{ abs(round($revenueChangePercent, 1)) }}%</span>
                    </div>
                @endif
            </div>
        </div>

        <!-- CARD 2: PENGELUARAN (EXPENSES) -->
        <div class="bg-white border border-gray-200/80 rounded-[2.5rem] p-8 shadow-sm transition hover:shadow-md flex flex-col items-center justify-center text-center">
            <!-- Icon Circle -->
            <div class="w-16 h-16 rounded-full flex items-center justify-center mb-6" style="background-color: #E53935; color: #ffffff;">
                <i class="ri-arrow-down-line text-2xl"></i>
            </div>
            
            <!-- Title -->
            <span class="text-[10px] font-extrabold text-gray-400 tracking-widest uppercase">Pengeluaran (Expenses)</span>
            
            <!-- Value -->
            <h2 class="text-3xl font-black tracking-tight mt-2 mb-6" style="color: #E53935;">
                Rp{{ number_format($expenses, 0, ',', '.') }}
            </h2>
            
            <!-- Stats -->
            <div class="grid grid-cols-2 w-full gap-4 text-center">
                <div>
                    <span class="block text-[10px] font-extrabold text-gray-400">Invoice</span>
                    <span class="block text-sm font-black text-gray-900 mt-1">{{ $invoiceCount }}</span>
                </div>
                <div>
                    <span class="block text-[10px] font-extrabold text-gray-400">Kategori</span>
                    <span class="block text-sm font-black text-gray-900 mt-1">{{ $expensesCategoriesCount }}</span>
                </div>
            </div>
            
            <!-- Divider -->
            <div class="w-full border-t border-gray-150 my-6"></div>
            
            <!-- Comparison -->
            <div class="flex flex-col items-center">
                <span class="text-[10px] font-extrabold text-gray-400 mb-2">{{ $comparisonLabel }}</span>
                @if($expensesChangePercent >= 0)
                    <div class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-black" style="background-color: #FFEBEE; color: #C62828;">
                        <span>↑ {{ abs(round($expensesChangePercent, 1)) }}%</span>
                    </div>
                @else
                    <div class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-black" style="background-color: #E8F5E9; color: #2E7D32;">
                        <span>↓ {{ abs(round($expensesChangePercent, 1)) }}%</span>
                    </div>
                @endif
            </div>
        </div>

        <!-- CARD 3: LABA BERSIH -->
        <div class="bg-white border border-gray-200/80 rounded-[2.5rem] p-8 shadow-sm transition hover:shadow-md flex flex-col items-center justify-center text-center">
            <!-- Icon Circle -->
            <div class="w-16 h-16 rounded-full flex items-center justify-center mb-6" style="background-color: #1E88E5; color: #ffffff;">
                <i class="ri-line-chart-line text-2xl"></i>
            </div>
            
            <!-- Title -->
            <span class="text-[10px] font-extrabold text-gray-400 tracking-widest uppercase">Laba Bersih</span>
            
            <!-- Value -->
            <h2 class="text-3xl font-black tracking-tight mt-2 mb-6" style="color: #125E34;">
                Rp{{ number_format($netProfit, 0, ',', '.') }}
            </h2>
            
            <!-- Stats -->
            <div class="w-full text-center">
                <span class="block text-[10px] font-extrabold text-gray-400">Profit Margin</span>
                <span class="block text-sm font-black mt-1" style="color: #2E7D32;">{{ number_format($profitMargin, 2, ',', '.') }}%</span>
            </div>
            
            <!-- Divider -->
            <div class="w-full border-t border-gray-150 my-6"></div>
            
            <!-- Comparison -->
            <div class="flex flex-col items-center">
                <span class="text-[10px] font-extrabold text-gray-400 mb-2">{{ $comparisonLabel }}</span>
                @if($netProfitChangePercent >= 0)
                    <div class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-black" style="background-color: #E8F5E9; color: #2E7D32;">
                        <span>↑ {{ abs(round($netProfitChangePercent, 1)) }}%</span>
                    </div>
                @else
                    <div class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-black" style="background-color: #FFEBEE; color: #C62828;">
                        <span>↓ {{ abs(round($netProfitChangePercent, 1)) }}%</span>
                    </div>
                @endif
            </div>
    </div>
</div>

    <!-- Details Tables Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mt-8">
        <!-- Pemasukan Table Card -->
        <div class="bg-white border border-gray-200/80 rounded-[2rem] p-6 shadow-sm flex flex-col">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-sm font-black text-gray-800 uppercase tracking-wider flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-emerald-600"></span>
                    Detail Pemasukan Harian
                </h3>
                <span class="text-[10px] font-bold text-gray-500">{{ $orders->count() }} Hari</span>
            </div>
            
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse text-xs">
                    <thead>
                        <tr class="bg-gray-50 border-b border-gray-150">
                            <th class="p-3 font-extrabold text-gray-500 uppercase tracking-wider">Tanggal</th>
                            <th class="p-3 font-extrabold text-gray-500 uppercase tracking-wider">Jumlah Transaksi</th>
                            <th class="p-3 font-extrabold text-gray-500 uppercase tracking-wider text-right">Total Pemasukan</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 font-semibold text-gray-700">
                        @forelse($orders as $dayRevenue)
                            <tr class="hover:bg-gray-50/50 transition">
                                <td class="p-3 font-extrabold text-gray-900">
                                    {{ \Carbon\Carbon::parse($dayRevenue->date)->translatedFormat('d M Y') }}
                                </td>
                                <td class="p-3 text-gray-500 font-bold">
                                    {{ $dayRevenue->count }} Transaksi
                                </td>
                                <td class="p-3 text-right text-[#125E34] font-extrabold">Rp{{ number_format($dayRevenue->total, 0, ',', '.') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="p-8 text-center text-gray-400 font-bold">Tidak ada data pemasukan pada periode ini.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Pengeluaran Table Card -->
        <div class="bg-white border border-gray-200/80 rounded-[2rem] p-6 shadow-sm flex flex-col">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-sm font-black text-gray-800 uppercase tracking-wider flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full" style="background-color: #E53935;"></span>
                    Detail Pengeluaran
                </h3>
                <span class="text-[10px] font-bold text-gray-500">{{ $expenseList->count() }} Data</span>
            </div>
            
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse text-xs">
                    <thead>
                        <tr class="bg-gray-50 border-b border-gray-150">
                            <th class="p-3 font-extrabold text-gray-500 uppercase tracking-wider">Tanggal</th>
                            <th class="p-3 font-extrabold text-gray-500 uppercase tracking-wider">Keterangan</th>
                            <th class="p-3 font-extrabold text-gray-500 uppercase tracking-wider text-right">Jumlah</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 font-semibold text-gray-700">
                        @forelse($expenseList as $expense)
                            <tr class="hover:bg-gray-50/50 transition">
                                <td class="p-3 text-gray-500">{{ $expense->date->format('d M Y') }}</td>
                                <td class="p-3 text-gray-800">{{ $expense->description }}</td>
                                <td class="p-3 text-right text-[#E53935] font-extrabold">Rp{{ number_format($expense->amount, 0, ',', '.') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="p-8 text-center text-gray-400 font-bold">Tidak ada data pengeluaran pada periode ini.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Total Summary Section -->
    <div class="max-w-md ml-auto mt-8 space-y-4 bg-white p-6 rounded-3xl border border-gray-200/80 shadow-sm">
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

</div>

<script>
    function toggleFilterInputs() {
        const type = document.getElementById('type-select').value;
        const monthlyFilters = document.getElementById('monthly-filters');
        const dateFilters = document.getElementById('date-filters');
        
        if (type === 'bulanan') {
            monthlyFilters.classList.remove('hidden');
            monthlyFilters.classList.add('grid');
            dateFilters.classList.add('hidden');
        } else {
            monthlyFilters.classList.add('hidden');
            monthlyFilters.classList.remove('grid');
            dateFilters.classList.remove('hidden');
        }
    }

    // Call on load to initialize correct fields visibility
    document.addEventListener('DOMContentLoaded', function() {
        toggleFilterInputs();
    });
</script>
@endsection
