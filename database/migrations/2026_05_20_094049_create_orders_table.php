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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_number')->unique();

            $table->string('session_id')->index();
            $table->string('customer_name');
            $table->string('table_number')->nullable();

            $table->enum('order_type', [
                'dine_in',
                'take_away'
            ]);

            $table->enum('payment_method', [
                'cash',
                'qris',
                'debit'
            ]);

            $table->decimal('subtotal', 12, 2);

            $table->decimal('discount', 12, 2)->default(0);

            $table->decimal('total', 12, 2);

            $table->enum('status', [
                'pending',
                'paid',
                'processing',
                'completed',
                'cancelled'
            ])->default('pending');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
