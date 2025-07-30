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
}
