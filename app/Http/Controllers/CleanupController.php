<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SP2D;
use Illuminate\Support\Facades\DB;

class CleanupController extends Controller
{
    public function konfirmasiHapus(Request $request)
    {
        $flagId = $request->flag_id;

        if ($request->action === 'yes') {
            // Hapus data sebelum 1 Juli 2024
            $deletedBefore = \Carbon\Carbon::create(2024, 7, 1)->startOfMonth();
            $deleted = SP2D::where('created_at', '<', $deletedBefore)->delete();

            // Log
            DB::table('data_cleanup_logs')->insert([
                'deleted_before' => $deletedBefore,
                'deleted_by' => auth()->user()->name,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Update flag
            DB::table('data_cleanup_flags')->where('id', $flagId)->update(['flag_hapus' => false]);

            return back()->with('success', "Sebanyak $deleted data berhasil dihapus oleh : " . auth()->user()->name);
        }

        // Tombol 'ingatkan besok' → jangan ubah flag, biar tetap muncul
        return back()->with('info', 'Penghapusan ditunda. Anda akan diingatkan kembali besok.');
    }
}
