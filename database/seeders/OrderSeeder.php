<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class OrderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $menus = DB::table('menus')->get();

        if ($menus->isEmpty()) {
            $this->command->warn('Tidak ada menu ditemukan. Jalankan MenuSeeder terlebih dahulu.');
            return;
        }

        $shifts = DB::table('shifts')->orderBy('id', 'asc')->get();

        $indonesianNames = [
            'Budi Santoso', 'Ani Wulandari', 'Citra Lestari', 'Dedi Kurniawan', 'Eka Putri',
            'Fajar Nugroho', 'Gita Permata', 'Hadi Wijaya', 'Indah Rahayu', 'Joko Susilo',
            'Kartika Sari', 'Lukman Hakim', 'Mega Utami', 'Novianto', 'Olivia',
            'Pratama', 'Qori', 'Rian', 'Siti', 'Taufik'
        ];

        // Target bulanan untuk membentuk pola lekukan grafik yang indah (dalam Rp)
        $targetRevenues = [
            1 => 8000000,   // Jan
            2 => 6500000,   // Feb
            3 => 5000000,   // Mar (Turun)
            4 => 7500000,   // Apr (Naik)
            5 => 6500000,   // Mei
            6 => 9500000,   // Jun (Puncak Liburan)
            7 => 8500000,   // Jul
            8 => 7000000,   // Ags
            9 => 6000000,   // Sep
            10 => 6200000,  // Okt
            11 => 8800000,  // Nov (Naik)
            12 => 9800000   // Des (Puncak Akhir Tahun)
        ];

        // Generate data selama 18 bulan ke belakang untuk menjamin grafik selalu berlekuk indah
        for ($i = 17; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $monthNum = $date->month;
            $year = $date->year;

            // Beri sedikit variasi acak agar data tampak organik (+/- 10%)
            $baseTarget = $targetRevenues[$monthNum] ?? 6000000;
            $variance = rand(-500000, 500000);
            $monthlyTarget = $baseTarget + $variance;

            $currentRevenue = 0;

            while ($currentRevenue < $monthlyTarget) {
                // Tanggal acak di bulan bersangkutan
                $day = rand(1, min(28, $date->daysInMonth));
                $hour = rand(9, 21);
                $minute = rand(0, 59);
                $second = rand(0, 59);
                
                $orderDate = clone $date;
                $orderDate->setDate($year, $monthNum, $day)->setTime($hour, $minute, $second);

                $customerName = $indonesianNames[array_rand($indonesianNames)];
                $isDineIn = rand(0, 1) === 1;
                $orderType = $isDineIn ? 'dine_in' : 'take_away';
                $tableNumber = $isDineIn ? 'A' . rand(1, 10) : null;
                $paymentMethod = ['cash', 'qris', 'debit'][rand(0, 2)];
                $discount = rand(0, 4) === 0 ? [5000, 10000, 15000][rand(0, 2)] : 0;

                // Insert order dasar
                $orderId = DB::table('orders')->insertGetId([
                    'order_number' => 'ORD-' . strtoupper(Str::random(8)),
                    'session_id' => Str::uuid(),
                    'shift_id' => $shifts->isEmpty() ? null : $shifts->random()->id,
                    'customer_name' => $customerName,
                    'table_number' => $tableNumber,
                    'order_type' => $orderType,
                    'payment_method' => $paymentMethod,
                    'subtotal' => 0,
                    'discount' => $discount,
                    'total' => 0,
                    'status' => 'completed', // Completed/Paid agar masuk perhitungan grafik
                    'created_at' => $orderDate,
                    'updated_at' => $orderDate,
                ]);

                // Pilih 1 - 3 menu acak
                $orderMenus = $menus->random(rand(1, min(3, $menus->count())));
                $subtotal = 0;

                foreach ($orderMenus as $menu) {
                    $quantity = rand(1, 3);
                    $itemSubtotal = $menu->price * $quantity;
                    $subtotal += $itemSubtotal;

                    DB::table('order_items')->insert([
                        'order_id' => $orderId,
                        'menu_id' => $menu->id,
                        'menu_name' => $menu->name,
                        'price' => $menu->price,
                        'quantity' => $quantity,
                        'note' => rand(0, 5) === 0 ? 'Pedas sekali' : null,
                        'subtotal' => $itemSubtotal,
                        'created_at' => $orderDate,
                        'updated_at' => $orderDate,
                    ]);
                }

                $total = max(0, $subtotal - $discount);

                // Update total sesungguhnya
                DB::table('orders')->where('id', $orderId)->update([
                    'subtotal' => $subtotal,
                    'total' => $total
                ]);

                $currentRevenue += $total;
            }
        }

        // Tambahkan beberapa transaksi hari ini dengan status beragam untuk keperluan UI dashboard
        $statuses = ['pending', 'processing', 'cancelled', 'paid'];
        for ($k = 0; $k < 5; $k++) {
            $customerName = $indonesianNames[array_rand($indonesianNames)];
            $status = $statuses[$k % count($statuses)];
            $isDineIn = rand(0, 1) === 1;
            
            $orderId = DB::table('orders')->insertGetId([
                'order_number' => 'ORD-TODAY' . $k,
                'session_id' => Str::uuid(),
                'shift_id' => $shifts->isEmpty() ? null : $shifts->random()->id,
                'customer_name' => $customerName,
                'table_number' => $isDineIn ? 'B' . rand(1, 5) : null,
                'order_type' => $isDineIn ? 'dine_in' : 'take_away',
                'payment_method' => ['cash', 'qris', 'debit'][rand(0, 2)],
                'subtotal' => 0,
                'discount' => 0,
                'total' => 0,
                'status' => $status,
                'created_at' => now()->subMinutes(rand(10, 180)),
                'updated_at' => now(),
            ]);

            $orderMenus = $menus->random(rand(1, 2));
            $subtotal = 0;
            foreach ($orderMenus as $menu) {
                $quantity = rand(1, 2);
                $itemSubtotal = $menu->price * $quantity;
                $subtotal += $itemSubtotal;

                DB::table('order_items')->insert([
                    'order_id' => $orderId,
                    'menu_id' => $menu->id,
                    'menu_name' => $menu->name,
                    'price' => $menu->price,
                    'quantity' => $quantity,
                    'note' => null,
                    'subtotal' => $itemSubtotal,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            DB::table('orders')->where('id', $orderId)->update([
                'subtotal' => $subtotal,
                'total' => $subtotal
            ]);
        }
    }
}
