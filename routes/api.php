<?php

use App\Http\Controllers\Api\AturanController;
use App\Http\Controllers\Api\FasilitasController;
use App\Http\Controllers\Api\RoomsController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\KosController;

// Rute untuk Manajemen Kos
Route::apiResource('kos', KosController::class);
Route::apiResource('rooms', RoomsController::class);
Route::apiResource('fasilitas', FasilitasController::class);
Route::apiResource('aturan', AturanController::class);
