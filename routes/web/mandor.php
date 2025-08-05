<?php

use App\Http\Controllers\Mandor\MandorController;
use Illuminate\Support\Facades\Route;

Route::prefix('mandor')->group(function () {
  Route::get('/', [MandorController::class, 'index']);
});
