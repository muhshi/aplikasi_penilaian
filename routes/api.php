<?php

use App\Http\Controllers\Api\MasterUserController;
use Illuminate\Http\Request;
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

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

/*
|--------------------------------------------------------------------------
| Master Data API (Machine-to-Machine)
|--------------------------------------------------------------------------
| Digunakan oleh aplikasi lain untuk sinkronisasi data dari aplikasi penilaian.
| Autentikasi: Sanctum Token dengan nama 'master-data-api'.
*/
Route::middleware(['auth:sanctum', 'master.token', 'throttle:60,1'])
    ->prefix('master')
    ->name('api.master.')
    ->group(function () {
        Route::get('/users', [MasterUserController::class, 'index'])->name('users.index');
        Route::get('/users/{user}', [MasterUserController::class, 'show'])->name('users.show');
    });
