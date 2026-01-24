<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Produk extends Model
{
    protected $table = 'produk';

    protected $casts = [
    'stok' => 'float',
    ];

    protected $fillable = [
        'barcode',
        'nama_produk',
        'stok',
        'satuan_dasar',
        'kategori',
    ];

    /**
     * Relasi: Produk memiliki banyak satuan
     */
    public function satuan()
    {
        return $this->hasMany(ProdukSatuan::class, 'produk_id');
    }

    // Relasi ke satu satuan dasar (untuk tampilan keranjang)
    public function satuanDasar()
    {
       return $this->hasOne(ProdukSatuan::class)->where('konversi', 1);
    }

    /**
     * Hitung stok total dalam satuan dasar (akan dipakai nanti)
     */
    public function getTotalStokDasarAttribute()
    {
        $total = 0;
        foreach ($this->satuan as $satuan) {
            // stok satuan dikalikan konversi → jumlah dalam satuan dasar
            $total += $satuan->stok * $satuan->konversi;
        }
        return $total;
    }

}
