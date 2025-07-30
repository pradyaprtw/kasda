<?php

namespace App\Helpers;

use Carbon\Carbon;

class DateHelper
{
    public static function getWeekDateRange($bulan, $tahun, $minggu)
    {
        $startOfMonth = Carbon::createFromDate($tahun, $bulan, 1)->startOfMonth();
        $start = $startOfMonth->copy()->addWeeks($minggu - 1)->startOfWeek(Carbon::MONDAY);
        $end = $start->copy()->endOfWeek(Carbon::SUNDAY);

        $endOfMonth = $startOfMonth->copy()->endOfMonth();
        if ($end->greaterThan($endOfMonth)) {
            $end = $endOfMonth;
        }

        return [$start, $end];
    }
}
