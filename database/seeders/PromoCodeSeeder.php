<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PromoCodeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $promoCodes = [
            [
                'code' => 'HEMAT10',
                'type' => 'percentage',
                'value' => 10.00,
                'max_discount' => 20000.00, // Batas diskon maksimal Rp20.000
                'minimum_transaction' => 50000.00, // Minimum belanja Rp50.000
                'expired_at' => now()->addMonth(),
                'quota' => 100,
                'used_count' => 0,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'code' => 'DISKON20',
                'type' => 'percentage',
                'value' => 20.00,
                'max_discount' => 50000.00,
                'minimum_transaction' => 100000.00,
                'expired_at' => now()->addWeeks(2),
                'quota' => 50,
                'used_count' => 0,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'code' => 'CASHBACK15',
                'type' => 'fixed',
                'value' => 15000.00,
                'max_discount' => 15000.00,
                'minimum_transaction' => 75000.00,
                'expired_at' => now()->addDays(10),
                'quota' => 30,
                'used_count' => 0,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'code' => 'BUKAPUASA',
                'type' => 'percentage',
                'value' => 25.00,
                'max_discount' => 30000.00,
                'minimum_transaction' => 80000.00,
                'expired_at' => now()->addMonth(),
                'quota' => 200,
                'used_count' => 0,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'code' => 'WEEKEND',
                'type' => 'fixed',
                'value' => 10000.00, // Diskon tetap Rp10.000
                'max_discount' => 10000.00,
                'minimum_transaction' => 40000.00,
                'expired_at' => now()->addWeek(),
                'quota' => 75,
                'used_count' => 0,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('promo_codes')->insert($promoCodes);
    }
}
