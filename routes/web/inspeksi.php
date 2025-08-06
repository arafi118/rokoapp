<?php

use App\Http\Controllers\Inspeksi\InspeksiController;
use App\Http\Controllers\Inspeksi\AnggotaController;
use Illuminate\Support\Facades\Route;

Route::prefix('inspeksi')->middleware(['auth', 'inspeksi'])->group(function () {
  //Dashboard route
  Route::get('/', [InspeksiController::class, 'index']);

  //Anggota routes
  Route::get('anggota', [AnggotaController::class, 'index']);
  Route::get('anggota/create', [AnggotaController::class, 'create']);

  //Kelompok routes
});
