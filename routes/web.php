<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\CompressController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\VideoController;
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

Route::get('/', [HomeController::class, 'index'])->name('root');
Route::get('/home', [HomeController::class, 'index'])->name('home');

Route::get('/video-compression', [VideoController::class, 'form'])->name('upload.page');
Route::get('/download-results/{id}', [VideoController::class, 'download_page'])->name('download.page');
Route::get('/download/{video}', [VideoController::class, 'download'])->name('download.video');

Route::get('/compress', [CompressController::class, 'index'])->name('compress_page');
Route::post('/compress', [CompressController::class, 'compress'])->name('compress');
