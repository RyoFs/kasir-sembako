<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaksi extends Model
{
    use HasFactory;

    // Nama tabel
    protected $table = 'transaksi';

    // Primary key bukan auto-increment
    protected $primaryKey = 'id';
    public $incrementing = false;

    // Tipe data primary key
    protected $keyType = 'string';

    // Kolom yang bisa diisi massal
    protected $fillable = [
        'id',
        'tanggal',
        'total',
        'diskon',
        'bayar',
        'kembali',
        'metode_pembayaran',
        'status',
        'user_id',
        'settlement_id', 
    ];

    protected $casts = [
        'tanggal' => 'datetime', // otomatis jadi Carbon
    ];

    // Relasi ke User
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    
    // (Opsional) relasi ke detail transaksi
    public function details()
    {
        return $this->hasMany(TransaksiDetail::class, 'transaksi_id', 'id');
    }

    public function settlement()
    {
        return $this->belongsTo(Settlement::class);
    }

    // Accessor untuk format tanggal
    public function getFormattedTanggalAttribute()
    {
        return \Carbon\Carbon::parse($this->tanggal)->format('d M Y');
    }

    // Accessor untuk format total
    public function getFormattedTotalAttribute()
    {
        return 'Rp ' . number_format($this->total, 0, ',', '.');
    }
}
