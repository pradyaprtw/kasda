<?php

namespace App\Exports;

use App\Helpers\Sp2dHelper;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class Sp2dTahunanExport implements WithMultipleSheets
{
    protected $dataByMonth;
    protected $tahun;

    public function __construct($dataByMonth, $tahun)
    {
        $this->dataByMonth = $dataByMonth;
        $this->tahun = $tahun;
    }

    public function sheets(): array
    {
        $sheets = [];
        
        // Cache untuk menyimpan perhitungan total yang sudah dilakukan
        $totalsCache = [];
        
        // Urutkan bulan dari 01 sampai 12
        $sortedMonths = $this->dataByMonth->sortKeys();

        foreach ($sortedMonths as $monthNum => $data) {
            if ($data->isEmpty()) {
                continue; // Lewati bulan yang tidak ada data
            }

            $currentMonthData = $data;
            
            // Dapatkan data bulan sebelumnya
            $previousMonthNum = (int)$monthNum - 1;
            $previousMonthKey = $previousMonthNum > 0 ? sprintf('%02d', $previousMonthNum) : '00';
            $previousMonthData = $this->dataByMonth->get($previousMonthKey, collect());

            // [OPTIMASI] Cache perhitungan total untuk menghindari perhitungan berulang
            if (!isset($totalsCache[$monthNum])) {
                $totalsCache[$monthNum] = Sp2dHelper::calculateTotals($currentMonthData);
            }
            
            if (!isset($totalsCache[$previousMonthKey]) && !$previousMonthData->isEmpty()) {
                $totalsCache[$previousMonthKey] = Sp2dHelper::calculateTotals($previousMonthData);
            }

            // Gabungkan data untuk total kumulatif - hanya lakukan jika diperlukan
            $cumulativeKey = $previousMonthKey . '_' . $monthNum;
            if (!isset($totalsCache[$cumulativeKey])) {
                $cumulativeData = $currentMonthData->merge($previousMonthData);
                $totalsCache[$cumulativeKey] = Sp2dHelper::calculateTotals($cumulativeData);
            }

            $totalsForSheet = [
                'current'    => $totalsCache[$monthNum],
                'previous'   => $totalsCache[$previousMonthKey] ?? [],
                'cumulative' => $totalsCache[$cumulativeKey],
            ];
            
            // Buat sheet baru dengan data dan total yang sudah benar
            $sheets[] = new Sp2dRekapExport(
                $currentMonthData,
                (int)$monthNum,
                $this->tahun,
                $totalsForSheet
            );
        }

        return $sheets;
    }
}
