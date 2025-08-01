<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\{
    HomeController,
    DisplayController,
    InstansiController,
    PenerimaController,
    ExportController,
    CleanupController
};

Auth::routes(['login' => false, 'register' => false, 'reset' => false]);

// Guest (Login & Register)
Route::middleware('guest')->group(function () {
    Route::get('/register', \App\Livewire\Auth\Register::class)->name('register');
    Route::get('/', \App\Livewire\Auth\Login::class)->name('login');

});

// Authenticated Users
Route::middleware('auth')->group(function () {
    Route::get('/home', [HomeController::class, 'index'])->name('home');
    Route::get('/sp2d', [DisplayController::class, 'index'])->name('sp2d');
    Route::get('/penerima', [PenerimaController::class, 'index'])->name('penerima');
    Route::get('/instansi', [InstansiController::class, 'index'])->name('instansi');
    Route::post('/konfirmasi-hapus', [CleanupController::class, 'konfirmasiHapus'])->name('konfirmasi.hapus');

    // Export routes
    Route::prefix('export')->name('export.')->group(function () {
        Route::get('/', [ExportController::class, 'index'])->name('index');
        Route::get('/harian', [ExportController::class, 'exportExcel'])->name('harian');
        Route::get('/gaji', [ExportController::class, 'exportGajiExcel'])->name('gaji');
        Route::get('/bulanan', [ExportController::class, 'exportRekapExcel'])->name('bulanan');
        Route::get('/mingguan', [ExportController::class, 'exportMingguan'])->name('mingguan');
        Route::get('/tahunan', [ExportController::class, 'exportTahunan'])->name('tahunan');
        Route::get('/pajak', [ExportController::class, 'exportPajakExcel'])->name('pajak');
    });
});
