<?php

use App\Http\Controllers\Inspeksi\InspeksiController;
use Illuminate\Support\Facades\Route;

Route::prefix('inspeksi')->group(function () {
  Route::get('/', [InspeksiController::class, 'index']);
});
