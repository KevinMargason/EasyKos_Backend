<?php

use App\Http\Controllers\Api\AturanController;
use App\Http\Controllers\Api\FasilitasController;
use App\Http\Controllers\Api\RoomsController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\KosController;
use App\Http\Controllers\Api\MyTupaiController;
use App\Http\Controllers\Api\PaymentController;
use App\Http\Controllers\Api\VoucherController;

// Rute untuk Manajemen Kos
Route::apiResource('kos', KosController::class);
Route::apiResource('rooms', RoomsController::class);
Route::apiResource('fasilitas', FasilitasController::class);
Route::apiResource('aturan', AturanController::class);

// Rute untuk Manajemen Pembayaran dan Voucher
Route::apiResource('payments', PaymentController::class);
Route::apiResource('vouchers', VoucherController::class);
Route::post('payments/{id}/pay', [PaymentController::class, 'pay']);

// Rute untuk Manajemen MyTupai
Route::apiResource('mytupai', MyTupaiController::class);
