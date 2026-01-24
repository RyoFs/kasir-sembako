<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('transaksi_detail', function (Blueprint $table) {
            $table->id();
            $table->string('transaksi_id'); // relasi ke transaksi (string)
            $table->foreign('transaksi_id')->references('id')->on('transaksi')->onDelete('cascade');
            $table->foreignId('produk_id')->constrained('produk')->onDelete('cascade');
            $table->unsignedBigInteger('satuan_id')->nullable();
            // Tambahkan foreign key ke tabel produk_satuan jika ingin enforce relasi
            $table->foreign('satuan_id')->references('id')->on('produk_satuan')->onDelete('set null');
            $table->integer('qty');
            $table->decimal('harga', 15, 2);
            $table->decimal('subtotal', 15, 2);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transaksi_detail');
    }
};
