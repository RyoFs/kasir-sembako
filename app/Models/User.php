<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;

    protected $fillable = [
        'username',
        'nama',
        'pin',
        'role',
    ];

    protected $hidden = ['pin'];

    public function transaksi()
    {
        return $this->hasMany(Transaksi::class, 'user_id');
    }

    public function settlements()
    {
        return $this->hasMany(Settlement::class, 'user_id');
    }
}
