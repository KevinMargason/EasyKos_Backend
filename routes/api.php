<?php

use App\Http\Controllers\Api\AturanController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ChatController;
use App\Http\Controllers\Api\CoinController;
use App\Http\Controllers\Api\DailyLoginController;
use App\Http\Controllers\Api\FasilitasController;
use App\Http\Controllers\Api\RoomsController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Api\KosController;
use App\Http\Controllers\Api\MisiController;
use App\Http\Controllers\Api\MyTupaiController;
use App\Http\Controllers\Api\PaymentController;
use App\Http\Controllers\Api\PMController;
use App\Http\Controllers\Api\ProfileController;
use App\Http\Controllers\Api\RewardController;
use App\Http\Controllers\Api\VoucherController;

// Rute untuk Autentikasi
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// Rute untuk Manajemen Kos
Route::apiResource('kos', KosController::class);
Route::apiResource('rooms', RoomsController::class);
Route::apiResource('fasilitas', FasilitasController::class);
Route::apiResource('aturan', AturanController::class);

// Rute untuk Manajemen Pembayaran dan Voucher
Route::apiResource('payments', PaymentController::class);
Route::apiResource('vouchers', VoucherController::class);
Route::post('payments/{id}/pay', [PaymentController::class, 'pay']);
Route::post('payments/info', [PaymentController::class, 'seePayment']);

// Health check route
Route::get('health', function () {
    try {
        DB::connection()->getPdo();
        return response()->json(['success' => true, 'message' => 'API is healthy', 'database' => 'connected']);
    } catch (\Throwable $e) {
        return response()->json(['success' => false, 'message' => 'API health check failed', 'error' => $e->getMessage()], 503);
    }
});

// Rute untuk Manajemen MyTupai
Route::apiResource('mytupai', MyTupaiController::class);
Route::post('mytupai/{id}/feed', [MyTupaiController::class, 'feed']);
Route::post('mytupai/{id}/sleep', [MyTupaiController::class, 'sleep']);
Route::get('mytupai/check', [MyTupaiController::class, 'checkMyTupai']);
Route::get('mytupai/check/{userId}', [MyTupaiController::class, 'checkMyTupai']);

// Rute untuk Daily Login dan Streak Mission
Route::post('daily-login/claim', [DailyLoginController::class, 'claimLogin']);
Route::get('/daily-login/status', [DailyLoginController::class, 'getStreakStatus']);
Route::get('missions', [MisiController::class, 'index']);
Route::post('missions/claim', [MisiController::class, 'claimReward']);

// Rute untuk Manajemen Koin
Route::get('wallet/balance/{userId}', [CoinController::class, 'getBalance']);
Route::post('rewards/redeem', [RewardController::class, 'redeem']);

// Rute untuk Profil Pengguna
Route::get('/profile', [ProfileController::class, 'show']);
Route::put('/profile', [ProfileController::class, 'update']);
Route::put('/users/profile', [ProfileController::class, 'update']);

// Rute untuk Manajemen Metode Pembayaran
Route::get('/payment-methods', [PMController::class, 'index']);
Route::post('/payment-methods', [PMController::class, 'store']);
Route::put('/payment-methods/{id}', [PMController::class, 'update']);
Route::delete('/payment-methods/{id}', [PMController::class, 'destroy']);

// Rute untuk Manajemen Aturan Kos
Route::get('/kos/current', [KosController::class, 'currentKos']);
Route::get('/residents', [KosController::class, 'allResidents']);
Route::get('/kos/{kosId}/residents', [KosController::class, 'kosResidents']);
Route::get('/kos/{kosId}/rooms', [RoomsController::class, 'getRoomsByKos']);

// Rute untuk Chat
Route::get('/chats', [ChatController::class, 'index']);
Route::get('/chats/{threadId}', [ChatController::class, 'show']);
Route::get('/chats/{threadId}/messages', [ChatController::class, 'messages']);
Route::post('/messages', [ChatController::class, 'sendMessage']);
