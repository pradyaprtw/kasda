<?php

// File: App\Http\Controllers\ExportController.php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SP2D;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\{Sp2dExport, Sp2dRekapExport, Sp2dMingguanExport, Sp2dTahunanExport};
use Carbon\Carbon;
use DateTime;
use App\Helpers\Sp2dHelper;
use App\Helpers\DateHelper;
use App\Helpers\TotalsHelper;

class ExportController extends Controller
{
    public function index()
    {
        return view('display-export');
    }

    public function exportExcel(Request $request)
    {
        $request->validate([
            'created_at' => 'required|date|before_or_equal:today',
        ]);

        $date = Carbon::parse($request->created_at);
        $totals = TotalsHelper::getTotalsForTwoDays($date);

        $todayData = SP2D::with(['penerima', 'instansi'])
            ->whereDate('created_at', $date)
            ->get();

        if ($todayData->isEmpty()) {
            return back()->with('error', 'Tidak ada data SP2D untuk tanggal yang dipilih.');
        }

        $fileName = 'rekap-sp2d-tanggal-' . $date->format('d-m-Y') . '.xlsx';

        return Excel::download(new Sp2dExport($todayData, $totals, $request->created_at), $fileName);
    }

    public function exportRekapExcel(Request $request)
    {
        $request->validate([
            'bulan' => 'required|numeric|min:1|max:12',
            'tahun' => 'required|numeric|min:2000',
        ]);

        $bulan = $request->bulan;
        $tahun = $request->tahun;
        $selectedDate = Carbon::create($tahun, $bulan, 1);

        $currentMonthData = SP2D::with(['penerima', 'instansi'])
            ->whereYear('created_at', $tahun)
            ->whereMonth('created_at', $bulan)
            ->get();

        if ($currentMonthData->isEmpty()) {
            return back()->with('error', 'Tidak ada data SP2D untuk bulan dan tahun yang dipilih.');
        }

        $previousMonthDate = $selectedDate->copy()->subMonth();
        $previousMonthData = SP2D::with(['penerima', 'instansi'])
            ->whereYear('created_at', $previousMonthDate->year)
            ->whereMonth('created_at', $previousMonthDate->month)
            ->get();

        $cumulativeData = $currentMonthData->merge($previousMonthData);

        $totals = [
            'current'    => Sp2dHelper::calculateTotals($currentMonthData),
            'previous'   => Sp2dHelper::calculateTotals($previousMonthData),
            'cumulative' => Sp2dHelper::calculateTotals($cumulativeData),
        ];

        $monthName = Carbon::createFromDate($tahun, $bulan)->translatedFormat('F');
        $fileName = 'rekap-sp2d-' . $monthName . '-' . $tahun . '.xlsx';

        ini_set('memory_limit', '1024M');
        set_time_limit(600);
        
        return Excel::download(new Sp2dRekapExport($currentMonthData, $bulan, $tahun, $totals), $fileName);
    }

    public function exportMingguan(Request $request)
    {
        $request->validate([
            'minggu' => 'required|integer|min:1|max:5',
            'bulan'  => 'required|integer|min:1|max:12',
            'tahun'  => 'required|integer|min:2000',
        ]);

        [$minggu, $bulan, $tahun] = [$request->minggu, $request->bulan, $request->tahun];
        [$start, $end] = DateHelper::getWeekDateRange($bulan, $tahun, $minggu);

        $data = SP2D::with(['penerima', 'instansi'])
            ->whereBetween('created_at', [$start, $end])
            ->get();

        if ($data->isEmpty()) {
            return back()->with('error', 'Tidak ada data SP2D untuk minggu yang dipilih.');
        }

        $namaBulan = Carbon::createFromDate($tahun, $bulan, 1)->translatedFormat('F');
        $filename = "rekap-sp2d-minggu-ke-{$minggu}-{$namaBulan}-{$tahun}.xlsx";

        return Excel::download(new Sp2dMingguanExport($minggu, $bulan, $tahun), $filename);
    }

public function exportTahunan(Request $request)
    {
        $request->validate([
            'tahun' => 'required|numeric|min:2000',
        ]);

        $tahun = $request->tahun;

        // [OPTIMASI 1] Gunakan select only untuk field yang diperlukan dan eager loading
        $allData = SP2D::select([
                'id', 'created_at', 'tanggal_sp2d', 'nomor_sp2d', 'jenis_sp2d', 
                'brutto', 'ppn', 'pph_21', 'pph_22', 'pph_23', 'pph_4', 'netto', 
                'no_bg', 'id_penerima', 'id_instansi'
            ])
            ->with([
                'instansi:id,nama_instansi', 
                'penerima:id,nama_penerima,no_rek'
            ])
            ->whereYear('created_at', $tahun)
            ->orderBy('created_at') // [OPTIMASI] Add ordering untuk konsistensi
            ->get();

        if ($allData->isEmpty()) {
            return back()->with('error', 'Tidak ada data SP2D untuk tahun yang dipilih.');
        }

        // [OPTIMASI 2] Gunakan groupBy yang lebih efisien
        $groupedData = $allData->groupBy(function ($item) {
            return $item->created_at->format('m');
        });

        // [OPTIMASI 3] Set memory dan time limit yang lebih optimal
        ini_set('memory_limit', '2048M'); // Increased memory
        set_time_limit(900); // 15 minutes
        
        // [OPTIMASI 4] Enable garbage collection
        gc_enable();

        try {
            // [OPTIMASI 5] Clear any previous cache
            \App\Exports\Sp2dRekapExport::clearCache();
            
            $export = new Sp2dTahunanExport($groupedData, $tahun);
            
            return Excel::download(
                $export,
                "rekap-sp2d-tahun-{$tahun}.xlsx"
            );
        } finally {
            // [OPTIMASI 6] Force garbage collection after export
            gc_collect_cycles();
        }
    }
}
