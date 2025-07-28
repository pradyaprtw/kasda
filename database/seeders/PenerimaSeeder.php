<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PenerimaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Nonaktifkan foreign key check untuk proses insert massal
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('penerima')->truncate(); // Kosongkan tabel terlebih dahulu

        $penerima = [];
        $totalPenerima = 25; // Sesuai dengan rentang 'id_penerima' di seeder SP2D

        echo "Generating " . $totalPenerima . " recipient records...\n";

        for ($i = 1; $i <= $totalPenerima; $i++) {
            $penerima[] = [
                'id' => $i, // ID eksplisit untuk referensi yang mudah
                'nama_penerima' => 'Penerima ' . Str::title(Str::random(8)),
                // Nomor rekening sekarang ada di sini
                'no_rek' => str_pad(rand(1, 9999999999), 10, '0', STR_PAD_LEFT),
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        // Insert semua data sekaligus
        DB::table('penerima')->insert($penerima);

        // Aktifkan kembali foreign key check
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        echo "Recipient seeding completed successfully!\n";
    }
}
