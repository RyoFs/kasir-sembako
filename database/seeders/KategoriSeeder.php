<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Kategori;

class KategoriSeeder extends Seeder
{
    public function run(): void
    {
        $Kategori = [
            [
                'nama' => 'Sembako',
                'deskripsi' => 'Bahan pokok kebutuhan sehari-hari seperti beras, gula, minyak, garam, dll.',
            ],
            [
                'nama' => 'Minuman',
                'deskripsi' => 'Aneka jenis minuman seperti air mineral, kopi, teh, dan minuman ringan.',
            ],
            [
                'nama' => 'Makanan Ringan',
                'deskripsi' => 'Snack, biskuit, keripik, dan makanan ringan lainnya.',
            ],
            [
                'nama' => 'Kebutuhan Rumah Tangga',
                'deskripsi' => 'Sabun, detergen, pembersih, dan perlengkapan rumah tangga lainnya.',
            ],
            [
                'nama' => 'Perawatan Tubuh',
                'deskripsi' => 'Produk kebersihan dan perawatan tubuh seperti sampo, sabun, dan pasta gigi.',
            ],
            [
                'nama' => 'Lain-lain',
                'deskripsi' => 'Kategori tambahan untuk produk umum lainnya.',
            ],
            [
                'nama' => 'ROKOK',
                'deskripsi' => 'Tembakau.',
            ],
        ];

        foreach ($Kategori as $cat) {
            Kategori::updateOrCreate(['nama' => $cat['nama']], $cat);
        }
    }
}
