<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\Inspeksi\InspeksiController;
use App\Http\Controllers\Inspeksi\AnggotaController;
use App\Http\Controllers\Inspeksi\LevelController;
use App\Http\Controllers\Inspeksi\RencanaController;
use App\Http\Controllers\Inspeksi\KaryawanController;
use App\Http\Controllers\Inspeksi\GroupController;
use Illuminate\Support\Facades\Route;

Route::prefix('inspeksi')->middleware(['auth', 'inspeksi'])->group(function () {
  //Dashboard route
  Route::get('/', [InspeksiController::class, 'index']);

  //Level routes
  Route::resource('level', LevelController::class);

  //Rencana routes
  Route::resource('rencana', RencanaController::class);

  //Anggota routes
  // Route::get('anggota/{anggota}/edit/', [AnggotaController::class, 'edit']);
  Route::resource('anggota', AnggotaController::class);
  Route::get('detail/{id}', [AnggotaController::class, 'detail']);
  Route::get('ambil_kab/{kode}', [AnggotaController::class, 'ambil_kab']);
  Route::get('ambil_kec/{kode}', [AnggotaController::class, 'ambil_kec']);
  Route::get('ambil_desa/{kode}', [AnggotaController::class, 'ambil_desa']);
  Route::get('anggota/{id}/detail', [AnggotaController::class, 'detail']);

  //Karyawan routes
  Route::resource('karyawan', KaryawanController::class);

  //Group routes
  Route::resource('group', GroupController::class);

  //Logout route

  Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
});
