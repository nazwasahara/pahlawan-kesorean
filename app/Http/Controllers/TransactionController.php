<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;

class TransactionController extends Controller
{
    public function index(Request $request)
    {
        Order::cancelExpiredOrders();
        $search = $request->input('search');
        $status = $request->input('status');
        $date = $request->input('date');

        $query = Order::query()->with('shift.user')->orderBy('created_at', 'desc');

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('order_number', 'like', '%' . $search . '%')
                  ->orWhere('customer_name', 'like', '%' . $search . '%');
            });
        }

        if ($status && $status !== 'all') {
            // Mapping statuses
            if ($status === 'paid') {
                $query->where('status', 'paid');
            } elseif ($status === 'pending') {
                $query->where('status', 'pending');
            } elseif ($status === 'processing') {
                $query->where('status', 'processing');
            } elseif ($status === 'completed') {
                $query->where('status', 'completed');
            } elseif ($status === 'cancelled') {
                $query->where('status', 'cancelled');
            }
        }

        if ($date) {
            $query->whereDate('created_at', $date);
        }

        $orders = $query->paginate(10)->withQueryString();

        return view('admin-owner.transactions.index', compact('orders', 'search', 'status', 'date'));
    }

    public function show($id)
    {
        Order::cancelExpiredOrders();
        $order = Order::with(['items', 'payment', 'shift'])->findOrFail($id);
        return view('admin-owner.transactions.show', compact('order'));
    }
}
