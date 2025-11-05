<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\StatisticsController;

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

// Rutas públicas de autenticación
Route::prefix('auth')->group(function () {
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/refresh', [AuthController::class, 'refresh']);
});

// Rutas protegidas con JWT
Route::middleware(['jwt'])->group(function () {
    // CRUD de usuarios
    Route::apiResource('users', UserController::class);

    // Estadísticas
    Route::prefix('statistics')->group(function () {
        Route::get('/daily', [StatisticsController::class, 'daily']);
        Route::get('/weekly', [StatisticsController::class, 'weekly']);
        Route::get('/monthly', [StatisticsController::class, 'monthly']);
    });
});
