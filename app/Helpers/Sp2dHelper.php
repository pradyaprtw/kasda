<?php

namespace App\Helpers;

use Illuminate\Support\Collection;

class SP2DHelper
{
    /**
     * Menghitung total dari koleksi data SP2D.
     *
     * @param Collection $data
     * @return array
     */
    public static function calculateTotals(Collection $data): array
    {
        // [PERUBAHAN] Menambahkan kalkulasi untuk 'jumlah_potongan'
        // dan memastikan 'netto' dihitung ulang berdasarkan potongan untuk konsistensi.
        $jumlah_potongan = $data->sum(function ($item) {
            return $item->ppn + $item->pph_21 + $item->pph_22 + $item->pph_23 + $item->pph_4;
        });

        $brutto = $data->sum('brutto');

        return [
            'brutto' => $brutto,
            'ppn' => $data->sum('ppn'),
            'pph_21' => $data->sum('pph_21'),
            'pph_22' => $data->sum('pph_22'),
            'pph_23' => $data->sum('pph_23'),
            'pph_4' => $data->sum('pph_4'),
            'jumlah_potongan' => $jumlah_potongan,
            'netto' => $brutto - $jumlah_potongan, // Menghitung netto dari brutto - total potongan
            'pfk' => $data->where('jenis_sp2d', 'PFK')->sum('brutto'), // Menggunakan brutto untuk PFK
        ];
    }
}
