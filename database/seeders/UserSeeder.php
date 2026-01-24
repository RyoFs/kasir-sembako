<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;

class UserSeeder extends Seeder
{
    public function run()
    {
        // Admin
        User::create([
            'username' => 'admin',
            'nama' => 'Administrator',
            'pin' => '1234',
            'role' => 'admin',
        ]);

        // Kasir
        User::create([
            'username' => 'kasir1',
            'nama'  => 'Ryo Fahrezi Sempurna',
            'pin' => '1111',
            'role' => 'kasir',
        ]);

        User::create([
            'username' => 'kasir2',
            'nama'  => 'Ryo Fahrezi Sempurna',
            'pin' => '2222',
            'role' => 'kasir',
        ]);
    }
}
