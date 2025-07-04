<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PenerimaSeeder extends Seeder
{
    public function run(): void
    {
        $penerima = [
            ['nama_penerima' => 'Budi Santoso/CV Makmur'],
            ['nama_penerima' => 'Siti AminahCV Rejeki'],
            ['nama_penerima' => 'Dwi Herlambang/CV Pribadi'],
        ];

        foreach ($penerima as $p) {
            DB::table('penerima')->insert([
                'nama_penerima' => $p['nama_penerima'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
