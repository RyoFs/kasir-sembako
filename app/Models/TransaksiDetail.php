<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransaksiDetail extends Model
{
    use HasFactory;

    protected $table = 'transaksi_detail';
    protected $fillable = ['transaksi_id', 'produk_id','satuan_id','qty', 'harga', 'subtotal'];

    public function transaksi()
    {
        return $this->belongsTo(Transaksi::class, 'transaksi_id', 'id');
    }

    public function produk()
    {
        return $this->belongsTo(Produk::class, 'produk_id', 'id');
    }

    public function satuan()
    {
        return $this->belongsTo(ProdukSatuan::class, 'satuan_id', 'id');
    }
}
