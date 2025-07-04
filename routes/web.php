<?php

use App\Http\Controllers\DisplayController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\InstansiController;
use App\Http\Controllers\PenerimaController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;


/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// Route::get('/', function () {
//     return view('display-sp2d');
// });

Auth::routes(['login' => false, 'register' => false]);

Route::middleware('guest')->group(function () {
    Route::get('/register', \App\Livewire\Auth\Register::class)->name('register');
    Route::get('/', \App\Livewire\Auth\Login::class)->name('login');
});

Route::middleware('auth')->group(function () {
    Route::get('/home', [HomeController::class, 'index'])->name('home');
    Route::get('/sp2d', [DisplayController::class, 'index'])->name('sp2d');
    Route::get('/penerima', [PenerimaController::class, 'index'])->name('penerima');
    Route::get('/instansi', [InstansiController::class, 'index'])->name('instansi');
});

// Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
