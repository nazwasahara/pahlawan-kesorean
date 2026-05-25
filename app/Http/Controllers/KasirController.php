<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\Menu;
use App\Models\Shift;
use App\Models\ActivityLog;
use Illuminate\Support\Facades\DB;

class KasirController extends Controller
{
    /**
     * Display the kasir dashboard.
     */
    public function index()
    {
        $this->cancelExpiredOrders();
        $categories = Category::orderBy('name')->get();
        $menus = Menu::where('is_available', true)->get();

        $occupiedTables = Order::whereNotIn('status', ['completed', 'cancelled'])
            ->whereNotNull('table_number')
            ->pluck('table_number')
            ->map(function ($val) {
                return (int) $val;
            })
            ->unique()
            ->toArray();

        return view('kasir.dashboard', compact('categories', 'menus', 'occupiedTables'));
    }

    public function storeOrder(Request $request)
    {
        $validated = $request->validate([
            'customer_name' => ['required', 'string', 'max:255'],
            'table_number' => ['nullable', 'regex:/^[0-9]+$/', 'max:50'],
            'order_type' => ['nullable', 'in:dine_in,take_away'],
            'payment_method' => ['required', 'in:cash,qris,debit'],
            'discount' => ['nullable', 'numeric', 'min:0'],
            'money_received' => ['nullable', 'numeric', 'min:0'],
            'note' => ['nullable', 'string'],
            'status' => ['nullable', 'string', 'in:paid,cancelled,pending'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.id' => ['required', 'integer', 'exists:menus,id'],
            'items.*.qty' => ['required', 'integer', 'min:1'],
        ]);

        // Check if table number is occupied for dine-in orders
        if ($request->filled('table_number')) {
            $isOccupied = Order::whereNotIn('status', ['completed', 'cancelled'])
                ->where('table_number', $request->table_number)
                ->exists();
            if ($isOccupied) {
                return response()->json([
                    'message' => 'Meja ' . $request->table_number . ' sedang terisi.'
                ], 422);
            }
        }

        $result = DB::transaction(function () use ($validated, $request) {
            $discount = (float) ($validated['discount'] ?? 0);
            $orderType = $validated['order_type'] ?? 'dine_in';
            $status = $validated['status'] ?? 'paid';

            $itemsByMenuId = [];
            foreach ($validated['items'] as $item) {
                $menuId = (int) $item['id'];
                $qty = (int) $item['qty'];
                $itemsByMenuId[$menuId] = ($itemsByMenuId[$menuId] ?? 0) + $qty;
            }

            $menuIds = array_keys($itemsByMenuId);
            $menus = Menu::whereIn('id', $menuIds)->lockForUpdate()->get()->keyBy('id');

            $subtotal = 0;
            foreach ($itemsByMenuId as $menuId => $qty) {
                $menu = $menus->get($menuId);
                if (!$menu) {
                    abort(422, 'Menu tidak ditemukan.');
                }

                // Only check availability and stock if the order status is paid
                if ($status !== 'cancelled') {
                    if (!$menu->is_available) {
                        abort(422, "Menu {$menu->name} tidak tersedia.");
                    }
                    if ((int) $menu->stock < $qty) {
                        abort(422, "Stok menu {$menu->name} tidak mencukupi.");
                    }
                }
                $subtotal += ((float) $menu->price) * $qty;
            }

            $total = max(0, $subtotal - $discount);

            $moneyReceived = isset($validated['money_received']) ? (float) $validated['money_received'] : null;
            if ($status !== 'cancelled' && $validated['payment_method'] === 'cash' && ($moneyReceived === null || $moneyReceived < $total)) {
                abort(422, 'Uang diterima kurang dari total pembayaran.');
            }

            $activeShift = Shift::where('user_id', auth()->id())->where('status', 'open')->first();
            if (!$activeShift && $status !== 'cancelled') {
                abort(422, 'Silakan buka shift terlebih dahulu sebelum melakukan transaksi.');
            }

            $order = new Order();
            $today = now()->startOfDay();
            $tomorrow = now()->endOfDay();
            $todayCount = Order::whereBetween('created_at', [$today, $tomorrow])->count();

            $sequence = $todayCount + 1;
            do {
                $orderNumber = 'POS-' . date('dmY') . '-' . $sequence;
                $sequence++;
            } while (Order::where('order_number', $orderNumber)->exists());
            $order->order_number = $orderNumber;
            $order->session_id = $request->session()->getId();
            $order->shift_id = $activeShift?->id;
            $order->customer_name = $validated['customer_name'];
            $order->order_type = $orderType;
            $order->table_number = ($orderType === 'take_away') ? null : ($validated['table_number'] !== null && $validated['table_number'] !== '' 
                ? (string) (int) $validated['table_number'] 
                : null);
            $order->payment_method = $validated['payment_method'];
            $order->subtotal = $subtotal;
            $order->discount = $discount;
            $order->total = $total;
            $order->status = $status;
            $order->save();

            foreach ($itemsByMenuId as $menuId => $qty) {
                $menu = $menus->get($menuId);
                $lineSubtotal = ((float) $menu->price) * $qty;

                $orderItem = new OrderItem();
                $orderItem->order_id = $order->id;
                $orderItem->menu_id = $menu->id;
                $orderItem->menu_name = $menu->name;
                $orderItem->price = $menu->price;
                $orderItem->quantity = $qty;
                $orderItem->note = $validated['note'] ?? null;
                $orderItem->subtotal = $lineSubtotal;
                $orderItem->save();

                // Only deduct stock if the order status is paid
                if ($status === 'paid' || $status === 'completed' || $status === 'processing') {
                    $menu->stock = (int) $menu->stock - $qty;
                    if ((int) $menu->stock <= 0) {
                        $menu->stock = 0;
                    }
                    $menu->save();
                }
            }

            $payment = new Payment();
            $payment->order_id = $order->id;
            $payment->method = $validated['payment_method'];
            $payment->amount = $total;
            $payment->status = $status === 'cancelled' ? 'failed' : ($status === 'pending' ? 'pending' : 'paid');
            $payment->reference_number = 'PAY-' . random_int(100000, 999999);
            $payment->paid_at = in_array($status, ['cancelled', 'pending']) ? null : now();
            $payment->save();

            $change = 0;
            if ($status !== 'cancelled' && $validated['payment_method'] === 'cash' && $moneyReceived !== null) {
                $change = $moneyReceived - $total;
            }

            return [
                'order_number' => $order->order_number,
                'paid_at' => $payment->paid_at ? optional($payment->paid_at)->format('d M Y, H:i') . ' WIB' : ($status === 'pending' ? 'Belum Bayar' : 'Dibatalkan'),
                'payment_method' => $validated['payment_method'],
                'total' => $total,
                'change' => $change,
                'status' => $order->status,
            ];
        });

        if ($result['status'] === 'cancelled') {
            ActivityLog::log('Batal transaksi', 'Batal transaksi #' . $result['order_number']);
        } else {
            ActivityLog::log('Tambah transaksi', 'Tambah transaksi #' . $result['order_number']);
        }

        return response()->json([
            'message' => $result['status'] === 'cancelled' ? 'Order berhasil dibatalkan.' : 'Order berhasil disimpan.',
            'data' => $result,
        ], 201);
    }

    /**
     * Display order history with filters.
     */
    public function orders(Request $request)
    {
        $this->cancelExpiredOrders();
        $query = Order::with(['items', 'payment'])->orderBy('created_at', 'desc');

        // Search filter
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('order_number', 'like', '%' . $search . '%')
                    ->orWhere('customer_name', 'like', '%' . $search . '%');
            });
        }

        // Status filter
        if ($request->filled('status') && $request->input('status') !== 'all') {
            $status = $request->input('status');
            if ($status === 'menunggu') {
                $query->where('status', 'pending');
            } elseif ($status === 'diproses') {
                $query->whereIn('status', ['paid', 'processing']);
            } elseif ($status === 'selesai') {
                $query->where('status', 'completed');
            } elseif ($status === 'dibatalkan') {
                $query->where('status', 'cancelled');
            }
        }

        // Time filter (default to 'today' as in the image)
        $time = $request->input('time', 'today');
        if ($time === 'today') {
            $query->whereDate('created_at', today());
        } elseif ($time === '7_days') {
            $query->where('created_at', '>=', now()->subDays(7));
        } elseif ($time === 'this_month') {
            $query->whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year);
        }

        $orders = $query->paginate(10)->withQueryString();

        return view('kasir.orders', compact('orders', 'time'));
    }

    /**
     * Display order details on a dedicated page.
     */
    public function showOrderDetail($id)
    {
        $this->cancelExpiredOrders();
        $order = Order::with(['items.menu', 'payment'])->findOrFail($id);

        $occupiedTables = Order::whereNotIn('status', ['completed', 'cancelled'])
            ->whereNotNull('table_number')
            ->where('id', '!=', $order->id)
            ->pluck('table_number')
            ->map(function ($val) {
                return (int) $val;
            })
            ->unique()
            ->toArray();

        return view('kasir.show-order', compact('order', 'occupiedTables'));
    }

    /**
     * Display Shift management page.
     */
    public function shift()
    {
        $activeShift = Shift::where('user_id', auth()->id())->where('status', 'open')->first();
        $lastShift = null;
        $totalPenjualan = 0;

        if (!$activeShift) {
            $lastShift = Shift::where('user_id', auth()->id())
                ->where('status', 'closed')
                ->orderBy('tanggal', 'desc')
                ->orderBy('jam_selesai', 'desc')
                ->first();
        } else {
            $totalPenjualan = Order::where('shift_id', $activeShift->id)
                ->whereIn('status', ['paid', 'completed'])
                ->sum('total');
        }

        return view('kasir.shift', compact('activeShift', 'lastShift', 'totalPenjualan'));
    }

    /**
     * Start a new shift.
     */
    public function startShift(Request $request)
    {
        $request->validate([
            'modal_awal' => 'required|numeric|min:0',
        ]);

        // Check if there is already an open shift
        $activeShift = Shift::where('user_id', auth()->id())->where('status', 'open')->first();
        if ($activeShift) {
            return redirect()->back()->with('error', 'Anda sudah memiliki shift yang aktif.');
        }

        Shift::create([
            'user_id' => auth()->id(),
            'tanggal' => now()->toDateString(),
            'jam_mulai' => now()->toTimeString(),
            'modal_awal' => $request->input('modal_awal'),
            'total_penjualan' => 0,
            'total_pengeluaran' => 0,
            'saldo_akhir' => $request->input('modal_awal'),
            'status' => 'open',
        ]);

        ActivityLog::log('Mulai Shift', 'Mulai shift dengan modal awal Rp' . number_format($request->input('modal_awal'), 0, ',', '.'));

        return redirect()->route('kasir.dashboard')->with('success', 'Shift berhasil dimulai.');
    }

    /**
     * End the current shift.
     */
    public function endShift(Request $request)
    {
        $activeShift = Shift::where('user_id', auth()->id())->where('status', 'open')->first();

        if (!$activeShift) {
            return redirect()->back()->with('error', 'Tidak ada shift aktif ditemukan.');
        }

        $totalPenjualan = Order::where('shift_id', $activeShift->id)
            ->whereIn('status', ['paid', 'completed'])
            ->sum('total');

        $saldoAkhir = $activeShift->modal_awal + $totalPenjualan - $activeShift->total_pengeluaran;

        $activeShift->update([
            'jam_selesai' => now()->toTimeString(),
            'total_penjualan' => $totalPenjualan,
            'saldo_akhir' => $saldoAkhir,
            'catatan' => $request->input('catatan'),
            'status' => 'closed',
        ]);

        ActivityLog::log('Tutup Shift', 'Tutup shift dengan total penjualan Rp' . number_format($totalPenjualan, 0, ',', '.'));

        return redirect()->route('kasir.shift')->with('success', 'Shift berhasil ditutup.');
    }

    /**
     * Update order status and table number.
     */
    public function updateOrder(Request $request, $id)
    {
        $order = Order::with('items')->findOrFail($id);

        $validated = $request->validate([
            'status' => 'required|in:pending,processing,paid,completed,cancelled',
            'table_number' => ['nullable', 'regex:/^[0-9]+$/', 'max:50'],
        ]);

        // Check if table number is occupied by another order
        if ($request->filled('table_number')) {
            $isOccupied = Order::whereNotIn('status', ['completed', 'cancelled'])
                ->where('table_number', $request->table_number)
                ->where('id', '!=', $order->id)
                ->exists();
            if ($isOccupied) {
                return redirect()->back()->with('error', 'Meja ' . $request->table_number . ' sedang terisi oleh pesanan lain.');
            }
        }

        // Prevent changing table number if it has already been set
        if ($order->table_number !== null) {
            if ($request->has('table_number') && (string)$request->table_number !== (string)$order->table_number) {
                return redirect()->back()->with('error', 'Nomor meja yang sudah dipilih tidak dapat diubah lagi.');
            }
        }

        $oldStatus = $order->status;
        $newStatus = $validated['status'];

        if ($oldStatus === 'cancelled' && $newStatus !== 'cancelled') {
            return redirect()->back()->with('error', 'Status pesanan yang sudah dibatalkan tidak dapat diubah lagi.');
        }

        $weights = [
            'pending' => 1,
            'paid' => 2,
            'processing' => 3,
            'completed' => 4,
            'cancelled' => 5,
        ];

        if ($newStatus !== 'cancelled') {
            if ($weights[$newStatus] < $weights[$oldStatus]) {
                return redirect()->back()->with('error', 'Status pesanan tidak dapat dikembalikan ke status sebelumnya.');
            }
        }

        $order->status = $newStatus;
        if ($request->has('table_number')) {
            $order->table_number = $validated['table_number'] !== null && $validated['table_number'] !== '' 
                ? (string) (int) $validated['table_number'] 
                : null;
        }

        // Update shift_id to cashier's active shift
        $activeShift = Shift::where('user_id', auth()->id())->where('status', 'open')->first();
        if ($activeShift) {
            $order->shift_id = $activeShift->id;
        }

        $order->save();

        if ($oldStatus !== $newStatus) {
            ActivityLog::log('Ubah status transaksi', "Ubah status transaksi #{$order->order_number} ke " . ucfirst($newStatus));
        } else {
            ActivityLog::log('Ubah transaksi', "Ubah transaksi #{$order->order_number}");
        }

        // Increment stock if order is cancelled
        if ($newStatus === 'cancelled' && $oldStatus !== 'cancelled') {
            foreach ($order->items as $item) {
                if ($item->menu) {
                    $item->menu->increment('stock', $item->quantity);
                }
            }
        }
        // Decrement stock if order is restored from cancelled
        elseif ($oldStatus === 'cancelled' && $newStatus !== 'cancelled') {
            foreach ($order->items as $item) {
                if ($item->menu) {
                    $item->menu->decrement('stock', $item->quantity);
                }
            }
        }
        // Decrement stock when transitioning from pending to active/paid
        elseif ($oldStatus === 'pending' && in_array($newStatus, ['paid', 'processing', 'completed'])) {
            foreach ($order->items as $item) {
                if ($item->menu) {
                    $item->menu->decrement('stock', $item->quantity);
                }
            }
        }

        return redirect()->back()->with('success', 'Order berhasil diperbarui.');
    }

    protected function cancelExpiredOrders()
    {
        Order::cancelExpiredOrders();
    }
}
