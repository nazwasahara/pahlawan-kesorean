<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Menu;
use App\Models\User;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminOwnerController extends Controller
{
    public function dashboard()
    {
        // 1. Transaksi Hari Ini
        $totalTransactions = Order::whereDate('created_at', today())->count();

        // 2. Pendapatan Hari Ini
        $totalRevenue = Order::whereDate('created_at', today())
            ->whereIn('status', ['paid', 'processing', 'completed'])
            ->sum('total');

        // 3. Menu Terlaris
        $bestSeller = OrderItem::select('menu_id', DB::raw('SUM(quantity) as total_qty'))
            ->groupBy('menu_id')
            ->orderByDesc('total_qty')
            ->first();

        $bestSellerMenu = $bestSeller && $bestSeller->menu ? $bestSeller->menu->name : 'Ayam Geprek';

        // 4. Data Grafik: Pendapatan Bulanan selama 10 Bulan Terakhir
        $indonesianMonths = [
            1 => 'Jan', 2 => 'Feb', 3 => 'Mar', 4 => 'Apr', 5 => 'Mei', 6 => 'Jun',
            7 => 'Jul', 8 => 'Ags', 9 => 'Sep', 10 => 'Okt', 11 => 'Nov', 12 => 'Des'
        ];

        $chartData = [];
        $chartLabels = [];
        for ($i = 9; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $monthNum = $date->month;
            $monthLabel = $indonesianMonths[$monthNum] ?? $date->format('M');

            $revenue = Order::whereYear('created_at', $date->year)
                ->whereMonth('created_at', $date->month)
                ->whereIn('status', ['paid', 'processing', 'completed'])
                ->sum('total');

            $chartLabels[] = $monthLabel;
            $chartData[] = (float) $revenue;
        }

        // 5. Aktivitas Terbaru untuk Owner, atau Transaksi Terbaru untuk Admin
        $recentActivities = [];
        $recentTransactions = [];

        if (auth()->user()->role === 'owner') {
            $recentActivities = ActivityLog::orderBy('created_at', 'desc')
                ->orderBy('id', 'desc')
                ->limit(5)
                ->get();
        } else {
            $recentTransactions = Order::orderBy('created_at', 'desc')
                ->orderBy('id', 'desc')
                ->limit(5)
                ->get();
        }

        return view('admin-owner.dashboard', compact(
            'totalTransactions',
            'totalRevenue',
            'bestSellerMenu',
            'chartLabels',
            'chartData',
            'recentActivities',
            'recentTransactions'
        ));
    }

}
