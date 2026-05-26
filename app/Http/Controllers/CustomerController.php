<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Menu;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\PromoCode;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CustomerController extends Controller
{
    public function index()
    {
        $this->cancelExpiredOrders();
        $promos = PromoCode::where('is_active', true)
            ->where(function ($query) {
                $query->whereNull('expired_at')->orWhere('expired_at', '>', now());
            })
            ->where(function ($query) {
                $query->whereNull('quota')->orWhereColumn('used_count', '<', 'quota');
            })
            ->get();

        return view('customer.index', compact('promos'));
    }

    public function menu()
    {
        $categories = Category::with('menus')->get();

        return view('customer.menu', compact('categories'));
    }

    public function cart()
    {
        $cart = Cart::where('session_id', session()->getId())->with('items.menu')->first();
        $items = $cart ? $cart->items : collect();

        $subtotal = $items->sum('subtotal');

        // Calculate discount
        $discount = 0;
        $promoCode = session('applied_promo_code');
        $promo = null;

        if ($promoCode) {
            $promo = PromoCode::where('code', $promoCode)->first();
            if ($promo && $subtotal >= $promo->minimum_transaction) {
                if ($promo->type === 'percentage') {
                    $discount = ($promo->value / 100) * $subtotal;
                    if ($promo->max_discount !== null && $discount > $promo->max_discount) {
                        $discount = $promo->max_discount;
                    }
                } else {
                    $discount = $promo->value;
                }
            } else {
                session()->forget('applied_promo_code');
                $promoCode = null;
            }
        }

        $total = max(0, $subtotal - $discount);

        return view('customer.cart', compact('items', 'subtotal', 'discount', 'total', 'promoCode'));
    }

    public function addToCart(Request $request)
    {
        $request->validate([
            'menu_id' => 'required|exists:menus,id',
            'quantity' => 'nullable|integer|min:1',
            'note' => 'nullable|string',
        ]);

        $menu = Menu::findOrFail($request->menu_id);
        if (!$menu->is_available || $menu->stock <= 0) {
            return response()->json([
                'success' => false,
                'message' => 'Menu ini sedang tidak tersedia atau sudah habis.'
            ], 422);
        }

        $quantity = $request->input('quantity', 1);
        $note = $request->input('note');

        $sessionId = session()->getId();
        $cart = Cart::firstOrCreate(['session_id' => $sessionId]);

        $cartItem = CartItem::where('cart_id', $cart->id)
            ->where('menu_id', $menu->id)
            ->first();

        if ($cartItem) {
            $cartItem->quantity += $quantity;
            $cartItem->subtotal = $cartItem->quantity * $cartItem->price;
            $cartItem->save();
        } else {
            $cartItem = new CartItem();
            $cartItem->cart_id = $cart->id;
            $cartItem->menu_id = $menu->id;
            $cartItem->quantity = $quantity;
            $cartItem->note = $note;
            $cartItem->price = $menu->price;
            $cartItem->subtotal = $menu->price * $quantity;
            $cartItem->save();
        }

        $cartCount = $cart->items()->sum('quantity');

        return response()->json([
            'success' => true,
            'message' => 'Menu berhasil ditambahkan ke keranjang!',
            'cart_count' => $cartCount
        ]);
    }

    public function updateCart(Request $request)
    {
        $request->validate([
            'cart_item_id' => 'required|exists:cart_items,id',
            'quantity' => 'required|integer|min:0',
            'note' => 'nullable|string',
        ]);

        $cartItem = CartItem::findOrFail($request->cart_item_id);

        if ($request->quantity == 0) {
            $cartItem->delete();
        } else {
            $cartItem->quantity = $request->quantity;
            if ($request->has('note')) {
                $cartItem->note = $request->note;
            }
            $cartItem->subtotal = $cartItem->quantity * $cartItem->price;
            $cartItem->save();
        }

        $cart = Cart::where('session_id', session()->getId())->first();
        $cartCount = $cart ? $cart->items()->sum('quantity') : 0;

        // Recalculate totals
        $items = $cart ? $cart->items()->with('menu')->get() : collect();
        $subtotal = $items->sum('subtotal');

        $discount = 0;
        $promoCode = session('applied_promo_code');
        if ($promoCode) {
            $promo = PromoCode::where('code', $promoCode)->first();
            if ($promo && $subtotal >= $promo->minimum_transaction) {
                if ($promo->type === 'percentage') {
                    $discount = ($promo->value / 100) * $subtotal;
                    if ($promo->max_discount !== null && $discount > $promo->max_discount) {
                        $discount = $promo->max_discount;
                    }
                } else {
                    $discount = $promo->value;
                }
            } else {
                session()->forget('applied_promo_code');
                $promoCode = null;
            }
        }

        $total = max(0, $subtotal - $discount);

        return response()->json([
            'success' => true,
            'cart_count' => $cartCount,
            'subtotal' => $subtotal,
            'discount' => $discount,
            'total' => $total,
            'item_subtotal' => $cartItem->subtotal ?? 0,
            'deleted' => $request->quantity == 0,
            'promo_removed' => ($promoCode === null && session('applied_promo_code') === null)
        ]);
    }

    public function removeFromCart(Request $request)
    {
        $request->validate([
            'cart_item_id' => 'required|exists:cart_items,id',
        ]);

        $cartItem = CartItem::findOrFail($request->cart_item_id);
        $cartItem->delete();

        $cart = Cart::where('session_id', session()->getId())->first();
        $cartCount = $cart ? $cart->items()->sum('quantity') : 0;

        $items = $cart ? $cart->items : collect();
        $subtotal = $items->sum('subtotal');

        $discount = 0;
        $promoCode = session('applied_promo_code');
        if ($promoCode) {
            $promo = PromoCode::where('code', $promoCode)->first();
            if ($promo && $subtotal >= $promo->minimum_transaction) {
                if ($promo->type === 'percentage') {
                    $discount = ($promo->value / 100) * $subtotal;
                    if ($promo->max_discount !== null && $discount > $promo->max_discount) {
                        $discount = $promo->max_discount;
                    }
                } else {
                    $discount = $promo->value;
                }
            } else {
                session()->forget('applied_promo_code');
                $promoCode = null;
            }
        }

        $total = max(0, $subtotal - $discount);

        return response()->json([
            'success' => true,
            'cart_count' => $cartCount,
            'subtotal' => $subtotal,
            'discount' => $discount,
            'total' => $total,
            'promo_removed' => ($promoCode === null && session('applied_promo_code') === null)
        ]);
    }

    public function applyPromo(Request $request)
    {
        $request->validate([
            'code' => 'required|string',
        ]);

        $code = strtoupper($request->code);
        $promo = PromoCode::where('code', $code)
            ->where('is_active', true)
            ->where(function ($query) {
                $query->whereNull('expired_at')->orWhere('expired_at', '>', now());
            })
            ->first();

        if (!$promo) {
            return response()->json([
                'success' => false,
                'message' => 'Kode promo tidak valid atau sudah kedaluwarsa!'
            ]);
        }

        if ($promo->quota !== null && $promo->used_count >= $promo->quota) {
            return response()->json([
                'success' => false,
                'message' => 'Kuota kode promo sudah habis!'
            ]);
        }

        // Prevent double usage of the same coupon by the same session or cookie
        $alreadyUsed = Order::where('session_id', session()->getId())
            ->where('promo_code', $promo->code)
            ->where('status', '!=', 'cancelled')
            ->exists() || request()->cookie('used_promo_' . $promo->code) === '1';

        if ($alreadyUsed) {
            return response()->json([
                'success' => false,
                'message' => 'Anda sudah pernah menggunakan kode promo ini pada pesanan sebelumnya.'
            ]);
        }

        $cart = Cart::where('session_id', session()->getId())->first();
        $subtotal = $cart ? $cart->items()->sum('subtotal') : 0;

        if ($subtotal < $promo->minimum_transaction) {
            return response()->json([
                'success' => false,
                'message' => 'Minimum transaksi untuk promo ini adalah Rp ' . number_format($promo->minimum_transaction, 0, ',', '.')
            ]);
        }

        session(['applied_promo_code' => $promo->code]);

        $discount = 0;
        if ($promo->type === 'percentage') {
            $discount = ($promo->value / 100) * $subtotal;
            if ($promo->max_discount !== null && $discount > $promo->max_discount) {
                $discount = $promo->max_discount;
            }
        } else {
            $discount = $promo->value;
        }

        $total = max(0, $subtotal - $discount);

        return response()->json([
            'success' => true,
            'message' => 'Kode promo berhasil digunakan!',
            'discount' => $discount,
            'total' => $total,
            'promo_code' => $promo->code
        ]);
    }

    public function removePromo()
    {
        session()->forget('applied_promo_code');

        $cart = Cart::where('session_id', session()->getId())->first();
        $subtotal = $cart ? $cart->items()->sum('subtotal') : 0;
        $total = $subtotal;

        return response()->json([
            'success' => true,
            'message' => 'Kode promo dihapus!',
            'total' => $total
        ]);
    }

    public function checkout()
    {
        $cart = Cart::where('session_id', session()->getId())->with('items.menu')->first();
        if (!$cart || $cart->items->count() === 0) {
            return redirect()->route('customer.menu')->with('error', 'Keranjang belanja Anda kosong.');
        }

        $items = $cart->items;
        $subtotal = $items->sum('subtotal');

        $discount = 0;
        $promoCode = session('applied_promo_code');
        if ($promoCode) {
            $promo = PromoCode::where('code', $promoCode)->first();
            if ($promo) {
                // Prevent double usage check on checkout loading
                $alreadyUsed = Order::where('session_id', session()->getId())
                    ->where('promo_code', $promo->code)
                    ->where('status', '!=', 'cancelled')
                    ->exists() || request()->cookie('used_promo_' . $promo->code) === '1';

                if ($alreadyUsed) {
                    session()->forget('applied_promo_code');
                    $promoCode = null;
                    return redirect()->route('customer.cart')->with('error', 'Anda sudah pernah menggunakan kode promo ini pada pesanan sebelumnya.');
                }

                if ($subtotal >= $promo->minimum_transaction) {
                    if ($promo->type === 'percentage') {
                        $discount = ($promo->value / 100) * $subtotal;
                        if ($promo->max_discount !== null && $discount > $promo->max_discount) {
                            $discount = $promo->max_discount;
                        }
                    } else {
                        $discount = $promo->value;
                    }
                }
            }
        }

        $total = max(0, $subtotal - $discount);

        return view('customer.checkout', compact('items', 'subtotal', 'discount', 'total', 'promoCode'));
    }

    public function storeCheckout(Request $request)
    {
        $request->validate([
            'customer_name' => 'required|string|max:255',
            'table_number' => ['nullable', 'regex:/^[0-9]+$/', 'max:50'],
            'order_type' => 'required|in:dine_in,take_away',
            'payment_method' => 'required|in:cash,qris,debit',
            'note' => 'nullable|string'
        ]);

        $cart = Cart::where('session_id', session()->getId())->with('items.menu')->first();
        if (!$cart || $cart->items->count() === 0) {
            return redirect()->route('customer.menu')->with('error', 'Keranjang belanja Anda kosong.');
        }

        $items = $cart->items;
        $subtotal = $items->sum('subtotal');

        $discount = 0;
        $promoCode = session('applied_promo_code');
        $promo = null;
        if ($promoCode) {
            $promo = PromoCode::where('code', $promoCode)->first();
            if ($promo) {
                // Double check prevention during store checkout submission
                $alreadyUsed = Order::where('session_id', session()->getId())
                    ->where('promo_code', $promo->code)
                    ->where('status', '!=', 'cancelled')
                    ->exists() || request()->cookie('used_promo_' . $promo->code) === '1';

                if ($alreadyUsed) {
                    session()->forget('applied_promo_code');
                    return redirect()->route('customer.cart')->with('error', 'Anda sudah pernah menggunakan kode promo ini pada pesanan sebelumnya.');
                }

                if ($subtotal >= $promo->minimum_transaction) {
                    if ($promo->type === 'percentage') {
                        $discount = ($promo->value / 100) * $subtotal;
                        if ($promo->max_discount !== null && $discount > $promo->max_discount) {
                            $discount = $promo->max_discount;
                        }
                    } else {
                        $discount = $promo->value;
                    }
                }
            }
        }

        $total = max(0, $subtotal - $discount);

        $order = DB::transaction(function () use ($request, $items, $subtotal, $discount, $total, $promo, $cart) {
            $order = new Order();
            $today = now()->startOfDay();
            $tomorrow = now()->endOfDay();
            $todayCount = Order::whereBetween('created_at', [$today, $tomorrow])->count();

            $sequence = $todayCount + 1;
            do {
                $orderNumber = 'CS-' . date('dmY') . '-' . $sequence;
                $sequence++;
            } while (Order::where('order_number', $orderNumber)->exists());
            $order->order_number = $orderNumber;
            $order->session_id = session()->getId();
            $order->customer_name = $request->customer_name;
            $order->table_number = $request->table_number !== null && $request->table_number !== '' 
                ? (string) (int) $request->table_number 
                : null;
            $order->order_type = $request->order_type;
            $order->payment_method = $request->payment_method;
            $order->subtotal = $subtotal;
            $order->discount = $discount;
            $order->total = $total;
            $order->status = 'pending';
            if ($promo) {
                $order->promo_code = $promo->code;
            }
            $order->save();

            ActivityLog::log('Transaksi', 'Transaksi online order #' . $order->order_number, 'Sistem');

            foreach ($items as $item) {
                $orderItem = new OrderItem();
                $orderItem->order_id = $order->id;
                $orderItem->menu_id = $item->menu_id;
                $orderItem->menu_name = $item->menu->name;
                $orderItem->price = $item->price;
                $orderItem->quantity = $item->quantity;
                $orderItem->note = $item->note ?? $request->note;
                $orderItem->subtotal = $item->subtotal;
                $orderItem->save();
            }

            $payment = new Payment();
            $payment->order_id = $order->id;
            $payment->method = $request->payment_method;
            $payment->amount = $total;
            $payment->status = 'pending';
            $payment->reference_number = 'PAY-' . random_int(100000, 999999);
            $payment->save();

            if ($promo) {
                $promo->increment('used_count');
                cookie()->queue('used_promo_' . $promo->code, '1', 60 * 24 * 365);
            }

            // Clear the cart
            $cart->items()->delete();
            $cart->delete();

            return $order;
        });

        session()->forget('applied_promo_code');

        return redirect()->route('customer.checkout.success', ['order_number' => $order->order_number]);
    }

    protected function cancelExpiredOrders()
    {
        Order::cancelExpiredOrders();
    }

    public function checkoutSuccess($orderNumber)
    {
        $this->cancelExpiredOrders();
        $order = Order::where('order_number', $orderNumber)->with('items')->firstOrFail();
        return view('customer.success', compact('order'));
    }

    public function cancelOrder($orderNumber)
    {
        $order = Order::where('order_number', $orderNumber)->firstOrFail();
        if ($order->status === 'pending') {
            $order->status = 'cancelled';
            $order->save();

            if ($order->payment) {
                $order->payment->status = 'failed';
                $order->payment->save();
            }

            ActivityLog::log('Batal transaksi', 'Batal transaksi #' . $order->order_number . ' karena QRIS expired', 'Sistem');

            return response()->json([
                'success' => true,
                'message' => 'Pesanan berhasil dibatalkan.'
            ]);
        }

        if ($order->status === 'cancelled') {
            return response()->json([
                'success' => false,
                'message' => 'Pesanan sudah dibatalkan sebelumnya.'
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Pesanan tidak dapat dibatalkan karena sudah dibayar atau sedang diproses.'
        ], 422);
    }

    public function history()
    {
        $this->cancelExpiredOrders();
        return view('customer.history');
    }

    public function getHistoryOrders(Request $request)
    {
        $this->cancelExpiredOrders();
        $orderNumbers = $request->input('order_numbers', []);

        if (empty($orderNumbers)) {
            return response()->json([]);
        }

        $orders = Order::whereIn('order_number', $orderNumbers)
            ->with('items')
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json($orders);
    }

    public function location()
    {
        return view('customer.location');
    }

    public function promos()
    {
        $promos = PromoCode::where('is_active', true)
            ->where(function ($query) {
                $query->whereNull('expired_at')->orWhere('expired_at', '>', now());
            })
            ->where(function ($query) {
                $query->whereNull('quota')->orWhereColumn('used_count', '<', 'quota');
            })
            ->get();

        return view('customer.promos', compact('promos'));
    }

    public function getOrderDetails($orderNumber)
    {
        $this->cancelExpiredOrders();
        $orderNumber = strtoupper(trim($orderNumber));

        // Find by exact order number or extracted from QRIS payload
        if (!str_contains($orderNumber, 'CS-') && !str_contains($orderNumber, 'POS-')) {
            preg_match('/(CS|POS)-\d+-\d+|POS\d+/i', $orderNumber, $matches);
            if (!empty($matches)) {
                $orderNumber = strtoupper($matches[0]);
            }
        }

        $order = Order::where('order_number', $orderNumber)->first();
        if (!$order) {
            return response()->json([
                'success' => false,
                'message' => 'Pesanan dengan nomor ' . $orderNumber . ' tidak ditemukan.'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'order_number' => $order->order_number,
            'customer_name' => $order->customer_name,
            'total' => (float) $order->total,
            'status' => $order->status
        ]);
    }

    public function checkOrderStatus($orderNumber)
    {
        $this->cancelExpiredOrders();
        $orderNumber = strtoupper(trim($orderNumber));
        $order = Order::where('order_number', $orderNumber)->first();
        if (!$order) {
            return response()->json(['status' => 'not_found'], 404);
        }
        return response()->json(['status' => $order->status]);
    }

    public function qrisSimulator(Request $request)
    {
        $this->cancelExpiredOrders();
        $pendingOrders = Order::where('payment_method', 'qris')
            ->where('status', 'pending')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('customer.qris-simulator', compact('pendingOrders'));
    }

    public function processSimulatorPayment(Request $request)
    {
        $request->validate([
            'order_number' => 'required|string',
        ]);

        $this->cancelExpiredOrders();

        $orderNumber = strtoupper(trim($request->input('order_number')));
        
        // Find by exact order number or extracted from QRIS payload
        if (!str_contains($orderNumber, 'CS-') && !str_contains($orderNumber, 'POS-')) {
            preg_match('/(CS|POS)-\d+-\d+|POS\d+/i', $orderNumber, $matches);
            if (!empty($matches)) {
                $orderNumber = strtoupper($matches[0]);
            }
        }

        $order = Order::where('order_number', $orderNumber)->first();
        if (!$order) {
            return response()->json([
                'success' => false,
                'message' => 'Pesanan dengan nomor ' . $orderNumber . ' tidak ditemukan.'
            ], 404);
        }

        if (in_array($order->status, ['completed', 'cancelled'])) {
            return response()->json([
                'success' => false,
                'message' => 'Pesanan sudah kedaluwarsa, selesai, atau telah dibatalkan.'
            ], 422);
        }

        if ($order->status === 'paid' || $order->status === 'processing') {
            return response()->json([
                'success' => true,
                'message' => 'Pesanan sudah lunas.'
            ]);
        }

        DB::transaction(function () use ($order) {
            $order->status = 'paid';
            $order->save();

            $payment = Payment::firstOrNew(['order_id' => $order->id]);
            $payment->method = 'qris';
            $payment->amount = $order->total;
            $payment->status = 'paid';
            if (!$payment->reference_number) {
                $payment->reference_number = 'PAY-' . random_int(100000, 999999);
            }
            $payment->paid_at = now();
            $payment->save();

            // Decrement stock for the items
            foreach ($order->items as $item) {
                if ($item->menu) {
                    $item->menu->decrement('stock', $item->quantity);
                }
            }

            ActivityLog::log('Simulator Bayar QRIS', 'Pembayaran QRIS untuk #' . $order->order_number . ' via Simulator', 'Simulator');
        });

        return response()->json([
            'success' => true,
            'message' => 'Pembayaran simulasi QRIS berhasil dilakukan.'
        ]);
    }
}
