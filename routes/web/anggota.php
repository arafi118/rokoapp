<?php

use App\Http\Controllers\Anggota\AnggotaController;
use Illuminate\Support\Facades\Route;

Route::prefix('anggota')->middleware(['auth', 'anggota'])->group(function () {
  Route::get('/', [AnggotaController::class, 'index']);
});
