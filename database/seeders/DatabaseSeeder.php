<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Disable foreign key checks for truncate
        Schema::disableForeignKeyConstraints();

        // Truncate all seeded tables to ensure fresh data
        DB::table('payments')->truncate();
        DB::table('order_items')->truncate();
        DB::table('orders')->truncate();
        DB::table('shifts')->truncate();
        DB::table('cart_items')->truncate();
        DB::table('carts')->truncate();
        DB::table('promo_codes')->truncate();
        DB::table('menus')->truncate();
        DB::table('categories')->truncate();
        DB::table('users')->truncate();
        DB::table('expenses')->truncate();
        DB::table('activity_logs')->truncate();

        // Re-enable foreign key checks
        Schema::enableForeignKeyConstraints();

        $this->call([
            UserSeeder::class,
            CategorySeeder::class,
            MenuSeeder::class,
            PromoCodeSeeder::class,
            // ShiftSeeder::class,
            // OrderSeeder::class,
            // PaymentSeeder::class,
            // ExpenseSeeder::class,
            // ActivityLogSeeder::class,
        ]);
    }
}
