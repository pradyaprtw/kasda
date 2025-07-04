<?php

namespace App\Observers;

use App\Models\Sp2d;
use App\Models\Logs;
use Illuminate\Support\Facades\Auth;

class Sp2dObserver
{
    /**
     * Handle the Sp2d "creating" event.
     *
     * This method is called before a Sp2d instance is created.
     * It sets the id_user to the currently authenticated user's ID.
     */
    public function creating(Sp2d $sp2d): void
    {
        // Otomatis set id_user saat data dibuat
        if (Auth::check()) {
            $sp2d->id_user = Auth::id();
        }
    }
    
    /**
     * Handle the Sp2d "created" event.
     */
    public function created(Sp2d $sp2d): void
    {
        Logs::create([
            'id_sp2d' => $sp2d->id,
            'id_user' => Auth::id(),
            'aktivitas' => 'Membuat SP2D baru: ' . $sp2d->nomor_sp2d,
        ]);
    }



    /**
     * Handle the Sp2d "updated" event.
     *
     * This method is called after a Sp2d instance is updated.
     * It logs the activity based on whether the verification status has changed or not.
     */
    // public function updated(Sp2d $sp2d): void
    // {
    //     // Cek jika yang berubah adalah status verifikasi
    //     if (true) { // Ganti 'true' dengan kondisi yang sesuai jika diperlukan
    //         Logs::create([
    //             'id_sp2d' => $sp2d->id,
    //             'id_user' => Auth::id(),
    //             'aktivitas' => 'Mengubah data SP2D: ' . $sp2d->nomor_sp2d,
    //         ]);
    //     }
    // }
}
