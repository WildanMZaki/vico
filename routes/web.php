<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\CompressController;
use App\Http\Controllers\HomeController;
use Illuminate\Support\Facades\Auth;

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


Auth::routes();

Route::get('/home', [HomeController::class, 'index'])->name('home');
Route::get('/compress', [CompressController::class, 'index'])->name('compress_page');
Route::post('/compress', [CompressController::class, 'compress'])->name('compress');
Route::get('/download/{video}', [CompressController::class, 'download'])->name('download');
