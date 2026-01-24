<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('produk', function (Blueprint $table) {
            $table->id();
            $table->string('barcode')->unique()->nullable();
            $table->string('nama_produk');
            // Stok dalam bentuk unit dasar
            $table->decimal('stok', 10, 2)->default(0);
            $table->string('satuan_dasar'); // untuk menentukan satuan dasar saat input stock awal, misal: pcs, kg dll...
            // Status & kategori opsional
            $table->string('kategori')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('produk');
    }
};
