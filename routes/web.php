<?php

use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', [AuthController::class, 'index'])->middleware('guest');
Route::post('/auth', [AuthController::class, 'auth'])->middleware('guest');

Route::get('/link', function () {
    // Storage link
    symlink(base_path('storage'), public_path('storage'));
});

foreach (glob(base_path('routes/web/*.php')) as $file) {
    require $file;
}
