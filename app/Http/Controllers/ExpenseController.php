<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use App\Models\ActivityLog;
use Illuminate\Http\Request;

class ExpenseController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        $date = $request->input('date');

        $query = Expense::query()->orderBy('date', 'desc')->orderBy('created_at', 'desc');

        if ($search) {
            $query->where('description', 'like', '%' . $search . '%');
        }

        if ($date) {
            $query->whereDate('date', $date);
        }

        // Calculate total amount based on the filtered results
        $totalAmount = $query->sum('amount');

        $expenses = $query->paginate(10)->withQueryString();

        return view('admin-owner.expenses.index', compact('expenses', 'search', 'date', 'totalAmount'));
    }

    public function store(Request $request)
    {
        if (auth()->user()->role !== 'admin') {
            abort(403, 'Aksi ini hanya diperbolehkan untuk Admin.');
        }

        $validated = $request->validate([
            'date' => 'required|date',
            'description' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0',
        ]);

        $expense = Expense::create($validated);

        ActivityLog::log('Catat Pengeluaran', 'Keluar Rp' . number_format($expense->amount, 0, ',', '.') . ' untuk ' . $expense->description);

        return redirect()->back()->with('success', 'Pengeluaran berhasil ditambahkan.');
    }

    public function update(Request $request, $id)
    {
        if (auth()->user()->role !== 'admin') {
            abort(403, 'Aksi ini hanya diperbolehkan untuk Admin.');
        }

        $expense = Expense::findOrFail($id);

        $validated = $request->validate([
            'date' => 'required|date',
            'description' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0',
        ]);

        $expense->update($validated);

        ActivityLog::log('Edit Pengeluaran', 'Ubah pengeluaran menjadi keluar Rp' . number_format($expense->amount, 0, ',', '.') . ' untuk ' . $expense->description);

        return redirect()->back()->with('success', 'Pengeluaran berhasil diperbarui.');
    }

    public function destroy($id)
    {
        if (auth()->user()->role !== 'admin') {
            abort(403, 'Aksi ini hanya diperbolehkan untuk Admin.');
        }

        $expense = Expense::findOrFail($id);
        $expense->delete();

        ActivityLog::log('Hapus pengeluaran', 'Hapus keluar Rp' . number_format($expense->amount, 0, ',', '.') . ' untuk ' . $expense->description);

        return redirect()->back()->with('success', 'Pengeluaran berhasil dihapus.');
    }
}
