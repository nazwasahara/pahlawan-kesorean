<?php

namespace Database\Seeders;

use App\Models\ActivityLog;
use Illuminate\Database\Seeder;

class ActivityLogSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $logs = [
            [
                'user_identity' => 'Admin',
                'action' => 'Catat Pengeluaran',
                'description' => 'Keluar Rp100.000 untuk beli kopi',
                'created_at' => '2026-05-18 15:15:08',
            ],
            [
                'user_identity' => 'Admin',
                'action' => 'Catat Pengeluaran',
                'description' => 'Keluar Rp100.000 untuk beli kopi',
                'created_at' => '2026-05-18 15:15:06',
            ],
            [
                'user_identity' => 'Admin',
                'action' => 'Catat Pengeluaran',
                'description' => 'Keluar Rp150.000 untuk beli gula',
                'created_at' => '2026-05-18 15:15:04',
            ],
            [
                'user_identity' => 'Admin',
                'action' => 'Edit Menu',
                'description' => 'Stok burger: 2 -> 20',
                'created_at' => '2026-05-18 15:14:04',
            ],
            [
                'user_identity' => 'Owner',
                'action' => 'Tambah Pengguna',
                'description' => 'Tambah kasir2',
                'created_at' => '2026-05-18 15:13:04',
            ],
            [
                'user_identity' => 'Admin',
                'action' => 'Edit Kategori',
                'description' => 'Jumlah menu kopi: 14 -> 15',
                'created_at' => '2026-05-18 15:12:04',
            ],
            [
                'user_identity' => 'Admin',
                'action' => 'Hapus Menu',
                'description' => 'Hapus Dimsum Mentai',
                'created_at' => '2026-05-18 15:11:04',
            ],
            [
                'user_identity' => 'Owner',
                'action' => 'Hapus Pengguna',
                'description' => 'Hapus kasir3',
                'created_at' => '2026-05-18 15:10:04',
            ],
            [
                'user_identity' => 'Owner',
                'action' => 'Nonaktifkan Pengguna',
                'description' => 'Nonaktif kasir3',
                'created_at' => '2026-05-18 15:09:04',
            ],
            [
                'user_identity' => 'Admin',
                'action' => 'Hapus pengeluaran',
                'description' => 'Hapus keluar Rp2.000 beli roti',
                'created_at' => '2026-05-18 15:08:04',
            ],
        ];

        foreach ($logs as $log) {
            ActivityLog::create([
                'user_identity' => $log['user_identity'],
                'action' => $log['action'],
                'description' => $log['description'],
                'created_at' => $log['created_at'],
                'updated_at' => $log['created_at'],
            ]);
        }
    }
}
