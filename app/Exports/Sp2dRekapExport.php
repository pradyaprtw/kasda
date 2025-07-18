<?php

namespace App\Exports;

use App\Models\SP2D;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class Sp2dRekapExport implements WithMultipleSheets
{
    protected $start;
    protected $end;

    public function __construct($start, $end)
    {
        $this->start = $start;
        $this->end = $end;
    }

    public function sheets(): array
    {
        $sheets = [];
        $dates = SP2D::whereBetween('created_at', [$this->start, $this->end])
            ->selectRaw('DATE(created_at) as date')
            ->distinct()
            ->orderBy('date', 'asc')
            ->pluck('date');

        // Buat sheet untuk setiap tanggal yang ditemukan
        if ($dates->isEmpty()) {
            $startDate = Carbon::parse($this->start);
            $endDate = Carbon::parse($this->end);

            // Loop dari start date sampai end date
            for ($date = $startDate->copy(); $date->lte($endDate); $date->addDay()) {
                $sheets[] = new Sp2dExport($date->format('d-m-Y'));
            }
        } else {
            // Jika ada data, buat sheet berdasarkan tanggal yang ada
            foreach ($dates as $date) {
                $sheets[] = new Sp2dExport($date);
            }
        }

        return $sheets;
    }
}
