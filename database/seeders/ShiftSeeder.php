<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ShiftSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = DB::table('users')->where('role', 'kasir')->get();

        if ($users->isEmpty()) {
            return;
        }

        $shifts = [
            [
                'user_id' => $users->first()->id,
                'tanggal' => now()->subDays(2)->toDateString(),
                'jam_mulai' => '08:00:00',
                'jam_selesai' => '16:00:00',
                'modal_awal' => 500000,
                'total_penjualan' => 77000,
                'total_pengeluaran' => 0,
                'saldo_akhir' => 577000,
                'catatan' => 'Shift lancar',
                'status' => 'closed',
                'created_at' => now()->subDays(2),
                'updated_at' => now()->subDays(2),
            ],
            [
                'user_id' => $users->first()->id,
                'tanggal' => now()->subDays(1)->toDateString(),
                'jam_mulai' => '08:00:00',
                'jam_selesai' => '16:00:00',
                'modal_awal' => 500000,
                'total_penjualan' => 95000,
                'total_pengeluaran' => 20000,
                'saldo_akhir' => 575000,
                'catatan' => 'Beli plastik kemasan',
                'status' => 'closed',
                'created_at' => now()->subDays(1),
                'updated_at' => now()->subDays(1),
            ],
            [
                'user_id' => $users->first()->id,
                'tanggal' => now()->toDateString(),
                'jam_mulai' => '08:00:00',
                'jam_selesai' => null,
                'modal_awal' => 500000,
                'total_penjualan' => 312000,
                'total_pengeluaran' => 0,
                'saldo_akhir' => 812000,
                'catatan' => 'Shift sedang berjalan',
                'status' => 'open',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        foreach ($shifts as $shift) {
            DB::table('shifts')->insert($shift);
        }
    }
}
