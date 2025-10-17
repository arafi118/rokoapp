<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\Inspeksi\AbsensiController;
use App\Http\Controllers\Inspeksi\InspeksiController;
use App\Http\Controllers\Inspeksi\AnggotaController;
use App\Http\Controllers\Inspeksi\MejaController;
use App\Http\Controllers\Inspeksi\TempatKerjaController;
use App\Http\Controllers\Inspeksi\LevelController;
use App\Http\Controllers\Inspeksi\RencanaController;
use App\Http\Controllers\Inspeksi\KaryawanController;
use App\Http\Controllers\Inspeksi\GroupController;
use App\Http\Controllers\Inspeksi\LaporanController;
use App\Http\Controllers\Inspeksi\PelaporanController;
use Illuminate\Support\Facades\Route;

Route::prefix('inspeksi')->middleware(['auth', 'inspeksi'])->group(function () {
  //Dashboard route
  Route::get('/', [InspeksiController::class, 'index']);

  //Level routes
  Route::resource('level', LevelController::class);

  //Rencana routes
  Route::resource('rencana', RencanaController::class);

  //Anggota routes
  Route::resource('anggota', AnggotaController::class);
  Route::get('detail/{id}', [AnggotaController::class, 'detail']);
  Route::get('ambil_kab/{kode}', [AnggotaController::class, 'ambil_kab']);
  Route::get('ambil_kec/{kode}', [AnggotaController::class, 'ambil_kec']);
  Route::get('ambil_desa/{kode}', [AnggotaController::class, 'ambil_desa']);
  Route::get('anggota/{id}/detail', [AnggotaController::class, 'detail']);

  //Karyawan routes
  Route::resource('karyawan', KaryawanController::class);

  //Tempat Kerja routes
  Route::resource('tempat-kerja', TempatKerjaController::class);

  //Group routes
  Route::get('group/list', [GroupController::class, 'list']);
  Route::resource('group', GroupController::class);

  Route::get('/absensi-karyawan', [AbsensiController::class, 'index']);
  Route::post('/absensi-karyawan', [AbsensiController::class, 'store']);

  Route::get('/laporan-absensi', [AbsensiController::class, 'laporan']);
  Route::post('/laporan-absensi', [AbsensiController::class, 'cetak']);

  //Pelaporan
  Route::get('/laporan', [PelaporanController::class, 'index']);
  Route::get('/pelaporan/preview', [PelaporanController::class, 'preview']);
  Route::get('/pelaporan/sub_laporan/{file}', [PelaporanController::class, 'subLaporan']);
  
  //Meja routes
  Route::resource('meja', MejaController::class);

  //Logout route

  Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
});
