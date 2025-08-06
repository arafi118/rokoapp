<?php

use App\Http\Controllers\Anggota\AnggotaController;
use Illuminate\Support\Facades\Route;

Route::prefix('anggota')->group(function () {
  Route::get('/', [AnggotaController::class, 'index']);
});
