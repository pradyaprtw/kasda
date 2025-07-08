<?php

namespace App\Http\Controllers;

use App\Exports\Sp2dExport;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class DisplayController extends Controller
{
    public function index(){
        return view('display-sp2d');
    }

    public function exportExcel(Request $request){
        //validasi input created_at
        $request->validate([
            'created_at' => 'required|date',
        ]);

        // Ambil created_at dari request
        $created_at = $request->input('created_at');

        // Konversi string tanggal menjadi objek Carbon (yang sesuai dengan type-hint Date)
        $tanggal_object = \Carbon\Carbon::parse($created_at);

        //format created_at untuk nama file
        $tanggalFormatted = \Carbon\Carbon::parse($created_at)->format('d-m-Y');
        $fileName = "Laporan SP2D_$tanggalFormatted.xlsx";

        // panggil  class export dengan created_at yang diberikan dan unduh file
        return Excel::download(new Sp2dExport($created_at), $fileName);
    }
}
