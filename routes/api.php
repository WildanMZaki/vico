<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\VideoController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });

// Authentication route
Route::post('auth/login', [AuthController::class, 'authenticate'])->name('login-api');

Route::post('compress-video', [VideoController::class, 'upload'])->name('upload-video');
Route::get('compress-progress/{id}', [VideoController::class, 'compress_progress'])->name('progress.compression');

// Protected route using JWT middleware
Route::middleware('auth:api')->group(function () {

});
