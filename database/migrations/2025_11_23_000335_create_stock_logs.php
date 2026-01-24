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

        Schema::create('stock_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('produk_id');
            $table->unsignedBigInteger('produk_satuan_id')->nullable();
            $table->foreign('produk_satuan_id')->references('id')->on('produk_satuan')->onDelete('set null');
            $table->enum('type', ['in', 'out']); // stock_in atau stock_out
            $table->decimal('qty', 10, 2); // jumlah dalam satuan dasar
            $table->decimal('qty_dasar', 10, 2); // jumlah dalam satuan tampilannya
            $table->decimal('harga_beli', 15, 2)->nullable(); // untuk stock_in
            $table->string('note')->nullable();
            $table->timestamps();
            $table->foreign('produk_id')->references('id')->on('produk')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
