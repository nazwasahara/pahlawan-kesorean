<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PaymentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $orders = DB::table('orders')->get();

        if ($orders->isEmpty()) {
            $this->command->warn('Tidak ada order ditemukan. Jalankan OrderSeeder terlebih dahulu.');
            return;
        }

        foreach ($orders as $order) {
            // Hanya buat payment untuk order yang sudah paid, processing, atau completed
            if (in_array($order->status, ['paid', 'processing', 'completed'])) {
                $status = $order->status === 'completed' || $order->status === 'processing' ? 'paid' : 'pending';
                
                DB::table('payments')->insert([
                    'order_id' => $order->id,
                    'method' => $order->payment_method,
                    'amount' => $order->total,
                    'status' => $status,
                    'reference_number' => $order->payment_method !== 'cash' ? 'REF-' . strtoupper(\Illuminate\Support\Str::random(10)) : null,
                    'paid_at' => $status === 'paid' ? $order->created_at : null,
                    'created_at' => $order->created_at,
                    'updated_at' => $order->updated_at,
                ]);
            }
        }
    }
}
