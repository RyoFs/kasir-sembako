<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StockLog extends Model
{
    protected $table = 'stock_logs';

    protected $fillable = [
        'produk_id',
        'produk_satuan_id',
        'type',
        'qty',
        'qty_dasar',
        'harga_beli',
        'note'
    ];

    public function produk()
    {
        return $this->belongsTo(Produk::class, 'produk_id');
    }

    public function satuan()
    {
        return $this->belongsTo(ProdukSatuan::class, 'produk_satuan_id');
    }

}
