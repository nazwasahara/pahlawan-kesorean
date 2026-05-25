<?php

namespace Database\Seeders;

use App\Models\Expense;
use Illuminate\Database\Seeder;

class ExpenseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $expenses = [
            ['date' => '2026-05-18', 'description' => 'beli kopi', 'amount' => 100000],
            ['date' => '2026-05-17', 'description' => 'beli kopi', 'amount' => 100000],
            ['date' => '2026-05-16', 'description' => 'beli gula', 'amount' => 150000],
            ['date' => '2026-05-15', 'description' => 'beli patty', 'amount' => 70000],
            ['date' => '2026-05-14', 'description' => 'servis ac', 'amount' => 200000],
            ['date' => '2026-05-13', 'description' => 'beli galon', 'amount' => 50000],
            ['date' => '2026-05-12', 'description' => 'beli roti', 'amount' => 80000],
            ['date' => '2026-05-11', 'description' => 'beli sayur', 'amount' => 150000],
            ['date' => '2026-05-10', 'description' => 'beli kopi', 'amount' => 100000],
            ['date' => '2026-05-09', 'description' => 'beli gula', 'amount' => 100000],
        ];

        foreach ($expenses as $expense) {
            Expense::create($expense);
        }
    }
}
