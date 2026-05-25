@extends('admin-owner.layouts.app')

@section('title', 'Dashboard - Pahlawan Kesorean')

@section('admin-owner-content')
<div class="space-y-6">
    <!-- Header Title -->
    <div>
        <h1 class="text-2xl font-extrabold text-gray-900 tracking-tight capitalize">
            {{ auth()->user()->role }} Dashboard - Kesorean
        </h1>
        <p class="text-xs font-bold text-gray-500 mt-1">
            Ringkasan Kinerja dan Status Bisnis.
        </p>
    </div>

    <!-- Stats Cards Grid -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- Card 1: Transaksi Hari Ini -->
        <div class="bg-[#E2F0D9] border border-[#B5D7A8]/80 rounded-[1rem] p-6 flex items-center gap-5 shadow-sm transition duration-200 hover:shadow-md min-w-0">
            <div class="w-14 h-14 bg-white rounded-2xl flex items-center justify-center text-[#125E34] shrink-0 shadow-sm border border-[#B5D7A8]/40">
                <i class="ri-folder-fill text-2xl"></i>
            </div>
            <div class="min-w-0 flex-1">
                <p class="text-2xl font-black text-gray-900 leading-tight truncate">
                    {{ $totalTransactions }}
                </p>
                <p class="text-[11px] font-bold text-[#125E34] mt-0.5">
                    Transaksi Hari Ini
                </p>
            </div>
        </div>

        <!-- Card 2: Pendapatan Hari Ini -->
        <div class="bg-[#FFF2CC] border border-[#FFE599]/80 rounded-[1rem] p-6 flex items-center gap-5 shadow-sm transition duration-200 hover:shadow-md min-w-0">
            <div class="w-14 h-14 bg-white rounded-2xl flex items-center justify-center text-[#E65100] shrink-0 shadow-sm border border-[#FFE599]/40">
                <i class="ri-wallet-3-fill text-2xl"></i>
            </div>
            <div class="min-w-0 flex-1">
                <p class="text-2xl font-black text-gray-900 leading-tight truncate">
                    Rp{{ number_format($totalRevenue, 0, ',', '.') }}
                </p>
                <p class="text-[11px] font-bold text-[#E65100] mt-0.5">
                    Pendapatan Hari Ini
                </p>
            </div>
        </div>

        <!-- Card 3: Menu Terlaris -->
        <div class="bg-[#D9E1F2] border border-[#B4C6E7]/80 rounded-[1rem] p-6 flex items-center gap-5 shadow-sm transition duration-200 hover:shadow-md min-w-0">
            <div class="w-14 h-14 bg-white rounded-2xl flex items-center justify-center text-[#0D47A1] shrink-0 shadow-sm border border-[#B4C6E7]/40">
                <i class="ri-star-fill text-2xl"></i>
            </div>
            <div class="min-w-0 flex-1">
                <p class="text-base sm:text-lg font-black text-gray-900 leading-tight break-words">
                    {{ $bestSellerMenu }}
                </p>
                <p class="text-[11px] font-bold text-[#0D47A1] mt-0.5">
                    Menu Terlaris
                </p>
            </div>
        </div>
    </div>

    <!-- Bottom Section Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 items-start">
        <!-- Revenue Chart Card -->
        <div class="lg:col-span-2 bg-white border border-gray-200/80 rounded-[1rem] p-6 shadow-sm flex flex-col h-[380px]">
            <h3 class="text-sm font-black text-gray-900 tracking-wide mb-4">Total Pendapatan (Rp)</h3>
            <div class="flex-1 min-h-0 relative">
                <canvas id="revenueChart"></canvas>
            </div>
        </div>

        <!-- Recent Activities Card / Latest Transactions Card -->
        @if(auth()->user()->role === 'owner')
        <div class="bg-white border border-gray-200/80 rounded-[1rem] p-6 shadow-sm flex flex-col h-[380px]">
            <div class="flex justify-between items-center mb-6">
                <h3 class="text-sm font-black text-gray-900 tracking-wide">Aktivitas Terbaru</h3>
                <a href="{{ route('admin-owner.logs.index') }}" class="text-[10px] font-bold text-[#125E34] hover:underline flex items-center gap-1">
                    Lihat Semua <i class="ri-arrow-right-line"></i>
                </a>
            </div>

            <!-- Table content -->
            <div class="flex-grow overflow-y-auto">
                <table class="w-full text-left">
                    <thead>
                        <tr class="border-b border-gray-150 text-[10px] font-extrabold text-gray-400 uppercase tracking-wider">
                            <th class="pb-3 pr-2 text-center">User</th>
                            <th class="pb-3 px-2 text-center">Aksi</th>
                            <th class="pb-3 pl-2 text-center">Waktu</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 text-xs font-bold text-gray-800">
                        @forelse($recentActivities as $activity)
                            <tr>
                                <td class="py-3.5 pr-2">
                                    @if($activity->user_identity === 'Sistem')
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-black bg-[#E8F5E9] text-[#125E34]">
                                            Sistem
                                        </span>
                                    @elseif($activity->user_identity === 'Admin')
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-black bg-[#E3F2FD] text-[#0D47A1]">
                                            Admin
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-black bg-[#FFF3E0] text-[#E65100]">
                                            {{ $activity->user_identity }}
                                        </span>
                                    @endif
                                </td>
                                <td class="py-3.5 px-2 text-gray-650 font-medium">{{ $activity->action }}</td>
                                <td class="py-3.5 pl-2 text-right text-gray-400 font-medium text-[10px]">{{ $activity->created_at->diffForHumans() }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="py-6 text-center text-gray-450 font-bold text-xs">Tidak ada aktivitas terbaru</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @elseif(auth()->user()->role === 'admin')
        <div class="bg-white border border-gray-200/80 rounded-[1rem] p-6 shadow-sm flex flex-col h-[380px]">
            <div class="flex justify-between items-center mb-6">
                <h3 class="text-sm font-black text-gray-900 tracking-wide">Transaksi Terbaru</h3>
                <a href="{{ route('admin-owner.transactions.index') }}" class="text-[10px] font-bold text-[#125E34] hover:underline flex items-center gap-1">
                    Lihat Semua <i class="ri-arrow-right-line"></i>
                </a>
            </div>

            <!-- Table content -->
            <div class="flex-grow overflow-y-auto">
                <table class="w-full text-left">
                    <thead>
                        <tr class="border-b border-gray-150 text-[10px] font-extrabold text-gray-400 uppercase tracking-wider">
                            <th class="pb-3 pr-2">Invoice</th>
                            <th class="pb-3 px-2">Pelanggan</th>
                            <th class="pb-3 pl-2 text-center">Status Order</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 text-xs font-bold text-gray-800">
                        @forelse($recentTransactions as $transaction)
                            <tr>
                                <td class="py-3.5 pr-2">
                                    <a href="{{ route('admin-owner.transactions.show', $transaction->id) }}" class="text-[#125E34] hover:underline font-extrabold">
                                        #{{ $transaction->order_number }}
                                    </a>
                                </td>
                                <td class="py-3.5 px-2 text-gray-650 font-medium truncate max-w-[90px]">{{ $transaction->customer_name }}</td>
                                <td class="py-3.5 pl-2">
                                    @if($transaction->status === 'pending')
                                        <span class="inline-block px-2.5 py-0.5 rounded-full text-[9px] font-black tracking-wide shadow-sm" style="background-color: #FFFDE7; color: #856404; border: 1px solid #FFF59D;">
                                            Menunggu
                                        </span>
                                    @elseif($transaction->status === 'paid')
                                        <span class="inline-block px-2.5 py-0.5 rounded-full text-[9px] font-black tracking-wide shadow-sm" style="background-color: #E8F5E9; color: #1B5E20; border: 1px solid #C8E6C9;">
                                            Lunas
                                        </span>
                                    @elseif($transaction->status === 'processing')
                                        <span class="inline-block px-2.5 py-0.5 rounded-full text-[9px] font-black tracking-wide shadow-sm" style="background-color: #E3F2FD; color: #0D47A1; border: 1px solid #BBDEFB;">
                                            Diproses
                                        </span>
                                    @elseif($transaction->status === 'completed')
                                        <span class="inline-block px-2.5 py-0.5 rounded-full text-[9px] font-black tracking-wide shadow-sm" style="background-color: #E3F2FD; color: #0D47A1; border: 1px solid #BBDEFB;">
                                            Selesai
                                        </span>
                                    @elseif($transaction->status === 'cancelled')
                                        <span class="inline-block px-2.5 py-0.5 rounded-full text-[9px] font-black tracking-wide shadow-sm" style="background-color: #FFEBEE; color: #C62828; border: 1px solid #FFCDD2;">
                                            Batal
                                        </span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="py-6 text-center text-gray-450 font-bold text-xs">Tidak ada transaksi terbaru</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @endif
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const ctx = document.getElementById('revenueChart').getContext('2d');
    
    // Create background gradient
    const gradient = ctx.createLinearGradient(0, 0, 0, 300);
    gradient.addColorStop(0, 'rgba(18, 94, 52, 0.25)'); // #125E34 with 0.25 opacity
    gradient.addColorStop(1, 'rgba(18, 94, 52, 0.0)');
    
    const chartLabels = @json($chartLabels);
    const chartData = @json($chartData);

    new Chart(ctx, {
        type: 'line',
        data: {
            labels: chartLabels,
            datasets: [{
                label: 'Total Pendapatan',
                data: chartData,
                borderColor: '#125E34',
                borderWidth: 2.5,
                backgroundColor: gradient,
                fill: true,
                tension: 0.4,
                pointBackgroundColor: '#125E34',
                pointBorderColor: '#ffffff',
                pointBorderWidth: 1.5,
                pointRadius: 4,
                pointHoverRadius: 6
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                },
                tooltip: {
                    backgroundColor: '#125E34',
                    titleFont: {
                        family: 'Inter, sans-serif',
                        weight: 'bold',
                        size: 11
                    },
                    bodyFont: {
                        family: 'Inter, sans-serif',
                        size: 11
                    },
                    padding: 8,
                    displayColors: false,
                    callbacks: {
                        label: function(context) {
                            return 'Rp ' + context.parsed.y.toLocaleString('id-ID');
                        }
                    }
                }
            },
            scales: {
                x: {
                    grid: {
                        display: false
                    },
                    ticks: {
                        color: '#9ca3af',
                        font: {
                            family: 'Inter, sans-serif',
                            weight: 'bold',
                            size: 10
                        }
                    }
                },
                y: {
                    beginAtZero: true,
                    grid: {
                        color: '#f3f4f6',
                        borderDash: [5, 5],
                        drawBorder: false
                    },
                    ticks: {
                        color: '#9ca3af',
                        font: {
                            family: 'Inter, sans-serif',
                            weight: 'bold',
                            size: 10
                        },
                        callback: function(value) {
                            if (value >= 1000000) {
                                return (value / 1000000).toLocaleString('id-ID', {
                                    maximumFractionDigits: 1
                                }) + ' jt';
                            }

                            if (value >= 1000) {
                                return (value / 1000).toLocaleString('id-ID', {
                                    maximumFractionDigits: 0
                                }) + ' rb';
                            }

                            return value;
                        }
                    }
                }
            }
        }
    });
});
</script>
@endpush
@endsection
