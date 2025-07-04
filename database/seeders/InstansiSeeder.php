<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class InstansiSeeder extends Seeder
{
    public function run(): void
    {
        $instansi = [
            'Dinas Pendidikan',
            'Dinas Kesehatan',
            'Dinas PU',
        ];

        foreach ($instansi as $nama) {
            DB::table('instansi')->insert([
                'nama_instansi' => $nama,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
