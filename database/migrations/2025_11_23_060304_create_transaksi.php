<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('transaksi', function (Blueprint $table) {
            $table->string('id')->primary(); // contoh: TRX20250108001
            $table->dateTime('tanggal');
            $table->decimal('total', 15, 2)->default(0);
            $table->decimal('diskon', 15, 2)->default(0); // tambahan kolom diskon
            $table->decimal('bayar', 15, 2)->nullable();
            $table->decimal('kembali', 15, 2)->nullable();
            $table->string('status')->default('lunas'); // lunas / pending
            $table->enum('metode_pembayaran', ['cash', 'qris', 'debit'])->default('cash');// metode pembayaran: cash, qris, debit
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('settlement_id')->nullable()->constrained('settlements')->onDelete('set null');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transaksi');
    }
};
