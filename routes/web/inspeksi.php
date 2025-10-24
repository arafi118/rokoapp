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
use App\Http\Controllers\PelaporanController;
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
  Route::get('/karyawan/list-group', [KaryawanController::class, 'getgroup']);
  Route::get('/karyawan/list-anggota', [KaryawanController::class, 'getanggota']);
  Route::get('/karyawan/list-meja', [KaryawanController::class, 'getmeja']);
  Route::get('/karyawan/list-level', [KaryawanController::class, 'getlevel']);
  Route::resource('karyawan', KaryawanController::class);

  //Tempat Kerja routes
  Route::get('tempat-kerja', [TempatKerjaController::class, 'index']);
  Route::put('tempat-kerja/update-banyak', [TempatKerjaController::class, 'updateBanyak']);
  Route::put('tempat-kerja/{id}', [TempatKerjaController::class, 'update']);

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

Route::get('/cron/update-banyak-karyawan', [TempatKerjaController::class, 'updateBanyakKaryawan'])
  ->withoutMiddleware(['auth', 'inspeksi'])
  ->name('cron.updateBanyakKaryawan');
