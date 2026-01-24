<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('settlements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade'); // Kasir yang buka/tutup
            $table->decimal('start_cash', 15, 2)->default(0); // Modal awal
            $table->decimal('end_cash', 15, 2)->nullable(); // Uang di laci saat tutup
            $table->decimal('total_cash_sales', 15, 2)->default(0); // Total penjualan tunai
            $table->decimal('total_qris_sales', 15, 2)->default(0); // Total penjualan QRIS
            $table->decimal('total_debit_sales', 15, 2)->default(0); // Total penjualan Debit
            $table->decimal('total_sales', 15, 2)->default(0); // Grand total
            $table->decimal('total_discount', 15, 2)->default(0); // Total diskon
            $table->enum('status', ['open', 'closed'])->default('open');
            $table->timestamp('opened_at'); // Waktu buka shift
            $table->timestamp('closed_at')->nullable(); // Waktu tutup shift
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('settlements');
    }
};