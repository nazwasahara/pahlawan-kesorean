<?php
require __DIR__ . '/../bootstrap/app.php';

$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle(
    $request = Illuminate\Http\Request::capture()
);

// Now query the DB
use App\Models\Order;
$orders = Order::orderBy('id', 'desc')->take(10)->get(['id', 'order_number', 'status', 'total', 'created_at']);
header('Content-Type: application/json');
echo json_encode($orders, JSON_PRETTY_PRINT);
