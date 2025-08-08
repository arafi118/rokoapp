<?php

use App\Http\Controllers\Mandor\AnggotaController;
use App\Http\Controllers\Mandor\MandorController;
use Illuminate\Support\Facades\Route;

Route::prefix('mandor')->middleware(['auth', 'mandor'])->group(function () {
  Route::get('/', [MandorController::class, 'index']);

  Route::get('/anggota', [AnggotaController::class, 'index']);
});
