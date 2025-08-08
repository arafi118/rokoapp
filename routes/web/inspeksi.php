<?php

use App\Http\Controllers\Inspeksi\InspeksiController;
use App\Http\Controllers\Inspeksi\AnggotaController;
use App\Http\Controllers\Inspeksi\LevelController;
use Illuminate\Support\Facades\Route;

Route::prefix('inspeksi')->middleware(['auth', 'inspeksi'])->group(function () {
  //Dashboard route
  Route::get('/', [InspeksiController::class, 'index']);

  //Level routes
  Route::resource('level', LevelController::class);

  //Anggota routes
  // Route::get('anggota/{anggota}/edit/', [AnggotaController::class, 'edit']);
  Route::resource('anggota', AnggotaController::class);
  Route::get('detail/{id}', [AnggotaController::class, 'detail']);
  Route::get('ambil_kab/{kode}', [AnggotaController::class, 'ambil_kab']);
  Route::get('ambil_kec/{kode}', [AnggotaController::class, 'ambil_kec']);
  Route::get('ambil_desa/{kode}', [AnggotaController::class, 'ambil_desa']);
  Route::get('anggota/{id}/detail', [AnggotaController::class, 'detail']);

  //Kelompok routes
});
