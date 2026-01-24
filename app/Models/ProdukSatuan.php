<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProdukSatuan extends Model
{
    protected $table = 'produk_satuan';

    protected $casts = [
    'konversi' => 'float',
    'harga_beli' => 'float',
    'harga_jual' => 'float',
    ];

    protected $fillable = [
        'produk_id',
        'nama_satuan',
        'konversi',
        'harga_beli',
        'harga_jual',
    ];

    /**
     * Relasi: Satuan milik satu produk
     */
    public function produk()
    {
        return $this->belongsTo(Produk::class, 'produk_id');
    }
}
