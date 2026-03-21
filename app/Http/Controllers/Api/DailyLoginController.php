<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\DailyLoginLog;
use App\Models\StreakLog;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DailyLoginController extends Controller
{

    public function claimLogin(Request $request)
    {
        $request->validate([
            'users_id' => 'required|integer'
        ]);

        $userId = $request->users_id;
        $hariIni = Carbon::today()->toDateString();
        $kemarin = Carbon::yesterday()->toDateString();

        $sudahClaim = DailyLoginLog::where('users_id', $userId)
            ->whereDate('login_date', $hariIni)
            ->exists();

        if ($sudahClaim) {
            return response()->json(['success' => false, 'message' => 'Kamu sudah mengambil hadiah login hari ini! Besok kembali lagi ya.'], 400);
        }

        $streak = StreakLog::firstOrCreate(
            ['users_id' => $userId, 'jenis' => 'login'],
            ['streak_sekarang' => 0, 'top_streak' => 0]
        );

        $loginTerakhir = DailyLoginLog::where('users_id', $userId)
            ->orderBy('login_date', 'desc')
            ->first();

        $isStreakBonus = false;

        if ($loginTerakhir && $loginTerakhir->login_date == $kemarin) {
            $streak->streak_sekarang += 1;
            $isStreakBonus = true;
        } else {
            $streak->streak_sekarang = 1;
        }

        if ($streak->streak_sekarang > $streak->top_streak) {
            $streak->top_streak = $streak->streak_sekarang;
        }
        $streak->save();

        $koinDidapat = 50 + ($streak->streak_sekarang * 10);

        $log = DailyLoginLog::create([
            'login_date'      => $hariIni,
            'koin_didapat'    => $koinDidapat,
            'is_streak_bonus' => $isStreakBonus,
            'users_id'        => $userId
        ]);

        CoinController::updateCoin(
            $userId,
            $koinDidapat,
            'daily_login',
            'Hadiah Login Harian hari ke-' . $streak->streak_sekarang,
            'credit',
            $log->id
        );

        return response()->json([
            'success' => true,
            'message' => 'Daily login berhasil diklaim!',
            'data' => [
                'login_log' => $log,
                'streak'    => $streak
            ]
        ], 200);
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
