<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MenuSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Ambil ID kategori
        $makananUtamaId = DB::table('categories')->where('slug', 'makanan-utama')->value('id');
        $minumanId = DB::table('categories')->where('slug', 'minuman')->value('id');
        $snackId = DB::table('categories')->where('slug', 'snack')->value('id');
        $dessertId = DB::table('categories')->where('slug', 'dessert')->value('id');

        $menus = [
            // Makanan Utama
            [
                'category_id' => $makananUtamaId,
                'name' => 'Nasi Goreng Spesial',
                'price' => 35000,
                'image' => 'menus/nasi-goreng.jpg',
                'is_available' => true,
                'stock' => 50,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'category_id' => $makananUtamaId,
                'name' => 'Mie Goreng Seafood',
                'price' => 42000,
                'image' => 'menus/mie-goreng.jpg',
                'is_available' => true,
                'stock' => 40,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'category_id' => $makananUtamaId,
                'name' => 'Ayam Bakar Taliwang',
                'price' => 45000,
                'image' => 'menus/ayam-bakar.jpg',
                'is_available' => true,
                'stock' => 30,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'category_id' => $makananUtamaId,
                'name' => 'Sate Ayam Madura',
                'price' => 30000,
                'image' => 'menus/sate-ayam.jpg',
                'is_available' => true,
                'stock' => 60,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'category_id' => $makananUtamaId,
                'name' => 'Ikan Bakar Rica-Rica',
                'price' => 55000,
                'image' => 'menus/ikan-bakar.jpg',
                'is_available' => true,
                'stock' => 20,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            // Minuman
            [
                'category_id' => $minumanId,
                'name' => 'Es Teh Tarik',
                'price' => 15000,
                'image' => 'menus/es-teh.jpg',
                'is_available' => true,
                'stock' => 100,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'category_id' => $minumanId,
                'name' => 'Kopi Susu Kekinian',
                'price' => 22000,
                'image' => 'menus/kopi-susu.jpg',
                'is_available' => true,
                'stock' => 80,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'category_id' => $minumanId,
                'name' => 'Jus Alpukat',
                'price' => 25000,
                'image' => 'menus/jus-alpukat.jpg',
                'is_available' => true,
                'stock' => 50,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'category_id' => $minumanId,
                'name' => 'Lemon Squash',
                'price' => 18000,
                'image' => 'menus/lemon-squash.jpg',
                'is_available' => true,
                'stock' => 70,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'category_id' => $minumanId,
                'name' => 'Es Campur',
                'price' => 20000,
                'image' => 'menus/es-campur.jpg',
                'is_available' => true,
                'stock' => 40,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            // Snack
            [
                'category_id' => $snackId,
                'name' => 'French Fries',
                'price' => 18000,
                'image' => 'menus/french-fries.jpg',
                'is_available' => true,
                'stock' => 80,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'category_id' => $snackId,
                'name' => 'Onion Rings',
                'price' => 15000,
                'image' => 'menus/onion-rings.jpg',
                'is_available' => true,
                'stock' => 60,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'category_id' => $snackId,
                'name' => 'Chicken Wings',
                'price' => 28000,
                'image' => 'menus/chicken-wings.jpg',
                'is_available' => true,
                'stock' => 40,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            // Dessert
            [
                'category_id' => $dessertId,
                'name' => 'Es Krim Coklat',
                'price' => 20000,
                'image' => 'menus/es-krim-coklat.jpg',
                'is_available' => true,
                'stock' => 50,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'category_id' => $dessertId,
                'name' => 'Puding Caramel',
                'price' => 18000,
                'image' => 'menus/puding-caramel.jpg',
                'is_available' => true,
                'stock' => 30,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'category_id' => $dessertId,
                'name' => 'Pancake Maple',
                'price' => 25000,
                'image' => 'menus/pancake-maple.jpg',
                'is_available' => true,
                'stock' => 25,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('menus')->insert($menus);
    }
}
