<?php

use App\Http\Controllers\Inspeksi\InspeksiController;
use App\Http\Controllers\Inspeksi\AnggotaController;
use Illuminate\Support\Facades\Route;

Route::prefix('inspeksi')->middleware(['auth', 'inspeksi'])->group(function () {
  //Dashboard route
  Route::get('/', [InspeksiController::class, 'index']);

  //Anggota routes
  // Route::get('anggota/{anggota}/edit/', [AnggotaController::class, 'edit']);
  Route::resource('anggota', AnggotaController::class);
  Route::get('detail/{id}', [AnggotaController::class, 'detail']);
  Route::get('ambil_kab/{kode}', [AnggotaController::class, 'ambil_kab']);
  Route::get('ambil_kec/{kode}', [AnggotaController::class, 'ambil_kec']);
  Route::get('ambil_desa/{kode}', [AnggotaController::class, 'ambil_desa']);

  //Kelompok routes
});
