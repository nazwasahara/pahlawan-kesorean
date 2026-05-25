@extends('kasir.layouts.app')

@section('title', ($activeShift ? 'Tutup Shift' : 'Buka Shift') . ' - Pahlawan Kesorean')

@section('kasir-content')
<div class="h-full flex flex-col">
    <!-- Main content flex row layout -->
    <div class="flex flex-col lg:flex-row gap-6 items-start">
        
        <!-- Left Side: Form Area -->
        <div class="flex-1 w-full">
            @if(session('success'))
                <div class="mb-4 p-4 rounded-xl bg-green-50 border border-green-200 text-green-800 text-sm font-semibold flex items-center gap-2">
                    <i class="ri-checkbox-circle-line text-lg text-green-600"></i>
                    {{ session('success') }}
                </div>
            @endif
            @if(session('error'))
                <div class="mb-4 p-4 rounded-xl bg-red-50 border border-red-200 text-red-800 text-sm font-semibold flex items-center gap-2">
                    <i class="ri-error-warning-line text-lg text-red-600"></i>
                    {{ session('error') }}
                </div>
            @endif

            <h2 class="text-xl md:text-2xl font-bold text-gray-900 mb-4">
                {{ $activeShift ? 'Tutup Shift' : 'Buka Shift' }}
            </h2>

            @if(!$activeShift)
                <!-- Buka Shift Form -->
                <form method="POST" action="{{ route('kasir.shift.start') }}" class="bg-white rounded-2xl border border-gray-200 p-6 md:p-8 shadow-sm flex flex-col gap-5">
                    @csrf
                    <!-- Nama Kasir -->
                    <div class="flex flex-col gap-2">
                        <label class="text-xs font-bold text-[#355b34] uppercase tracking-wider">Nama Kasir</label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 pointer-events-none">
                                <i class="ri-user-line text-lg"></i>
                            </span>
                            <select disabled class="w-full pl-10 pr-4 py-3 border border-gray-200 rounded-xl text-sm bg-gray-50 text-gray-900 font-semibold appearance-none cursor-not-allowed">
                                <option>{{ auth()->user()->name }}</option>
                            </select>
                        </div>
                    </div>

                    <!-- Tanggal -->
                    <div class="flex flex-col gap-2">
                        <label class="text-xs font-bold text-[#355b34] uppercase tracking-wider">Tanggal</label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 pointer-events-none">
                                <i class="ri-calendar-line text-lg"></i>
                            </span>
                            <input type="text" readonly value="{{ now()->translatedFormat('d F Y') }}" class="w-full pl-10 pr-4 py-3 border border-gray-200 rounded-xl text-sm bg-gray-50 text-gray-900 font-semibold cursor-not-allowed">
                        </div>
                    </div>

                    <!-- Jam Mulai -->
                    <div class="flex flex-col gap-2">
                        <label class="text-xs font-bold text-[#355b34] uppercase tracking-wider">Jam Mulai</label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 pointer-events-none">
                                <i class="ri-time-line text-lg"></i>
                            </span>
                            <input type="text" readonly value="{{ now()->format('H:i') }}" class="w-full pl-10 pr-4 py-3 border border-gray-200 rounded-xl text-sm bg-gray-50 text-gray-900 font-semibold cursor-not-allowed">
                        </div>
                    </div>

                    <!-- Modal Awal Kas -->
                    <div class="flex flex-col gap-2">
                        <label class="text-xs font-bold text-[#355b34] uppercase tracking-wider">Modal Awal Kas</label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 pointer-events-none font-bold text-sm">
                                Rp
                            </span>
                            <input type="number" name="modal_awal" value="500000" min="0" step="1000" required class="w-full pl-10 pr-4 py-3 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-1 focus:ring-[#355b34] focus:border-[#355b34] font-semibold text-gray-950 transition duration-150">
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <button type="submit" class="bg-[#355b34] hover:bg-[#2d4a2a] text-white font-bold py-3.5 px-6 rounded-xl text-sm transition duration-150 shadow-sm flex items-center justify-center gap-2 mt-2">
                        <i class="ri-play-fill text-base"></i> Mulai Shift
                    </button>
                </form>
            @else
                <!-- Tutup Shift Form -->
                <form method="POST" action="{{ route('kasir.shift.end') }}" class="bg-white rounded-2xl border border-gray-200 p-6 md:p-8 shadow-sm flex flex-col gap-5">
                    @csrf
                    <!-- Detail Shift Table/Box -->
                    <div class="border border-gray-200 rounded-xl divide-y divide-gray-100 overflow-hidden bg-gray-50/20">
                        <!-- Nama Kasir -->
                        <div class="flex justify-between items-center p-4">
                            <span class="flex items-center gap-3 text-sm font-semibold">
                                <i class="ri-user-line text-lg text-[#355b34]"></i> Nama Kasir
                            </span>
                            <span class="text-sm font-bold text-gray-900">{{ auth()->user()->name }}</span>
                        </div>
                        
                        <!-- Tanggal -->
                        <div class="flex justify-between items-center p-4">
                            <span class="flex items-center gap-3 text-sm font-semibold">
                                <i class="ri-calendar-line text-lg text-[#355b34]"></i> Tanggal
                            </span>
                            <span class="text-sm font-bold text-gray-900">
                                {{ \Carbon\Carbon::parse($activeShift->tanggal)->translatedFormat('d F Y') }}
                            </span>
                        </div>

                        <!-- Jam Mulai -->
                        <div class="flex justify-between items-center p-4">
                            <span class="flex items-center gap-3 text-sm font-semibold">
                                <i class="ri-time-line text-lg text-[#355b34]"></i> Jam Mulai
                            </span>
                            <span class="text-sm font-bold text-gray-900">
                                {{ \Carbon\Carbon::parse($activeShift->jam_mulai)->format('H:i') }}
                            </span>
                        </div>

                        <!-- Jam Selesai -->
                        <div class="flex justify-between items-center p-4">
                            <span class="flex items-center gap-3 text-sm font-semibold">
                                <i class="ri-time-line text-lg text-[#355b34]"></i> Jam Selesai
                            </span>
                            <span class="text-sm font-bold text-gray-900">
                                {{ now()->format('H:i') }}
                            </span>
                        </div>
                    </div>

                    <!-- Catatan -->
                    <div class="flex flex-col gap-2">
                        <label class="text-xs font-bold uppercase tracking-wider">Catatan (Opsional)</label>
                        <textarea name="catatan" rows="4" placeholder="Masukkan catatan shift di sini..." class="w-full border border-gray-200 rounded-xl p-3.5 text-sm focus:outline-none focus:ring-1 focus:ring-[#355b34] focus:border-[#355b34] text-gray-900 transition duration-150"></textarea>
                    </div>

                    <!-- Submit Button -->
                    <button type="submit" class="bg-[#355b34] hover:bg-[#2d4a2a] text-white font-bold py-3.5 px-6 rounded-xl text-sm transition duration-150 shadow-sm flex items-center justify-center gap-2">
                        <i class="ri-play-fill text-base"></i> Tutup Shift
                    </button>
                </form>
            @endif
        </div>

        <!-- Right Side: Sidebar Info -->
        <div class="w-full lg:w-80 shrink-0">
            @if(!$activeShift)
                <!-- Informasi Shift Sebelumnya Card -->
                <div class="bg-white rounded-2xl border border-gray-200 p-6 shadow-sm w-full flex flex-col gap-5 mt-0 lg:mt-12">
                    <div class="flex items-center gap-2 border-b border-gray-100 pb-3">
                        <i class="ri-history-line text-lg text-[#355b34]"></i>
                        <h3 class="text-sm font-bold text-gray-900">Informasi Shift Sebelumnya</h3>
                    </div>
                    
                    @if($lastShift)
                        <div class="flex flex-col gap-4">
                            <!-- Tanggal -->
                            <div class="flex flex-col">
                                <span class="text-xs font-semibold text-gray-400">Tanggal</span>
                                <span class="text-sm font-bold text-gray-900 mt-0.5">
                                    {{ \Carbon\Carbon::parse($lastShift->tanggal)->translatedFormat('d F Y') }}
                                </span>
                            </div>

                            <!-- Jam Selesai -->
                            <div class="flex flex-col border-t border-gray-100 pt-3">
                                <span class="text-xs font-semibold text-gray-400">Jam Selesai</span>
                                <span class="text-sm font-bold text-gray-900 mt-0.5">
                                    {{ \Carbon\Carbon::parse($lastShift->jam_selesai)->format('H:i') }}
                                </span>
                            </div>

                            <!-- Total Penjualan -->
                            <div class="flex flex-col border-t border-gray-100 pt-3">
                                <span class="text-xs font-semibold text-gray-400">Total Penjualan</span>
                                <span class="text-sm font-bold text-gray-900 mt-0.5">
                                    Rp {{ number_format($lastShift->total_penjualan, 0, ',', '.') }}
                                </span>
                            </div>

                            <!-- Saldo Akhir -->
                            <div class="flex flex-col border-t border-gray-100 pt-3">
                                <span class="text-xs font-semibold text-gray-400">Saldo Akhir</span>
                                <span class="text-sm font-bold text-gray-900 mt-0.5">
                                    Rp {{ number_format($lastShift->saldo_akhir, 0, ',', '.') }}
                                </span>
                            </div>
                        </div>
                    @else
                        <div class="py-6 text-center text-xs text-gray-400">
                            Tidak ada riwayat shift sebelumnya.
                        </div>
                    @endif
                </div>
            @else
                <!-- Ringkasan Penjualan Card -->
                <div class="bg-white rounded-2xl border border-gray-200 p-6 shadow-sm w-full flex flex-col gap-5 mt-0 lg:mt-12">
                    <div class="flex items-center gap-2 border-b border-gray-100 pb-3">
                        <i class="ri-line-chart-line text-lg text-[#355b34]"></i>
                        <h3 class="text-sm font-bold text-gray-900">Ringkasan Penjualan</h3>
                    </div>

                    <div class="flex flex-col gap-4">
                        <!-- Modal Awal -->
                        <div class="flex flex-col">
                            <span class="text-xs font-semibold text-gray-400">Modal Awal</span>
                            <span class="text-sm font-bold text-gray-900 mt-0.5">
                                Rp {{ number_format($activeShift->modal_awal, 0, ',', '.') }}
                            </span>
                        </div>

                        <!-- Total Penjualan -->
                        <div class="flex flex-col border-t border-gray-100 pt-3">
                            <span class="text-xs font-semibold text-gray-400">Total Penjualan</span>
                            <span class="text-sm font-bold text-gray-900 mt-0.5">
                                Rp {{ number_format($totalPenjualan, 0, ',', '.') }}
                            </span>
                        </div>

                        <!-- Total Pengeluaran -->
                        <div class="flex flex-col border-t border-gray-100 pt-3">
                            <span class="text-xs font-semibold text-gray-400">Total Pengeluaran</span>
                            <span class="text-sm font-bold text-gray-900 mt-0.5">
                                Rp {{ number_format($activeShift->total_pengeluaran, 0, ',', '.') }}
                            </span>
                        </div>

                        <!-- Saldo Akhir -->
                        <div class="flex flex-col border-t border-gray-100 pt-3">
                            <span class="text-xs font-semibold text-gray-400">Saldo Akhir <span class="text-[10px] text-gray-400 font-normal">(Modal + Penjualan - Pengeluaran)</span></span>
                            <span class="text-sm font-bold text-gray-950 mt-0.5">
                                Rp {{ number_format($activeShift->modal_awal + $totalPenjualan - $activeShift->total_pengeluaran, 0, ',', '.') }}
                            </span>
                        </div>
                    </div>
                </div>
            @endif
        </div>

    </div>
</div>
@endsection
