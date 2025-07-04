<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DummySp2dSeeder extends Seeder
{
    public function run(): void
    {
        foreach (range(1, 10) as $i) {
            DB::table('sp2d')->insert([
                'nomor_sp2d' => 'SP2D-00' . $i,
                'tanggal_sp2d' => Carbon::now()->subDays(rand(0, 10))->format('Y-m-d'),
                'jenis_sp2d' => collect(['GU', 'UP', 'LS', 'TU', 'gaji'])->random(),
                'keterangan' => 'Keterangan SP2D ke-' . $i,
                'id_instansi' => rand(1, 3), // pastikan instansi dengan id 1–3 sudah ada
                'id_penerima' => rand(1, 3), // pastikan penerima id 1–3 sudah ada
                'id_user' => rand(1, 3), // pastikan user id 1–3 sudah ada
                'no_rek' => '123456789' . rand(10, 99),
                'brutto' => $br = rand(5000000, 20000000),
                'ppn' => $ppn = rand(100000, 500000),
                'pph_21' => $pph21 = rand(100000, 500000),
                'pph_22' => $pph22 = rand(100000, 500000),
                'pph_23' => $pph23 = rand(100000, 500000),
                'pph_4' => $pph4 = rand(100000, 500000),
                'no_bg' => rand(10000000, 99999999),
                'status' => rand(0, 1),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
