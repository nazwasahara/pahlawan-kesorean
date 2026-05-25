<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function payment()
    {
        return $this->hasOne(Payment::class);
    }

    public function shift()
    {
        return $this->belongsTo(Shift::class);
    }

    public static function cancelExpiredOrders()
    {
        // Cancel all pending QRIS orders created more than 5 minutes (300 seconds) ago
        $expiredOrders = self::where('payment_method', 'qris')
            ->where('status', 'pending')
            ->where('created_at', '<', now()->subMinutes(5))
            ->get();

        foreach ($expiredOrders as $order) {
            \Illuminate\Support\Facades\DB::transaction(function () use ($order) {
                $order->status = 'cancelled';
                $order->save();

                if ($order->payment) {
                    $order->payment->status = 'failed';
                    $order->payment->save();
                }

                \App\Models\ActivityLog::log('Batal transaksi', 'Batal transaksi #' . $order->order_number . ' karena QRIS expired (Auto-Cancel)', 'Sistem');
            });
        }
    }
}
