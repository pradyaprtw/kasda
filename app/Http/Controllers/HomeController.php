<?php

namespace App\Http\Controllers;

use App\Models\Logs;
use Illuminate\Http\Request;
use Illuminate\View\View;
use App\Models\Sp2d;
use App\Models\SP2D as ModelsSP2D;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\DB;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index(): View
    {
        // === MENGAMBIL LOGIKA DARI REKAP BULANAN WIDGET ===
        $nettoBulanIni = SP2D::whereYear('created_at', now()->year)
            ->whereMonth('created_at', now()->month)
            ->sum('netto');

        $nettoBulanLalu = SP2D::whereYear('created_at', now()->subMonth()->year)
            ->whereMonth('created_at', now()->subMonth()->month)
            ->sum('netto');
            
        $totalPfk = SP2D::whereYear('created_at', now()->year)
            ->whereMonth('created_at', now()->month)
            ->where('jenis_sp2d', 'PFK')
            ->sum('netto');

        $totalPPN = SP2D::whereDate('created_at', now()->toDateString())
            ->sum('ppn');

        $totalPPH21 = SP2D::whereDate('created_at', now()->toDateString())
            ->sum('pph_21');

        $totalPPH22 = SP2D::whereDate('created_at', now()->toDateString())
            ->sum('pph_22');

        $totalPPH23 = SP2D::whereDate('created_at', now()->toDateString())
            ->sum('pph_23');

        $totalPPH4 = SP2D::whereDate('created_at', now()->toDateString())
            ->sum('pph_4');
        
        $nettoHariIni = SP2D::whereDate('created_at', now()->toDateString())
            ->sum('netto');
        
        $nettoSemua = SP2D::sum('netto');

        $brutoSemua = SP2D::whereDate('created_at', now()->toDateString())
            ->sum('brutto');

        $rekapAkhir = $nettoBulanIni - $nettoBulanLalu - $totalPfk;

        $rekapBulanan = [
            'nettoBulanIni' => $nettoBulanIni,
            'nettoBulanLalu' => $nettoBulanLalu,
            'totalPfk' => $totalPfk,
            'rekapAkhir' => $rekapAkhir,
            'nettoHariIni' => $nettoHariIni,
            'nettoSemua' => $nettoSemua,
            'brutoSemua' => $brutoSemua,
            'totalPPN' => $totalPPN,
            'totalPPH21' => $totalPPH21,
            'totalPPH22' => $totalPPH22,
            'totalPPH23' => $totalPPH23,
            'totalPPH4' => $totalPPH4,
        ];

       
    // === QUERY BARU UNTUK STATISTIK PETUGAS ===
        $petugasStats = User::withCount(['logs as total_input_hari_ini' => function ($query) {
                // Hitung log mereka dengan kondisi...
                $query->where('aktivitas', 'like', '%Membuat SP2D baru%')
                      ->whereDate('created_at', today());
            }])
            ->get();

        // Kirim semua data yang sudah diproses ke view 'home.blade.php'
        return view('home', [
            'rekapBulanan' => $rekapBulanan,
            'petugasStats' => $petugasStats,
        ]);
    }
}
