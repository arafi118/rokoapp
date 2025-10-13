<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\Mandor\AbsensiController;
use App\Http\Controllers\Mandor\KaryawanController;
use App\Http\Controllers\Mandor\MandorController;
use Illuminate\Support\Facades\Route;

Route::prefix('mandor')->middleware(['auth', 'mandor'])->group(function () {
  Route::get('/', [MandorController::class, 'index']);

  Route::get('/karyawan', [KaryawanController::class, 'index']);
  Route::get('/absensi-karyawan', [AbsensiController::class, 'index']);
  Route::post('/absensi-karyawan', [AbsensiController::class, 'store']);

  Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
});
