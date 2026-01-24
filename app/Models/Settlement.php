<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Settlement extends Model
{
    use HasFactory;

    /**
     * PERBAIKAN 1: Tambahkan properti $fillable
     * Kolom-kolom ini diizinkan untuk diisi secara massal (mass assignment).
     */
    protected $fillable = [
        'user_id',
        'start_cash',
        'end_cash',
        'total_cash_sales',
        'total_qris_sales',
        'total_debit_sales',
        'total_sales',
        'total_discount',
        'status',
        'opened_at',
        'closed_at',
    ];

    /**
     * PERBAIKAN 2: Perbaiki struktur $casts
     * Format yang benar adalah 'nama_kolom' => 'tipe_data'.
     */
    protected $casts = [
        'opened_at' => 'datetime',
        'closed_at' => 'datetime',
        'start_cash' => 'decimal:2',
        'end_cash' => 'decimal:2',
        'total_cash_sales' => 'decimal:2',
        'total_qris_sales' => 'decimal:2',
        'total_debit_sales' => 'decimal:2',
        'total_sales' => 'decimal:2',
        'total_discount' => 'decimal:2',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function transaksis()
    {
        return $this->hasMany(Transaksi::class);
    }
}