<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;

class DummySp2dSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Menonaktifkan batas waktu eksekusi dan memori untuk proses yang berjalan lama.
        ini_set('max_execution_time', 0); // 0 = tidak ada batas waktu
        ini_set('memory_limit', '-1');   // -1 = tidak ada batas memori

        // Nonaktifkan foreign key check untuk mempercepat proses insert massal
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        
        // Kosongkan tabel terlebih dahulu untuk menghindari data duplikat
        echo "Truncating sp2d table...\n";
        DB::table('sp2d')->truncate();

        // --- KONFIGURASI BARU ---
        $years = range(2019, 2024); // Rentang tahun yang akan diisi data
        $months = range(1, 12);     // Rentang bulan
        $recordsPerMonth = 3500;   // Jumlah data per bulan
        $chunkSize = 1000;         // Ukuran batch untuk sekali insert ke database
        $jenisSp2dOptions = ['GU', 'UP', 'LS', 'TU', 'gaji', 'PFK'];
        $data = [];
        $totalInserted = 0;
        $recordCounter = 1; // Counter unik untuk semua record

        // Loop untuk setiap tahun
        foreach ($years as $year) {
            // Loop untuk setiap bulan
            foreach ($months as $month) {
                echo "--------------------------------------------------\n";
                echo "Generating " . number_format($recordsPerMonth) . " records for month: $month-$year\n";
                echo "--------------------------------------------------\n";

                // Tentukan rentang tanggal untuk bulan yang sedang diproses
                $startDate = Carbon::create($year, $month, 1)->startOfMonth();
                $endDate = $startDate->copy()->endOfMonth();
                $totalSecondsInMonth = $startDate->diffInSeconds($endDate);

                // Loop untuk membuat data sebanyak $recordsPerMonth
                for ($i = 1; $i <= $recordsPerMonth; $i++) {
                    
                    // Membuat timestamp acak dalam rentang bulan yang ditentukan
                    $createdAt = $startDate->copy()->addSeconds(rand(0, $totalSecondsInMonth));

                    // Tanggal SP2D dibuat beberapa hari sebelum tanggal created_at
                    $tanggalSp2d = $createdAt->copy()->subDays(rand(1, 10))->toDateString();

                    // Menghasilkan nilai brutto dan pajak secara acak
                    $brutto = rand(500000, 50000000);
                    $ppn = rand(0, 1) ? $brutto * 0.11 : 0; // PPN 11% atau 0
                    $pph_21 = rand(0, 1) ? $brutto * 0.05 : 0;
                    $pph_22 = rand(0, 1) ? $brutto * 0.015 : 0;
                    $pph_23 = rand(0, 1) ? $brutto * 0.02 : 0;
                    $pph_4 = rand(0, 1) ? $brutto * 0.10 : 0;

                    $data[] = [
                        // Info SP2D
                        'nomor_sp2d' => 'SP2D/' . $year . '/' . $recordCounter, // Dijamin unik
                        'tanggal_sp2d' => $tanggalSp2d,
                        'jenis_sp2d' => $jenisSp2dOptions[array_rand($jenisSp2dOptions)],
                        'keterangan' => 'Pembayaran untuk kegiatan ' . Str::random(10),

                        // Relasi
                        'id_instansi' => rand(1, 30),
                        'id_penerima' => rand(1, 25),
                        'id_user' => 1,

                        // Keuangan
                        'brutto' => $brutto,
                        'ppn' => $ppn,
                        'pph_21' => $pph_21,
                        'pph_22' => $pph_22,
                        'pph_23' => $pph_23,
                        'pph_4' => $pph_4,
                        'no_bg' => 'BG' . str_pad($recordCounter, 8, '0', STR_PAD_LEFT), // Dijamin unik
                        'waktu_sesuai' => null, // Sesuai permintaan, dibuat null

                        // Timestamps
                        'created_at' => $createdAt,
                        'updated_at' => $createdAt,
                    ];

                    // Jika batch sudah mencapai ukuran chunk, insert ke database
                    if (count($data) === $chunkSize) {
                        DB::table('sp2d')->insert($data);
                        $data = []; // Kosongkan array untuk batch berikutnya
                        $totalInserted += $chunkSize;
                        echo "Inserted " . number_format($chunkSize) . " records. Total so far: " . number_format($totalInserted) . "\n";
                    }
                    
                    $recordCounter++;
                }
            }
        }

        // Insert sisa data yang belum masuk dalam batch terakhir
        if (!empty($data)) {
            $finalCount = count($data);
            DB::table('sp2d')->insert($data);
            $totalInserted += $finalCount;
            echo "Inserted remaining " . number_format($finalCount) . " records.\n";
        }

        // Aktifkan kembali foreign key check
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        echo "\n==================================================\n";
        echo "Seeding completed successfully!\n";
        echo "Total records inserted: " . number_format($totalInserted) . "\n";
        echo "==================================================\n";
    }
}
