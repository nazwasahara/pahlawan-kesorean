<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\KasirController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\AdminOwnerController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\MenuController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\ActivityLogController;
use App\Http\Controllers\PromoCodeController;
use Illuminate\Support\Facades\Route;


// Auth routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/forgot-password', [AuthController::class, 'resetPasswordDirect'])->name('password.reset.direct');
});

Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

// Admin & Owner (Shared Backoffice)
Route::middleware(['auth', 'role:owner,admin'])->prefix('admin-owner')->name('admin-owner.')->group(function () {
    Route::get('/dashboard', [AdminOwnerController::class, 'dashboard'])->name('dashboard');
    
    // User Management
    Route::get('/users', [UserController::class, 'index'])->name('users.index');
    Route::post('/users', [UserController::class, 'store'])->name('users.store');
    Route::post('/users/{id}/toggle-status', [UserController::class, 'toggleStatus'])->name('users.toggle-status');
    Route::delete('/users/{id}', [UserController::class, 'destroy'])->name('users.destroy');

    // Menu Management
    Route::get('/menus', [MenuController::class, 'index'])->name('menus.index');
    Route::middleware('role:admin')->group(function () {
        Route::post('/menus', [MenuController::class, 'store'])->name('menus.store');
        Route::post('/menus/{id}/update', [MenuController::class, 'update'])->name('menus.update');
        Route::delete('/menus/{id}', [MenuController::class, 'destroy'])->name('menus.destroy');
    });

    // Category Management
    Route::get('/categories', [CategoryController::class, 'index'])->name('categories.index');
    Route::middleware('role:admin')->group(function () {
        Route::post('/categories', [CategoryController::class, 'store'])->name('categories.store');
        Route::post('/categories/{id}/update', [CategoryController::class, 'update'])->name('categories.update');
        Route::delete('/categories/{id}', [CategoryController::class, 'destroy'])->name('categories.destroy');
    });

    // Promo Management
    Route::get('/promos', [PromoCodeController::class, 'index'])->name('promos.index');
    Route::middleware('role:admin')->group(function () {
        Route::post('/promos', [PromoCodeController::class, 'store'])->name('promos.store');
        Route::post('/promos/{id}/update', [PromoCodeController::class, 'update'])->name('promos.update');
        Route::post('/promos/{id}/toggle-status', [PromoCodeController::class, 'toggleStatus'])->name('promos.toggle-status');
        Route::delete('/promos/{id}', [PromoCodeController::class, 'destroy'])->name('promos.destroy');
    });

    // Transaction Management
    Route::get('/transactions', [TransactionController::class, 'index'])->name('transactions.index');
    Route::get('/transactions/{id}', [TransactionController::class, 'show'])->name('transactions.show');

    // Expense Management
    Route::get('/expenses', [ExpenseController::class, 'index'])->name('expenses.index');
    Route::post('/expenses', [ExpenseController::class, 'store'])->name('expenses.store');
    Route::post('/expenses/{id}/update', [ExpenseController::class, 'update'])->name('expenses.update');
    Route::delete('/expenses/{id}', [ExpenseController::class, 'destroy'])->name('expenses.destroy');

    // Report Center
    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');

    // Activity Logs
    Route::get('/logs', [ActivityLogController::class, 'index'])->name('logs.index');
});

// Kasir
Route::middleware(['auth', 'role:kasir'])->prefix('kasir')->name('kasir.')->group(function () {
    Route::get('/dashboard', [KasirController::class, 'index'])->name('dashboard');
    Route::post('/orders/checkout', [KasirController::class, 'storeOrder'])->name('orders.checkout');
    Route::get('/orders', [KasirController::class, 'orders'])->name('orders');
    Route::get('/orders/{id}', [KasirController::class, 'showOrderDetail'])->name('orders.show');
    Route::post('/orders/{id}/update', [KasirController::class, 'updateOrder'])->name('orders.update');
    Route::get('/shift', [KasirController::class, 'shift'])->name('shift');
    Route::post('/shift/start', [KasirController::class, 'startShift'])->name('shift.start');
    Route::post('/shift/end', [KasirController::class, 'endShift'])->name('shift.end');
});

// Customer (public)
Route::get('/', [CustomerController::class, 'index'])->name('customer.welcome');
Route::get('/menu', [CustomerController::class, 'menu'])->name('customer.menu');
Route::get('/location', [CustomerController::class, 'location'])->name('customer.location');
Route::get('/promos', [CustomerController::class, 'promos'])->name('customer.promos');

// Customer Cart & Checkout
Route::get('/cart', [CustomerController::class, 'cart'])->name('customer.cart');
Route::post('/cart/add', [CustomerController::class, 'addToCart'])->name('customer.cart.add');
Route::post('/cart/update', [CustomerController::class, 'updateCart'])->name('customer.cart.update');
Route::post('/cart/remove', [CustomerController::class, 'removeFromCart'])->name('customer.cart.remove');
Route::post('/cart/apply-promo', [CustomerController::class, 'applyPromo'])->name('customer.cart.apply-promo');
Route::post('/cart/remove-promo', [CustomerController::class, 'removePromo'])->name('customer.cart.remove-promo');

Route::get('/checkout', [CustomerController::class, 'checkout'])->name('customer.checkout');
Route::post('/checkout', [CustomerController::class, 'storeCheckout'])->name('customer.checkout.store');
Route::get('/checkout/success/{order_number}', [CustomerController::class, 'checkoutSuccess'])->name('customer.checkout.success');
Route::post('/checkout/cancel/{order_number}', [CustomerController::class, 'cancelOrder'])->name('customer.checkout.cancel');
Route::get('/checkout/status/{order_number}', [CustomerController::class, 'checkOrderStatus'])->name('customer.checkout.status');
Route::get('/checkout/details/{order_number}', [CustomerController::class, 'getOrderDetails'])->name('customer.checkout.details');

// QRIS Simulator
Route::get('/qris-simulator', [CustomerController::class, 'qrisSimulator'])->name('customer.qris-simulator');
Route::post('/qris-simulator/pay', [CustomerController::class, 'processSimulatorPayment'])->name('customer.qris-simulator.pay');

Route::get('/history', [CustomerController::class, 'history'])->name('customer.history');
Route::post('/history/orders', [CustomerController::class, 'getHistoryOrders'])->name('customer.history.orders');

