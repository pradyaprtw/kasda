<?php

namespace App\Helpers;

use App\Models\SP2D;
use Carbon\Carbon;

class TotalsHelper
{
    public static function getTotalsForTwoDays(Carbon $date): array
    {
        $today = $date;
        $yesterday = $date->copy()->subDay();

        $todayData = SP2D::whereDate('created_at', $today)->get();
        $yesterdayData = SP2D::whereDate('created_at', $yesterday)->get();
        $twoDaysData = $todayData->merge($yesterdayData); // gabung 27 + 28

        return [
            'today'     => self::calculate($todayData),
            'yesterday' => self::calculate($yesterdayData),
            'combined'  => self::calculate($twoDaysData),
        ];
    }

    protected static function calculate($collection): array
    {
        return [
            'brutto'          => $collection->sum('brutto'),
            'ppn'             => $collection->sum('ppn'),
            'pph_21'          => $collection->sum('pph_21'),
            'pph_22'          => $collection->sum('pph_22'),
            'pph_23'          => $collection->sum('pph_23'),
            'pph_4'           => $collection->sum('pph_4'),
            'jumlah_potongan' => $collection->sum(function ($item) {
                return $item->ppn + $item->pph_21 + $item->pph_22 + $item->pph_23 + $item->pph_4;
            }),
            'netto' => $collection->sum(function ($item) {
                $pot = $item->ppn + $item->pph_21 + $item->pph_22 + $item->pph_23 + $item->pph_4;
                return $item->brutto - $pot;
            }),
            'pfk' => $collection->filter(function ($item) {
                return strtolower($item->jenis_sp2d) === 'pfk';
            })->sum('brutto'),
        ];
    }

    /**
     * Helper untuk mengambil dan menjumlahkan total untuk satu hari spesifik.
     * [BARU] Menggantikan getTotalsForDateRange untuk menyederhanakan logika.
     *
     * @param Carbon $date
     * @param array $jenisSp2d
     * @return array
     */
    private function getTotalsForDay(Carbon $date, array $jenisSp2d): array
    {
        $totals = Sp2d::whereIn('jenis_sp2d', $jenisSp2d)
                     ->whereDate('created_at', $date) // Kueri hanya untuk satu tanggal
                     ->selectRaw(
                         "SUM(brutto) as brutto, " .
                         "SUM(pph_21) as pph_21, " .
                         "SUM(iuran_wajib) as iuran_wajib, " .
                         "SUM(iuran_wajib_2) as iuran_wajib_2, " .
                         "SUM(netto) as netto"
                     )->first();

        // Jika tidak ada data, kembalikan array dengan nilai nol agar penjumlahan tidak error.
        if (!$totals || is_null($totals->brutto)) {
            return [
                'brutto'          => 0.0,
                'pph_21'          => 0.0,
                'iuran_wajib'     => 0.0,
                'iuran_wajib_2'   => 0.0,
                'jumlah_potongan' => 0.0,
                'netto'           => 0.0,
            ];
        }

        $jumlah_potongan = $totals->pph_21 + $totals->iuran_wajib + $totals->iuran_wajib_2;

        // Kembalikan hasil dengan tipe data yang benar.
        return [
            'brutto'          => (float)$totals->brutto,
            'pph_21'          => (float)$totals->pph_21,
            'iuran_wajib'     => (float)$totals->iuran_wajib,
            'iuran_wajib_2'   => (float)$totals->iuran_wajib_2,
            'jumlah_potongan' => (float)$jumlah_potongan,
            'netto'           => (float)$totals->netto,
        ];
    }
}
