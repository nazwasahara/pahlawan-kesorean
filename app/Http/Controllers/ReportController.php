<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Expense;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        if (auth()->user()->role !== 'owner') {
            abort(403, 'Aksi ini hanya diperbolehkan untuk Owner.');
        }

        $type = $request->input('type', 'bulanan');
        $month = $request->input('month', Carbon::now()->month);
        $year = $request->input('year', Carbon::now()->year);
        $dateStr = $request->input('date', Carbon::now()->toDateString());

        // Parse date for daily/weekly calculations
        $selectedDate = Carbon::parse($dateStr);

        if ($type === 'harian') {
            $start = $selectedDate->copy()->startOfDay();
            $end = $selectedDate->copy()->endOfDay();
            
            $prevStart = $start->copy()->subDay()->startOfDay();
            $prevEnd = $start->copy()->subDay()->endOfDay();
            
            $periodLabel = $selectedDate->translatedFormat('d F Y');
            $comparisonLabel = 'vs hari sebelumnya';
        } elseif ($type === 'mingguan') {
            $start = $selectedDate->copy()->startOfWeek();
            $end = $selectedDate->copy()->endOfWeek();
            
            $prevStart = $start->copy()->subWeek()->startOfWeek();
            $prevEnd = $start->copy()->subWeek()->endOfWeek();
            
            $periodLabel = 'Minggu (' . $start->translatedFormat('d M') . ' - ' . $end->translatedFormat('d M Y') . ')';
            $comparisonLabel = 'vs minggu sebelumnya';
        } else { // bulanan
            $start = Carbon::createFromDate($year, $month, 1)->startOfMonth();
            $end = Carbon::createFromDate($year, $month, 1)->endOfMonth();
            
            $prevStart = $start->copy()->subMonth()->startOfMonth();
            $prevEnd = $start->copy()->subMonth()->endOfMonth();
            
            $periodLabel = $start->translatedFormat('F Y');
            $comparisonLabel = 'vs bulan sebelumnya';
        }

        // 1. Revenue (Pemasukan)
        $revenue = Order::whereIn('status', ['paid', 'processing', 'completed'])
            ->whereBetween('updated_at', [$start, $end])
            ->sum('total');

        $transactionsCount = Order::whereIn('status', ['paid', 'processing', 'completed'])
            ->whereBetween('updated_at', [$start, $end])
            ->count();

        $averageOrderValue = $transactionsCount > 0 ? $revenue / $transactionsCount : 0;

        $prevRevenue = Order::whereIn('status', ['paid', 'processing', 'completed'])
            ->whereBetween('updated_at', [$prevStart, $prevEnd])
            ->sum('total');

        $revenueChangePercent = $prevRevenue > 0
            ? (($revenue - $prevRevenue) / $prevRevenue) * 100
            : ($revenue > 0 ? 100 : 0);

        // 2. Expenses (Pengeluaran)
        $expenses = Expense::whereBetween('date', [$start, $end])
            ->sum('amount');

        $invoiceCount = Expense::whereBetween('date', [$start, $end])
            ->count();

        $expensesCategoriesCount = Expense::whereBetween('date', [$start, $end])
            ->distinct('description')
            ->count();

        $prevExpenses = Expense::whereBetween('date', [$prevStart, $prevEnd])
            ->sum('amount');

        $expensesChangePercent = $prevExpenses > 0
            ? (($expenses - $prevExpenses) / $prevExpenses) * 100
            : ($expenses > 0 ? 100 : 0);

        // 3. Net Profit (Laba Bersih)
        $netProfit = $revenue - $expenses;
        $profitMargin = $revenue > 0 ? ($netProfit / $revenue) * 100 : 0;

        $prevNetProfit = $prevRevenue - $prevExpenses;
        $netProfitChangePercent = $prevNetProfit > 0
            ? (($netProfit - $prevNetProfit) / $prevNetProfit) * 100
            : ($netProfit > 0 ? 100 : 0);

        // Fetch Detailed Lists
        $orders = Order::whereIn('status', ['paid', 'processing', 'completed'])
            ->whereBetween('updated_at', [$start, $end])
            ->selectRaw('DATE(updated_at) as date, COUNT(*) as count, SUM(total) as total')
            ->groupBy('date')
            ->orderBy('date', 'desc')
            ->get();

        $expenseList = Expense::whereBetween('date', [$start, $end])
            ->orderBy('date', 'desc')
            ->get();

        // Handle Exporting
        $export = $request->input('export');
        if ($export === 'excel') {
            return $this->exportExcel($type, $periodLabel, $revenue, $transactionsCount, $averageOrderValue, $expenses, $invoiceCount, $expensesCategoriesCount, $netProfit, $profitMargin, $orders, $expenseList);
        } elseif ($export === 'pdf') {
            return view('admin-owner.reports.print', compact(
                'type',
                'periodLabel',
                'revenue',
                'transactionsCount',
                'averageOrderValue',
                'expenses',
                'invoiceCount',
                'expensesCategoriesCount',
                'netProfit',
                'profitMargin',
                'orders',
                'expenseList'
            ));
        }

        // Years range for filter
        $availableYears = range(Carbon::now()->year - 2, Carbon::now()->year + 2);

        return view('admin-owner.reports.index', compact(
            'revenue',
            'transactionsCount',
            'averageOrderValue',
            'revenueChangePercent',
            'expenses',
            'invoiceCount',
            'expensesCategoriesCount',
            'expensesChangePercent',
            'netProfit',
            'profitMargin',
            'netProfitChangePercent',
            'periodLabel',
            'comparisonLabel',
            'type',
            'month',
            'year',
            'dateStr',
            'availableYears',
            'orders',
            'expenseList'
        ));
    }

    private function exportExcel($type, $periodLabel, $revenue, $transactionsCount, $averageOrderValue, $expenses, $invoiceCount, $expensesCategoriesCount, $netProfit, $profitMargin, $orders, $expenseList)
    {
        $filename = "laporan-penjualan-" . strtolower($type) . "-" . date('Ymd') . ".csv";
        
        $headers = array(
            "Content-type"        => "text/csv; charset=UTF-8",
            "Content-Disposition" => "attachment; filename=$filename",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        );

        $callback = function() use ($type, $periodLabel, $revenue, $transactionsCount, $averageOrderValue, $expenses, $invoiceCount, $expensesCategoriesCount, $netProfit, $profitMargin, $orders, $expenseList) {
            $file = fopen('php://output', 'w');
            
            // Add UTF-8 BOM for proper Excel encoding
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));

            fputcsv($file, ['Laporan Keuangan & Penjualan - Pahlawan Kesorean']);
            fputcsv($file, []);
            fputcsv($file, ['Parameter', 'Detail / Nilai']);
            fputcsv($file, ['Tipe Laporan', ucfirst($type)]);
            fputcsv($file, ['Periode', $periodLabel]);
            fputcsv($file, []);
            fputcsv($file, ['Pemasukan (Revenue)', 'Rp ' . number_format($revenue, 0, ',', '.')]);
            fputcsv($file, ['Total Transaksi', $transactionsCount]);
            fputcsv($file, ['Rata-rata Nilai Pesanan', 'Rp ' . number_format($averageOrderValue, 0, ',', '.')]);
            fputcsv($file, []);
            fputcsv($file, ['Pengeluaran (Expenses)', 'Rp ' . number_format($expenses, 0, ',', '.')]);
            fputcsv($file, ['Jumlah Invoice', $invoiceCount]);
            fputcsv($file, ['Kategori Pengeluaran', $expensesCategoriesCount]);
            fputcsv($file, []);
            fputcsv($file, ['Laba Bersih', 'Rp ' . number_format($netProfit, 0, ',', '.')]);
            fputcsv($file, ['Profit Margin', number_format($profitMargin, 2, ',', '.') . '%']);
            
            fputcsv($file, []);
            fputcsv($file, ['DETAIL PEMASUKAN HARIAN']);
            fputcsv($file, ['Tanggal', 'Jumlah Transaksi', 'Total Pemasukan']);
            foreach ($orders as $o) {
                $formattedDate = \Carbon\Carbon::parse($o->date)->translatedFormat('d M Y');
                fputcsv($file, [$formattedDate, $o->count . ' Transaksi', 'Rp ' . number_format($o->total, 0, ',', '.')]);
            }

            fputcsv($file, []);
            fputcsv($file, ['DETAIL PENGELUARAN']);
            fputcsv($file, ['Tanggal', 'Deskripsi/Keterangan', 'Jumlah']);
            foreach ($expenseList as $e) {
                fputcsv($file, [$e->date->format('Y-m-d'), $e->description, 'Rp ' . number_format($e->amount, 0, ',', '.')]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
