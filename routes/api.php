<?php

use App\Http\Controllers\Api\AturanController;
use App\Http\Controllers\Api\CoinController;
use App\Http\Controllers\Api\DailyLoginController;
use App\Http\Controllers\Api\FasilitasController;
use App\Http\Controllers\Api\RoomsController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\KosController;
use App\Http\Controllers\Api\MisiController;
use App\Http\Controllers\Api\MyTupaiController;
use App\Http\Controllers\Api\PaymentController;
use App\Http\Controllers\Api\RewardController;
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
Route::post('mytupai/{id}/feed', [MyTupaiController::class, 'feed']);
Route::post('mytupai/{id}/sleep', [MyTupaiController::class, 'sleep']);

// Rute untuk Daily Login dan Streak Mission
Route::post('daily-login/claim', [DailyLoginController::class, 'claimLogin']);
Route::get('missions', [MisiController::class, 'index']);
Route::post('missions/claim', [MisiController::class, 'claimReward']);

// Rute untuk Manajemen Koin
Route::get('wallet/balance/{userId}', [CoinController::class, 'getBalance']);
Route::post('rewards/redeem', [RewardController::class, 'redeem']);
