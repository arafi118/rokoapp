<?php

use App\Http\Controllers\Anggota\AnggotController;
use Illuminate\Support\Facades\Route;

Route::prefix('anggota')->group(function () {
  Route::get('/', [AnggotController::class, 'index']);
});
