<?php

// File: App\Http\Controllers\ExportController.php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SP2D;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\{Sp2dExport, Sp2dRekapExport, Sp2dMingguanExport, Sp2dTahunanExport, Sp2dExportGaji, PajakSp2dExport};
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

    /**
     * Handles the daily report export.
     * Excludes 'LS-Gaji' and 'LS-Gaji PPPK' from the report.
     */
    public function exportExcel(Request $request)
    {
        $request->validate([
            'created_at' => 'required|date|before_or_equal:today',
        ]);

        $date = Carbon::parse($request->created_at);
        $excludeTypes = ['LS-Gaji', 'LS-Gaji PPPK'];

        $totals = TotalsHelper::getTotalsForTwoDays($date, $excludeTypes);

        $todayData = SP2D::with(['penerima', 'instansi'])
            ->whereDate('created_at', $date)
            ->whereNotIn('jenis_sp2d', $excludeTypes)
            ->get();

        if ($todayData->isEmpty()) {
            return back()->with('error', 'Tidak ada data SP2D (non-gaji) untuk tanggal yang dipilih.');
        }

        $fileName = 'rekap-sp2d-tanggal-' . $date->format('d-m-Y') . '.xlsx';

        return Excel::download(new Sp2dExport($todayData, $totals, $request->created_at), $fileName);
    }

    /**
     * Handles the monthly report export.
     * [MODIFIED] Excludes salary data.
     */
    public function exportRekapExcel(Request $request)
    {
        $request->validate([
            'bulan' => 'required|numeric|min:1|max:12',
            'tahun' => 'required|numeric|min:2000',
        ]);

        $bulan = $request->bulan;
        $tahun = $request->tahun;
        $selectedDate = Carbon::create($tahun, $bulan, 1);
        $excludeTypes = ['LS-Gaji', 'LS-Gaji PPPK'];

        $currentMonthData = SP2D::with(['penerima', 'instansi'])
            ->whereYear('created_at', $tahun)
            ->whereMonth('created_at', $bulan)
            ->whereNotIn('jenis_sp2d', $excludeTypes) // Added filter
            ->get();

        if ($currentMonthData->isEmpty()) {
            return back()->with('error', 'Tidak ada data SP2D (non-gaji) untuk bulan dan tahun yang dipilih.');
        }

        $previousMonthDate = $selectedDate->copy()->subMonth();
        $previousMonthData = SP2D::with(['penerima', 'instansi'])
            ->whereYear('created_at', $previousMonthDate->year)
            ->whereMonth('created_at', $previousMonthDate->month)
            ->whereNotIn('jenis_sp2d', $excludeTypes) // Added filter
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

    /**
     * Handles the weekly report export.
     * [MODIFIED] Excludes salary data.
     */
    public function exportMingguan(Request $request)
    {
        $request->validate([
            'minggu' => 'required|integer|min:1|max:5',
            'bulan'  => 'required|integer|min:1|max:12',
            'tahun'  => 'required|integer|min:2000',
        ]);

        [$minggu, $bulan, $tahun] = [$request->minggu, $request->bulan, $request->tahun];
        [$start, $end] = DateHelper::getWeekDateRange($bulan, $tahun, $minggu);
        $excludeTypes = ['LS-Gaji', 'LS-Gaji PPPK'];

        $data = SP2D::with(['penerima', 'instansi'])
            ->whereBetween('created_at', [$start, $end])
            ->whereNotIn('jenis_sp2d', $excludeTypes) // Added filter
            ->get();

        if ($data->isEmpty()) {
            return back()->with('error', 'Tidak ada data SP2D (non-gaji) untuk minggu yang dipilih.');
        }

        $namaBulan = Carbon::createFromDate($tahun, $bulan, 1)->translatedFormat('F');
        $filename = "rekap-sp2d-minggu-ke-{$minggu}-{$namaBulan}-{$tahun}.xlsx";

        return Excel::download(new Sp2dMingguanExport($minggu, $bulan, $tahun), $filename);
    }

    /**
     * Handles the yearly report export.
     * [MODIFIED] Excludes salary data.
     */
    public function exportTahunan(Request $request)
    {
        $request->validate([
            'tahun' => 'required|numeric|min:2000',
        ]);

        $tahun = $request->tahun;
        $excludeTypes = ['LS-Gaji', 'LS-Gaji PPPK'];

        $allData = SP2D::select([
            'id',
            'created_at',
            'nomor_sp2d',
            'jenis_sp2d',
            'brutto',
            'ppn',
            'pph_21',
            'pph_22',
            'pph_23',
            'pph_4',
            'netto',
            'no_bg',
            'id_penerima',
            'id_instansi'
        ])
            ->with([
                'instansi:id,nama_instansi',
                'penerima:id,nama_penerima,no_rek'
            ])
            ->whereYear('created_at', $tahun)
            ->whereNotIn('jenis_sp2d', $excludeTypes) // Added filter
            ->orderBy('created_at')
            ->get();

        if ($allData->isEmpty()) {
            return back()->with('error', 'Tidak ada data SP2D (non-gaji) untuk tahun yang dipilih.');
        }

        $groupedData = $allData->groupBy(function ($item) {
            return $item->created_at->format('m');
        });

        ini_set('memory_limit', '2048M');
        set_time_limit(900);

        gc_enable();

        try {
            \App\Exports\Sp2dRekapExport::clearCache();

            $export = new Sp2dTahunanExport($groupedData, $tahun);

            return Excel::download(
                $export,
                "rekap-sp2d-tahun-{$tahun}.xlsx"
            );
        } finally {
            gc_collect_cycles();
        }
    }

    /**
     * Handles the salary-specific report export.
     * This method REMAINS UNCHANGED to only fetch salary data.
     */
    public function exportGajiExcel(Request $request)
    {
        $request->validate([
            'created_at' => 'required|date_format:Y-m-d',
        ]);

        $date = $request->input('created_at');
        $parsedDate = Carbon::parse($date);
        $jenisSp2d = ['LS-Gaji', 'LS-Gaji PPPK'];

        $dataForToday = Sp2d::with(['penerima', 'instansi'])
            ->whereIn('jenis_sp2d', $jenisSp2d)
            ->whereDate('created_at', $parsedDate)
            ->get();

        $totals = $this->calculateTotals($parsedDate, $jenisSp2d);

        $fileName = 'Laporan SP2D Gaji ' . $parsedDate->format('d-m-Y') . '.xlsx';

        return Excel::download(new Sp2dExportGaji($dataForToday, $totals, $date), $fileName);
    }

    private function calculateTotals(Carbon $date, array $jenisSp2d): array
    {
        $todayTotals = $this->getTotalsForDay($date, $jenisSp2d);
        $yesterday = $date->copy()->subDay();
        $yesterdayTotals = $this->getTotalsForDay($yesterday, $jenisSp2d);

        $combinedTotals = [];
        foreach (array_keys($todayTotals) as $key) {
            $combinedTotals[$key] = ($todayTotals[$key] ?? 0) + ($yesterdayTotals[$key] ?? 0);
        }

        return [
            'today'     => $todayTotals,
            'yesterday' => $yesterdayTotals,
            'combined'  => $combinedTotals,
        ];
    }

    private function getTotalsForDay(Carbon $date, array $jenisSp2d): array
    {
        $totals = Sp2d::whereIn('jenis_sp2d', $jenisSp2d)
            ->whereDate('created_at', $date)
            ->selectRaw(
                "SUM(brutto) as brutto, " .
                    "SUM(pph_21) as pph_21, " .
                    "SUM(iuran_wajib) as iuran_wajib, " .
                    "SUM(iuran_wajib_2) as iuran_wajib_2, " .
                    "SUM(netto) as netto"
            )->first();

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

        return [
            'brutto'          => (float)$totals->brutto,
            'pph_21'          => (float)$totals->pph_21,
            'iuran_wajib'     => (float)$totals->iuran_wajib,
            'iuran_wajib_2'   => (float)$totals->iuran_wajib_2,
            'jumlah_potongan' => (float)$jumlah_potongan,
            'netto'           => (float)$totals->netto,
        ];
    }

    public function exportPajakExcel(Request $request)
    {
        // 1. Validasi input tanggal
        $request->validate([
            'created_at' => 'required|date_format:Y-m-d',
        ]);

        $date = Carbon::parse($request->input('created_at'));

        // 2. Ambil semua data SP2D pada tanggal yang dipilih
        $dataForReport = SP2D::with(['penerima', 'instansi'])
            ->whereDate('created_at', $date)
            // Filter whereNotIn dihapus untuk mengambil semua jenis SP2D
            ->get();

        // 3. Jika tidak ada data, kembalikan dengan pesan error
        if ($dataForReport->isEmpty()) {
            return back()->with('error', 'Tidak ada data SP2D untuk tanggal yang dipilih.');
        }

        // 4. Hitung total menggunakan TotalsHelper untuk semua data pada hari itu
        $totals = TotalsHelper::getTotalsForTwoDays($date); // Parameter excludeTypes dihapus

        // 5. Buat nama file dinamis
        $fileName = 'Laporan Pajak SP2D ' . $date->format('d-m-Y') . '.xlsx';

        // 6. Panggil kelas PajakSp2dExport dan unduh file
        return Excel::download(new PajakSp2dExport($dataForReport, $totals, $request->input('created_at')), $fileName);
    }
}
