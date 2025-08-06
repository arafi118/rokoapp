<?php

use App\Http\Controllers\Anggota\AnggotaController;
use Illuminate\Support\Facades\Route;


Route::prefix('anggota')->middleware(['anggota'])->group(function () {
    Route::get('/', [AnggotaController::class, 'create'])->name('anggota.dashboard');
    Route::post('/', [AnggotaController::class, 'store'])->name('anggota.store');
    Route::get('/produksi', [AnggotaController::class, 'produksi'])->name('anggota.produksi'); 
});
