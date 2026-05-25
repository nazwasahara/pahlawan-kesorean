<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Membuat user dengan role spesifik sebagai contoh data master
        User::factory()->owner()->create([
            'name' => 'Owner Utama',
            'email' => 'owner@example.com',
        ]);

        User::factory()->admin()->create([
            'name' => 'Admin Utama',
            'email' => 'admin@example.com',
        ]);

        User::factory()->kasir()->create([
            'name' => 'Kasir Utama',
            'email' => 'kasir@example.com',
        ]);

        // Membuat 10 user random (role akan random: owner, admin, atau kasir)
        User::factory(10)->create();
    }
}
