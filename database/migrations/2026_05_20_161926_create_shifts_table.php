<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('shifts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();

            $table->date('tanggal');
            $table->time('jam_mulai');
            $table->time('jam_selesai')->nullable();

            $table->decimal('modal_awal', 12, 2)->default(0);
            $table->decimal('total_penjualan', 12, 2)->default(0);
            $table->decimal('total_pengeluaran', 12, 2)->default(0);
            $table->decimal('saldo_akhir', 12, 2)->default(0);

            $table->text('catatan')->nullable();

            $table->enum('status', ['open', 'closed'])->default('open');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shifts');
    }
};
